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
                                <input type="hidden" name="id" value="{{$data->id}}" >
                                @csrf
                                <input type="hidden" name="_method" value="{!! $form['method'] !!}">
                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="name">Name *</label>
                                                <input type="text" id="name" name="name" value="{{ old('name', $data->name) }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea type="text" name="description"  class="form-control editor-tinymce">{{ old('description', $data->description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="jobtitle">Image</label>
                                                <input type="file" name="image" id="image" class="form-control">
                                            </div>
                                            <div class="form-group d-flex">
                                                <div class="single-image position-relative">
                                                    <button id="removeImage" type="button" class="btn btn-danger position-absolute " style="display: none;border-radius:50%;padding: 5px; top: 3px; right:3px"><i class="fa fa-close"></i></button>
                                                    <img id="imagePreview" src="#" alt="Image Preview" style="display: none; max-width: 100px;">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="previous_image" value="{{ $data->image }}" />
                                                @if ($data->image != '' && file_exists(uploadsDir('front'). $data->image))
                                                    <div class="avatar mr-1 avatar-xl">
                                                        <img src="{!! asset(uploadsDir('front'). $data->image) !!}" alt="Page Image" title="Page Image" class="img-responsive" />
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
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
