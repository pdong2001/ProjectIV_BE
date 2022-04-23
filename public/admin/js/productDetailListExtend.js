const route = 'product-details';
extendController = ($scope, $http) => {
    // $scope.name = '';
    // $scope.visible = true;
    $scope.fields = [
        {hidden: false, field: 'product.name', display: 'Tên sản phẩm', default: '', type: 'text', readonly: true},
        {hidden: false, field: 'option_name', display: 'Tên CT', default: '', type: 'text'},
        {hidden: false, field: 'option_value', display: 'Giá trị', default: '', type: 'text'},
        {hidden: false, field: 'remaining_quantity', display: 'Số lượng còn', default: '', type: 'text'},
        {hidden: false, field: 'default_image.file_path', display: 'Ảnh', default: '', type: 'file', readonly: false},
        {hidden: false, field: 'unit', display: 'ĐVT', default: '', type: 'text'},
        {hidden: false, field: 'total_quantity', display: 'Tổng số', default: 0, type: 'text'},
        {hidden: false, field: 'visible', display: 'Hiển thị', default: true, type: 'checkbox'},
        // {hidden: false, field: 'visible', display: 'Hiển thị', default: true, type: 'checkbox'},
        // {hidden: false, field: 'visible', display: 'Hiển thị', default: true, type: 'checkbox'},
    ];
    $scope.id = 0;
    $scope.item = {};
    $scope.selectedProduct = {};

    for (let field of $scope.fields.filter(v => !v.readonly)) {
        $scope.item[field.field] = field.default;
    }

    $scope.showEdit = (item) => {
        const file = document.getElementById('default_image.file_path');
        if (file != null) value = '';
        $scope.id = item.id;
        $scope.selectedProduct = $scope.products.find(v => v.id == item.product.id)??{};
        for (let field of $scope.fields.filter(v => !v.readonly)) {
            $scope.item[field.field] = item[field.field];
        }
        $scope.editting = true;
        $scope.formVisible = true;
        $scope.deleting = false;
    }

    $scope.showAddNew = () => {
        for (let field of $scope.fields.filter(v => !v.readonly)) {
            $scope.item[field.field] = field.default;
        }
        const file = document.getElementById('default_image.file_path');
        if (file != null) value = '';
        $scope.editting = false;
        $scope.deleting = false;
    }
    $scope.save = () => {
        const fileE = document.getElementById('default_image.file_path');
        let file;
        if (fileE != null) file = fileE.files[0];
        let item = {};
        for (let field of $scope.fields.filter(v => !v.readonly)) {
            item[field.field] = $scope.item[field.field];
        }
        let index = document.getElementById('select')?.selectedIndex??-1;
        $scope.selectedProduct = $scope.products[index];
        item.product_id = $scope.selectedProduct.id;
        if (file != undefined && file != null)
        {
            $scope.upLoadFile(file, '/api/upload')
                .then(res => {
                    if (res.data.status == true)
                    {
                        item.default_image = res.data.data.id;
                    }
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
            item.product_id = $scope.selectedProduct.id;
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
    $scope.products = [];
    $http.get('/api/admin/products?page=1&limit=1000')
        .then(res => {
            if (res.data.status == true) {
                $scope.products = res.data.data;
            }
        });
    $scope.change = () => {
        console.log($scope.file);
    }
}
