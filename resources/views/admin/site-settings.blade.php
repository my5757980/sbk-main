@extends('admin.layouts.app')

@section('content')
    <form method="POST" action="{{ route('admin.site-settings.update', $records->id) }}" class="number-tab-steps wizard-circle" enctype="multipart/form-data">
        @csrf
        @method('PUT')
<section id="number-tabs">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Header</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">

                        <fieldset>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jobtitle">Logo</label>
                                        <input type="file" name="logo" class="form-control">
                                        <input type="hidden" name="previous_logo" value="{{ $records->logo }}" />
                                        @if ($records->logo != '' && file_exists(uploadsDir('front') . $records->logo))
                                            <div class="avatar mr-1 avatar-xl">
                                                <img src="{!! asset(uploadsDir('front'). $records->logo) !!}" alt="" title="Logo" class="img-responsive" />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jobtitle">Favicon</label>
                                        <input type="file" name="favicon" class="form-control">
                                        <input type="hidden" name="previous_favicon" value="{{ $records->favicon }}" />
                                        @if ($records->favicon != '' && file_exists(uploadsDir('front') . $records->favicon))
                                            <div class="avatar mr-1 avatar-xl">
                                                <img src="{!! asset(uploadsDir('front'). $records->favicon) !!}" alt="" title="Logo" class="img-responsive" />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Site Settings</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">

                            <fieldset>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="site_title">Site Title *</label>
                                            <input type="text" name="site_title" maxlength="190" value="{{ old('site_title', $records->site_title) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="contact_email">Contact Email *</label>
                                            <input type="email" name="contact_email" maxlength="190" value="{{ old('contact_email', $records->contact_email) }}"  class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="contact_phone">Contact Phone</label>
                                            <input type="number" name="contact_phone" maxlength="190" value="{{ old('contact_phone', $records->contact_phone) }}"  class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" name="address" maxlength="190" value="{{ old('address', $records->address) }}"  class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="copyright">Copyright Line</label>
                                            <textarea name="copyright" maxlength="65000" rows="5" class="form-control">{{ old('footer_sentence', $records->copyright) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="footer_sentence">Footer Sentence *</label>
                                            <textarea name="footer_sentence" maxlength="65000" rows="5" class="form-control">{{ old('footer_sentence', $records->footer_sentence) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="footer_scripts">Footer Script</label>
                                            <textarea name="footer_scripts" maxlength="65000" rows="5" class="form-control">{{ old('footer_scripts', $records->footer_scripts) }}</textarea>
                                            <small>
                                                Add custom scripts you might want to be loaded in the footer of your website. You need to have <b>SCRIPT</b> or <b>STYLE</b> tag around scripts.</small>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{--    <div class="row">--}}
{{--        <div class="col-12">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header">--}}
{{--                    <h4 class="card-title">Social Links</h4>--}}
{{--                </div>--}}
{{--                <div class="card-content">--}}
{{--                    <div class="card-body">--}}
{{--                        <fieldset>--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-sm-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="facebook">FaceBook URL</label>--}}
{{--                                        <input type="text" name="facebook" maxlength="190" value="{{ old('facebook', $records->facebook) }}" class="form-control">--}}
{{--                                    </div>--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="twitter">Twitter URL</label>--}}
{{--                                        <input type="text" name="twitter" maxlength="190" value="{{ old('twitter', $records->twitter) }}" class="form-control">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-sm-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="pinterest">Linkedin URL</label>--}}
{{--                                        <input type="text" name="linkedin" maxlength="190" value="{{ old('linkedin', $records->linkedin) }}" class="form-control">--}}
{{--                                    </div>--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="pinterest">Youtube URL</label>--}}
{{--                                        <input type="text" name="youtube" maxlength="190" value="{{ old('youtube', $records->youtube) }}" class="form-control">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-sm-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="pinterest">Intagram URL</label>--}}
{{--                                        <input type="text" name="instagram" maxlength="190" value="{{ old('instagram', $records->instagram) }}" class="form-control">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-sm-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="pinterest">Whatsapp URL</label>--}}
{{--                                        <input type="text" name="whatsapp" maxlength="190" value="{{ old('whatsapp', $records->whatsapp) }}" class="form-control">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </fieldset>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    </form>

@endsection
