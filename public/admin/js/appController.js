'use strict';

var extendController;
const app = angular.module('myApp', []);
app.controller('myController', function ($scope, $http) {
    $scope.data = [];
    $scope.totalRecords = 0;
    $scope.page = 1;
    $scope.limit = 10;
    $scope.column = '';
    $scope.sort = 'asc';
    $scope.formVisible = false;
    $scope.editting = false;
    $scope.searchValue = '';
    $scope.deleting = false;
    $scope.extendQuerys = '';
    if (extendController) {
        extendController($scope, $http);
    }
    $scope.getList = () => {
        const url = `/api/admin/${route}?page=${$scope.page}&limit=${$scope.limit}&column=${$scope.column}&sort=${$scope.sort}&search=${$scope.searchValue}&${$scope.extendQuerys}`;
        $http.get(url)
            .then(res => {
                if (res.data.status == true) {
                    $scope.data = res.data.data;
                    $scope.totalRecords = res.data.meta.total;
                }
            });
    }
    $scope.upLoadFile = function(file, uploadUrl) {
        var fd = new FormData();
        fd.append('file', file);

        return $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        });
    }

    $scope.getById = (id) => {
        const url = `/api/admin/${route}/${id}`;
        $http.get(url)
            .then(res => {
                if (res.data.status == true) {
                    const index = $scope.data.findIndex(v => v.id == id);
                    if (index > 0) {
                        $scope.data[index] = res.data.data;
                    }
                }
            });
    }

    $scope.update = (id, item) => {
        const url = `/api/admin/${route}/${id}`;
        $http.patch(url, item)
            .then(res => {
                if (res.data.status == true) {
                    $scope.getList();
                }
            });
    }

    $scope.create = (item) => {
        const url = `/api/admin/${route}`;
        $http.post(url, item)
            .then(res => {
                if (res.data.status == true) {
                    $scope.getList();
                }
            });
    }
    $scope.delete = (id) => {
        const url = `/api/admin/${route}/${id}`;
        $http.delete(url)
            .then(res => {
                if (res.data.status == true) {
                    $scope.getList();
                }
            });
    }
    $scope.loadPage = (page) => {
        if (page => 1 && page < $scope.totalRecords / $scope.limit) {

            $scope.page = page;
            $scope.getList();
        }
    }

    $scope.order = (column) => {
        if ($scope.column != column) {
            $scope.column = column;
        } else {
            $scope.sort = $scope.sort == 'asc' ? 'desc' : 'asc';
        }
        $scope.getList();
    }
    $scope.getList();

    $scope.search = () => {
        $scope.getList();
    }
});

app.filter('page', function () {
    return function (input, page, total, perPage, limit = 5) {
        total = parseInt(total);
        page = parseInt(page);
        perPage = parseInt(perPage);
        limit = parseInt(limit);
        page -= 2;
        if (page < 1) page = 1;
        for (let i = 0; i < limit && (i + page - 1) * perPage <= total; i++) {
            input.push((i + page));
        }
        return input;
    };
});

app.filter('editable', function () {
    return function (input) {
        input = input.filter(v => v.readonly == undefined || v.readonly == false);
        return input;
    }
})

app.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;

            element.bind('change', function () {
                scope.$apply(function () {
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]);

app.filter('visible', function () {
    return function (input) {
        input = input.filter(v => v.hidden == undefined || v.hidden == false);
        return input;
    }
})

app.filter('value', function () {
    return function (input, field) {
        let value = input;
        const fields = field.split('.');
        for (let f of fields) {
            value = value[f];
            if (value == undefined) {
                return '';
            }
        }
        return value;
    }
})

app.config(function ($sceProvider) {
    // Completely disable SCE.  For demonstration purposes only!
    // Do not use in new projects or libraries.
    $sceProvider.enabled(false);
});
