<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>{!! $siteSettings->site_title !!} - Login</title>
    <link rel="apple-touch-icon" href="{!! asset('assets/admin/app-assets/images/ico/apple-icon-120.png') !!}">
    <link rel="shortcut icon" type="image/x-icon" href="{!! $siteSettings->favicon_path !!}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/vendors.min.css') !!}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/bootstrap.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/bootstrap-extended.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/colors.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/components.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/themes/dark-layout.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/themes/semi-dark-layout.css') !!}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/core/menu/menu-types/vertical-menu.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/core/colors/palette-gradient.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/pages/authentication.css') !!}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/assets/css/style.css') !!}">
    <!-- END: Custom CSS-->

    <!-- BEGIN: Toastr CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/extensions/toastr.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/plugins/extensions/toastr.css') !!}">
    <!-- END: Toastr CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body  class="vertical-layout vertical-menu-modern 1-column  navbar-floating footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        @yield('content')
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="{!! asset('assets/admin/app-assets/vendors/js/vendors.min.js') !!}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{!! asset('assets/admin/app-assets/js/core/app-menu.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/js/core/app.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/js/scripts/components.js') !!}"></script>
    <!-- END: Theme JS-->

    <!-- Toastr -->
    <script src="{!! asset('assets/admin/app-assets/vendors/js/extensions/toastr.min.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/js/scripts/extensions/toastr.js') !!}"></script>
    <!-- END: Page JS-->
    @include('admin.partials.errors')
</body>
<!-- END: Body-->

</html>
