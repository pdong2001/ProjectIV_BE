@extends('admin/layout/admin-layout')
@section('title')
    Admin - Sản phẩm
@endsection
@section('page-title')
    Sản phẩm
@endsection
@section('main-content')
    <div ng-app="myApp" ng-controller="myController">
        <div class="mb-3 border-1 rounded-1 d-flex justify-content-between">
            <button ng-click="showAddNew()" type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#staticBackdrop">
                Thêm
            </button>
            <div>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Tìm kiếm" ng-model="searchValue"
                           aria-label="Tìm kiếm" aria-describedby="button-addon2">
                    <button ng-click="search()" class="btn btn-outline-secondary" type="button" id="button-addon2">Tìm
                    </button>
                </div>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">STT</th>
                <th style="cursor: pointer;" ng-repeat="f in fields | visible" ng-click="order(f.field)" scope="col">
                    @{{ f.display
                    }}
                </th>
                <th style="cursor: default;"></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="item in data">
                <th scope="row">@{{ $index + 1 }}</th>
                <td ng-repeat="f in fields | visible">
                    <span ng-if="f.type != 'file' && f.type != 'editor'"> @{{ item | value: f.field }}</span>
                    <div ng-bind-html="item[f.field]" ng-if="f.type == 'editor'" class="ql-contaienr">
                    </div>
                    <img height="100" ng-if="f.type == 'file'" src="/api/files/@{{ item | value: f.field }}"/>
                </td>
                <td>
                    <a href="/admin/product-detail/@{{ item.id }}" class="btn btn-success m-1">Xem</a>
                    <button ng-click="showEdit(item)" type="button" class="btn btn-info m-1" data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop">
                        Sửa
                    </button>
                    <button ng-click="showDelete(item.id)" type="button" class="btn btn-danger m-1"
                            data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop">
                        Xóa
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li ng-class="page > 1? 'page-item': 'page-item disabled'"><a class="page-link"
                                                                              ng-click="loadPage(page - 1)"
                                                                              style="cursor: pointer;">Trước</a>
                </li>
                <li ng-class="i == page ? 'page-item active' : 'page-item'"
                    ng-repeat="i in [] | page: page : totalRecords: limit"><a class="page-link" style="cursor: pointer;"
                                                                              ng-click="loadPage(i)">@{{ i }}</a></li>
                <li ng-class="page < totalRecords / limit? 'page-item': 'page-item disabled'"><a class="page-link"
                                                                                                 style="cursor: pointer;"
                                                                                                 ng-click="loadPage(page + 1)"
                    >Sau</a>
                </li>
            </ul>
        </nav>
        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel"> @{{ deleting ?'Xác nhận':'Thông tin loại sản
                            phẩm' }} </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div ng-if="deleting">
                            Bạn có chắc chắn muốn xóa sản phẩm?
                        </div>
                        <div ng-if="!deleting" class="container-fluid">
                            <div class="row">
                                <div class="mb-3 col-md-6 col-12">
                                    <label for="select" class="form-label fw-bold">Sản phẩm</label>
                                    <select id="select" data-ng-options="o.name for o in products" class="form-select"
                                            data-ng-model="selectedProduct"></select>

                                </div>
                                <div ng-repeat="f in fields | editable" ng-class="f.type != 'editor' ? 'col-md-6' : ''"
                                     class="form-group mb-3 col-12">
                                    <label for="@{{ f.field }}" class="form-label fw-bold">@{{ f.display }}</label>
                                    <input ng-if="f.type != 'editor' && f.type != 'file'" id="@{{ f.field }}"
                                           class="@{{ f.type != 'checkbox' ? 'form-control' : 'form-check-input'}}"
                                           type="@{{f.type}}" ng-model="item[f.field]"/>
                                    <input ng-if="f.type == 'file'" id="@{{ f.field }}"
                                           class="form-control"
                                           type="@{{f.type}}" file-model="file"/>
                                    <div ng-if="f.type == 'editor'" class="editor">
                                    </div>
                                </div>
                                @{{ file }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" aria-label="Close" class="btn btn-secondary"
                                data-bs-dismiss="modal">Hủy
                        </button>
                        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-primary"
                                ng-click="save()">@{{ deleting ?'Xác nhận':'Lưu' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="/admin/js/productDetailListExtend.js"></script>
    <script src="/admin/js/appController.js"></script>
@endsection
