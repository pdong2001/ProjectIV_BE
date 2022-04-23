<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>@yield('title')</title>
        <link href="/admin/css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script src="/angular.min.js"></script>
        <script src="/admin/js/scripts.js"></script>
{{--        <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>--}}
{{--        <link href="//cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">--}}
{{--        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">--}}
        <script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>
    </head>
    <body>
      @include('admin/layout/admin-navbar')
        <div id="layoutSidenav">
          @include('admin/layout/admin-sidebar')
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">

                    <h1 class="mt-4">@yield('page-title')</h1>
                    @yield('main-content')
                    </div>
                </main>
               @include('admin/layout/admin-footer')
            </div>
        </div>
        <script src="/admin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    @yield('scripts')
    </body>
</html>
