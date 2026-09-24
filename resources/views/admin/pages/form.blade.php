@extends('admin.layouts.app')

@section('css')
    <style type="text/css">
        .tox-notifications-container, .tox-statusbar__branding {
            display: none !important;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <section id="number-tabs">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{!! $form['heading'] !!}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form method="POST" action="{{ $form['action'] }}" class="number-tab-steps wizard-circle" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="_method" value="{!! $form["method"] !!}">
{{--                                <fieldset>--}}
{{--                                    <div class="row">--}}
{{--                                        <div class="col-sm-6">--}}
{{--                                            <div class="form-group">--}}
{{--                                                <label for="heading">Type</label>--}}
{{--                                                <select name="type" id="type" class="form-control">--}}
{{--                                                    @foreach(pageTypes() as $val => $label)--}}
{{--                                                        <option  value="{!! $val !!}" {!! matchChecked($val, $data->type ?? '') !!}>{!! $label !!}</option>--}}
{{--                                                    @endforeach--}}
{{--                                                </select>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </fieldset>--}}
                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="heading">Title</label>
                                                <input type="text" id="page_title" name="page_title" maxlength="190" value="{{ old('page_title', $data->page_title ?? '') }}" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="slug">Slug *</label>
                                                <input type="text" id="slug" name="slug" maxlength="190" value="{{ old('slug', $data->slug ?? '') }}"  class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="sub_heading">Subtitle</label>
                                                <input type="text" id="page_subtitle" name="page_subtitle" maxlength="190" value="{{ old('page_subtitle', $data->page_subtitle ?? '') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="page_title">Meta Title</label>
                                                <input type="text" id="meta_title" name="meta_title" maxlength="100" value="{{ old('meta_title', $data->meta_title ?? '') }}" class="form-control">
                                                <small class="emp_post text-truncate" style="font-size: 11px;">
                                                    This is what will appear in the first line when this page shows up
                                                    in the search results.It should be less than or equal to
                                                    <span class="badge bg-success text-white">
                                                100 character
                                            </span>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="meta_description">Meta Description</label>
                                                <input type="text" id="meta_description" name="meta_description" maxlength="190" value="{{ old('meta_description', $data->meta_description ?? '') }}" class="form-control">
                                                <small class="emp_post text-truncate" style="font-size: 11px;">
                                                    This description will show up on search engines.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="meta_keywords">Seo Keywords</label>
                                                <select class="js-example-tokenizer form-control" name="meta_keywords[]" multiple="">
                                                    @foreach($data->meta_keywords ?? [] as $keyword)
                                                        <option value="{!! $keyword !!}" selected>{!! $keyword !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="content">Content *</label>
                                                <textarea name="content" class="form-control editor-tinymce" maxlength="65000" rows="5" id="editor">{{ old('content', $data->content ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <div class="row">
{{--                                        <div class="col-sm-6">--}}
{{--                                            <div class="form-group">--}}
{{--                                                <label for="jobtitle">Image</label>--}}
{{--                                                <input type="file" name="image" id="image" class="form-control">--}}
{{--                                            </div>--}}
{{--                                            <div class="form-group d-flex">--}}
{{--                                                <div class="single-image position-relative">--}}
{{--                                                    <button id="removeImage" type="button" class="btn btn-danger position-absolute " style="display: none;border-radius:50%;padding: 5px; top: 3px; right:3px"><i class="fa fa-close"></i></button>--}}
{{--                                                    <img id="imagePreview" src="#" alt="Image Preview" style="display: none; max-width: 100px;">--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="form-group">--}}
{{--                                                <input type="hidden" name="previous_image" value="{{ $data->image }}" />--}}
{{--                                                @if ($data->image != '' && file_exists(uploadsDir('front'). $data->image))--}}
{{--                                                    <div class="avatar mr-1 avatar-xl">--}}
{{--                                                        <img src="{!! asset(uploadsDir('front'). $data->image) !!}" alt="Page Image" title="Page Image" class="img-responsive" />--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="content">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    @foreach(pageStatuses() as $val => $label)
                                                        <option value="{!! $val !!}" {!! matchSelected($val, $data->status ?? '') !!}>{!! $label !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Save Changes</button>
                                    <a href="{!! $form['cancel_url'] !!}" class="btn btn-danger mr-1 mb-1 waves-effect waves-light" style="color: white">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer-js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $("#page_title").on('blur', function (){
            var value = $( this ).val();
            $('#slug').val(slugify(value));
        });

        $(".js-example-tokenizer").select2({
            tags: true,
            tokenSeparators: [',', ' '],
        });
    </script>
@endsection
