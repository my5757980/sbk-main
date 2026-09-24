@extends('admin.layouts.app')

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
                            <form method="POST" action="{!! $form['action'] !!}" class="number-tab-steps wizard-circle" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                @csrf
                                <input type="hidden" name="_method" value="{!! $form['method'] !!}">

                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="title">Title *</label>
                                                <input type="text" id="title" name="title" value="{{ old('title', $data->title) }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="menu_title">Header Menu Title</label>
                                                <input type="text" id="menu_title" name="menu_title" value="{{ old('menu_title', $data->menu_title) }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="slug">Slug *</label>
                                                <input type="text" id="slug" name="slug" value="{{ old('slug', $data->slug) }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="sort_order">Sort Order</label>
                                                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $data->sort_order ?? 0) }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    @foreach(pageStatuses() as $val => $label)
                                                        <option value="{!! $val !!}" {!! matchSelected($val, old('status', $data->status ?? 'published')) !!}>{!! $label !!}</option>
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
                                                <label for="excerpt">Short Intro / Hero Text</label>
                                                <textarea name="excerpt" rows="3" maxlength="1000" class="form-control">{{ old('excerpt', $data->excerpt) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="primary_cta_label">Primary CTA Label</label>
                                                <input type="text" id="primary_cta_label" name="primary_cta_label" value="{{ old('primary_cta_label', $data->primary_cta_label) }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="secondary_cta_label">Secondary CTA Label</label>
                                                <input type="text" id="secondary_cta_label" name="secondary_cta_label" value="{{ old('secondary_cta_label', $data->secondary_cta_label) }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="content">Page Content</label>
                                                <textarea name="content" rows="10" id="content" class="form-control editor-tinymce">{{ old('content', $data->content ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="image">Hero Image</label>
                                                <input type="file" name="image" id="image" class="form-control">
                                            </div>
                                            <div class="form-group d-flex">
                                                <div class="single-image position-relative">
                                                    <button id="removeImage" type="button" class="btn btn-danger position-absolute" style="display: none;border-radius:50%;padding: 5px; top: 3px; right:3px"><i class="fa fa-close"></i></button>
                                                    <img id="imagePreview" src="#" alt="Image Preview" style="display: none; max-width: 100px;">
                                                </div>
                                            </div>
                                            <input type="hidden" name="previous_image" value="{{ $data->image }}">
                                            @if ($data->image != '' && file_exists(uploadsDir('front'). $data->image))
                                                <div class="avatar mr-1 avatar-xl">
                                                    <img src="{!! asset(uploadsDir('front'). $data->image) !!}" alt="Service Image" class="img-responsive">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="meta_title">Meta Title</label>
                                                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $data->meta_title) }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="meta_description">Meta Description</label>
                                                <textarea name="meta_description" rows="4" maxlength="1000" class="form-control">{{ old('meta_description', $data->meta_description) }}</textarea>
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
    <script>
        $('#title').on('blur', function () {
            if (!$('#slug').val()) {
                $('#slug').val(slugify($(this).val()));
            }
            if (!$('#menu_title').val()) {
                $('#menu_title').val($(this).val());
            }
            if (!$('#meta_title').val()) {
                $('#meta_title').val($(this).val() + ' | Vertex INT');
            }
        });
    </script>
@endsection
