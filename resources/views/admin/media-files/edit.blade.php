@extends('admin.layouts.app')

@section('content')
<section id="number-tabs">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Media <i class="feather icon-film"></i></h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.media-files.update', $data->id) }}" class="number-tab-steps wizard-circle" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <fieldset>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="page_title">Alternate Text *</label>
                                            <input type="text" name="alt_text" value="{{ old('page_title', $data->alt_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="slug">File *</label>
                                            <input type="file" name="file" class="form-control">
                                            <input type="hidden" name="previous_image" value="{!! $data->filename !!}" />
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="meta_title">Caption *</label>
                                            <input type="text" name="caption" maxlength="190" value="{{ old('meta_title', $data->caption) }}"  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="meta_title">Existing File</label>
                                            <br>
                                            @if ($data->filename && file_exists(uploadsDir('front') . $data->filename))
                                                <img src="{!! asset(uploadsDir('front') . $data->filename) !!}" height="150" width="150">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection