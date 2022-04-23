const route = 'products';
extendController = ($scope, $http) => {
    // $scope.name = '';
    // $scope.visible = true;
    $scope.fields = [
        {hidden: false, field: 'name', display: 'Tên sản phẩm', default: '', type: 'text'},
        {hidden: false, field: 'code', display: 'Mã', default: '', type: 'text'},
        {hidden: false, field: 'category.name', display: 'Tên loại', default: '', type: 'text', readonly : true},
        {hidden: false, field: 'quantity', display: 'Số lượng', default: '', type: 'text'},
        {hidden: false, field: 'default_image.file_path', display: 'Ảnh', default: '', type: 'file'},
        {hidden: false, field: 'visible', display: 'Hiển thị', default: true, type: 'checkbox'},
        {hidden: true, field: 'description', display: 'Mô tả', default: '', type: 'editor'},
        // {hidden: true, field: 'visible', display: 'Hiển thị', default: true, type:'checkbox'},
    ];
    $scope.id = 0;
    $scope.item = {};
    $scope.selectedCategory = {};

    for (let field of $scope.fields.filter(v => !v.readonly)) {
        $scope.item[field.field] = field.default;
    }

    $scope.showEdit = (item) => {
        const file = document.getElementById('default_image.file_path');
        if (file != null) file.value = '';
        $scope.id = item.id;
        $scope.selectedCategory = $scope.categories.find(v => v.id == item.category_id)??{};
        for (let field of $scope.fields.filter(v => !v.readonly)) {
            $scope.item[field.field] = item[field.field];
        }
        $scope.editting = true;
        editor.setData(item.description??'');
        $scope.formVisible = true;
        $scope.deleting = false;
    }

    $scope.showAddNew = () => {
        for (let field of $scope.fields.filter(v => !v.readonly)) {
            $scope.item[field.field] = field.default;
        }
        const file = document.getElementById('default_image.file_path');
        if (file != null) value = '';
        editor.setData('');
        $scope.editting = false;
        $scope.deleting = false;
    }
    $scope.save = () => {
        const fileE = document.getElementById('default_image.file_path');
        let file;
        if (fileE != null) 
            file = fileE.files[0];
        let item = {};
        for (let field of $scope.fields.filter(v => !v.readonly)) {
            item[field.field] = $scope.item[field.field];
        }
        let index = document.getElementById('selectCate')?.selectedIndex??-1;
        if (index > 0)
            $scope.selectedCategory = $scope.categories[index];
        item.category_id = $scope.selectedCategory.id;
        if (file != undefined && file != null)
        {
            $scope.upLoadFile(file, '/api/upload')
                .then(res => {
                    if (res.data.status == true)
                    {
                        item.default_image = res.data.data.id;
                    }
                    item.description = editor.getData();
                    item.category_id = $scope.selectedCategory.id;
                    if ($scope.editting) {
                        $scope.update($scope.id, item);
                    } else if ($scope.deleting) {
                        $scope.delete($scope.id);
                    } else {
                        $scope.create(item)
                    }
                });
        }
        else
        {
            item.description = editor.getData();
            item.category_id = $scope.selectedCategory.id;
            if ($scope.editting) {
                $scope.update($scope.id, item);
            } else if ($scope.deleting) {
                $scope.delete($scope.id);
            } else {
                $scope.create(item)
            }
        }
    }
    $scope.showDelete = (id) => {
        $scope.id = id;
        $scope.deleting = true;
    }
    $scope.categories = [];
    $http.get('/api/admin/categories?page=1&limit=1000')
        .then(res => {
            if (res.data.status == true) {
                $scope.categories = res.data.data;
            }
        });
    $scope.change = () => {
        console.log($scope.file);
    }
}
