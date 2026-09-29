<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="loading">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="{!! $siteSettings->site_title !!} - Admin">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>{!! $siteSettings->site_title !!} - Admin</title>
    <link rel="apple-touch-icon" href="{!! asset('assets/admin/app-assets/images/ico/apple-icon-120.png') !!}">
    <link rel="shortcut icon" type="image/x-icon" href="{!! $siteSettings->favicon_path !!}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/vendors.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/charts/apexcharts.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/extensions/tether-theme-arrows.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/extensions/tether.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/extensions/shepherd-theme-default.css') !!}">

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
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/pages/dashboard-analytics.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/pages/card-analytics.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/plugins/tour/tour.css') !!}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/assets/css/style.css') !!}">
    <!-- END: Custom CSS-->

    <!-- BEGIN: Toastr CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/extensions/toastr.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/css/plugins/extensions/toastr.css') !!}">
    <!-- END: Toastr CSS-->

    <!-- BEGIN: DataTables CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/tables/datatable/datatables.min.css') !!}">
    <!-- END: Toastr CSS-->

    <!-- BEGIN: SweetAlert CSS-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/admin/app-assets/vendors/css/extensions/sweetalert2.min.css') !!}">
    <!-- END: Toastr CSS-->

    {{-- Select2 --}}
    <link rel="stylesheet" href="{{ asset('assets/admin/app-assets/vendors/css/select2/select2@4.1.0-rc.0.min.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <style>
        .tippy-popper {
            display: none;
        }
        .feather {
            font-size: 10px;
        }
    </style>
    @yield('css')
    @stack('css')
</head>
<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
    @include('admin.layouts.partials.header')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
    <form id="logout-form" action="{{ route('admin.auth.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix blue-grey lighten-2 mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; {{ date("Y") }}<a class="text-bold-800 grey darken-2" href="#" target="_blank">{{ config('app.name', 'Laravel') }},</a>{!! $siteSettings->footer_sentence !!}</span></span>
            <button class="btn btn-primary btn-icon scroll-top" type="button"><i class="feather icon-arrow-up"></i></button>
        </p>
    </footer>
    <!-- END: Footer-->
    <!-- BEGIN: Vendor JS-->
    <script src="{!! asset('assets/admin/app-assets/vendors/js/vendors.min.js') !!}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
{{--    <script src="{!! asset('assets/admin/app-assets/vendors/js/charts/apexcharts.min.js') !!}"></script>--}}
{{--    <script src="{!! asset('assets/admin/app-assets/vendors/js/extensions/tether.min.js') !!}"></script>--}}
{{--    <script src="{!! asset('assets/admin/app-assets/vendors/js/extensions/shepherd.min.js') !!}"></script>--}}
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{!! asset('assets/admin/app-assets/js/core/app-menu.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/js/core/app.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/js/scripts/components.js') !!}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
{{--    <script src="{!! asset('assets/admin/app-assets/js/scripts/pages/dashboard-analytics.js') !!}"></script>--}}
    <!-- Toastr -->
    <script src="{!! asset('assets/admin/app-assets/vendors/js/extensions/toastr.min.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/js/scripts/extensions/toastr.js') !!}"></script>
    <!-- DataTables -->
{{--    <script src="{!! asset('assets/admin/app-assets/vendors/js/tables/datatable/pdfmake.min.js') !!}"></script>--}}
{{--    <script src="{!! asset('assets/admin/app-assets/vendors/js/tables/datatable/vfs_fonts.js') !!}"></script>--}}
    <script src="{!! asset('assets/admin/app-assets/vendors/js/tables/datatable/datatables.min.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') !!}"></script>
{{--    <script src="{!! asset('assets/admin/app-assets/vendors/js/tables/datatable/buttons.html5.min.js') !!}"></script>--}}
{{--    <script src="{!! asset('assets/admin/app-assets/vendors/js/tables/datatable/buttons.print.min.js') !!}"></script>--}}
{{--    <script src="{!! asset('assets/admin/app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') !!}"></script>--}}
    <script src="{!! asset('assets/admin/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/js/scripts/datatables/datatable.js') !!}"></script>
     <!-- SweetAlert -->
    <script src="{!! asset('assets/admin/app-assets/js/scripts/extensions/sweet-alerts.js') !!}"></script>
    <script src="{!! asset('assets/admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') !!}"></script>
    <script>
        function logout() {
            document.getElementById("logout-form").submit();
        }

        function deleteConfirmation(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-danger ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    document.getElementById("deleteForm" + id + "").submit();
                } else {
                    Swal.fire({
                        type: "success",
                        title: 'Cancelled!',
                        text: 'Your data is safe :)',
                        confirmButtonClass: 'btn btn-success',
                    })
                }
            });
        }
    </script>
    @include('admin.partials.errors')
    @yield('footer-js')
    <script src="{!! asset('assets/admin/app-assets/js/tinymce/tinymce.min.js') !!}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/axios/1.6.0/axios.min.js" integrity="sha512-WrdC3CE9vf1nBf58JHepuWT4x24uTacky9fuzw2g/3L9JkihgwZ6Cfv+JGTtNyosOhEmttMtEZ6H3qJWfI7gIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/admin/app-assets/vendors/js/select2/select2@4.1.0-rc.0.min.js') }}"></script>

    <script>

        $(document).ready(function () {
            $('.select2').select2();
        });

        tinymce.init({
            selector: 'textarea.editor-tinymce',
            height: 300,
            menubar: true,
            menubar: true,
            entity_encoding: 'raw',
            resize: 'vertical',
            plugins: 'code autolink advlist visualchars link image media table charmap hr pagebreak nonbreaking  anchor insertdatetime lists textcolor wordcount imagetools  contextmenu  visualblocks',
            extended_valid_elements: 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
            toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link image table | alignleft aligncenter alignright alignjustify  | numlist bullist indent  |  visualblocks code',
            convert_urls: false,
            image_caption: true,
            image_advtab: true,
            image_title: true,
            placeholder: '',
            contextmenu: 'link image inserttable | cell row column deletetable',
            automatic_uploads: true,
            block_unsupported_drop: false,
            file_picker_types: 'file image media',
            relative_urls: false,
            remove_script_host: false,
            cleanup_on_startup: false,
            trim_span_elements: false,
            verify_html: false,
            cleanup: false,
            valid_elements: "@[id|class|style|title|dir<ltr?rtl|lang|xml::lang|onclick|ondblclick|" + "onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|" + "onkeydown|onkeyup],a[rel|rev|charset|hreflang|tabindex|accesskey|type|" + "name|href|target|title|class|onfocus|onblur],strong/b,em/i,strike,u," + "#p,-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|" + "src|border|alt=|title|hspace|vspace|width|height|align],-sub,-sup," + "-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|" + "height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|" + "height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot," + "#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor" + "|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,-div," + "-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face" + "|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite]," + "object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width" + "|height|src|*],script[src|type],map[name],area[shape|coords|href|alt|target],bdo," + "button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|" + "valign|width],dfn,fieldset,form[action|accept|accept-charset|enctype|method]," + "input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value]," + "kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value]," + "q[cite],samp,select[disabled|multiple|name|size],small," + "textarea[cols|rows|disabled|name|readonly],tt,var,big",
            valid_children: '*[*]',
            allow_html_in_named_anchor: true,
            allow_unsafe_link_target: true,
            automatic_uploads: true,
            file_picker_callback: function(callback, value, meta) {
                if (meta.filetype === 'image') {
                    // Open a file picker or use your own file upload logic here
                    openImageUploader(callback);
                }
            },
        });

        function openImageUploader(callback) {
            // Display a file input for image selection
            var input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function () {
                var file = this.files[0];
                if (file) {
                    // Simulate an AJAX request to upload the image to the server
                    uploadImageToServer(file, function (imageUrl) {
                        callback(imageUrl);
                    });
                }
            };
            input.click();
        }
        function uploadImageToServer(file, successCallback) {
            // Implement your server-side image upload logic here (e.g., with Axios or jQuery.ajax)
            // After uploading, call the successCallback with the image URL
            var formData = new FormData();
            formData.append('image', file);

            // Replace the URL and headers with your server configuration
            var serverUrl = '{!! route('admin.upload-image-from-editor') !!}'; // Update to the actual upload route
            axios.post(serverUrl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }).then(function (response) {
                successCallback(response.data.location);
            }).catch(function (error) {
                // Handle upload errors
            });
        }

        function slugify(text)
        {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }

        $('#image').change(function () {
            readURL(this);
        });

        $('#removeImage').click(function () {
            $('#image').val(''); // Clear the file input
            $('#imagePreview').attr('src', '').hide(); // Clear and hide the preview
            $('#removeImage').hide(); // Hide the remove button
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                    $('#removeImage').show(); // Show the remove button
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                $('#imagePreview').attr('src', '').hide();
                $('#removeImage').hide();
            }
        }
    </script>
    {{-- The live chat's desk, for the admin-panel staff who answer customers. --}}
    @include('chat.widget', ['chatDeskOnly' => true])
</body>
</html>
