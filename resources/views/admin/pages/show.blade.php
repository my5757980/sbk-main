@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Page Details <i class="feather icon-globe"></i></h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                        <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                        <li><a data-action="reload"><i class="feather icon-rotate-cw"></i></a></li>
                        <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <div class="row" style="width: 100%;">
                                	<div class="col-6">
                                		<p><h5>Page Title: </h5>{!! $data->page_title !!}</p>
                                	</div>
                                	<div class="col-6">
                                		<p><h5>Slug: </h5>{!! $data->slug !!}</p>
                                	</div>
                                	<div class="col-6">
                                		<p><h5>Contents: </h5>{!! $data->content !!}</p>
                                	</div>
                                	<div class="col-6">
                                		<p><h5>Meta Title: </h5>{!! $data->meta_title !!}</p>
                                	</div>
{{--                                	<div class="col-6">--}}
{{--                                		<p><h5>Meta Keywords: </h5>{!! $data->meta_keywords !!}</p>--}}
{{--                                	</div>--}}
                                	<div class="col-6">
                                		<p><h5>Meta Description: </h5>{!! $data->meta_description !!}</p>
                                	</div>
{{--                                	<div class="col-6">--}}
{{--                                		<p><h5>Is System Page? </h5>{!! ($data->is_system_page == 1) ? 'Yes' : 'No' !!}</p>--}}
{{--                                	</div>--}}
                                	<div class="col-6">
                                		<p><h5>Created At: </h5>{!! $data->created_at->diffForHumans() !!}</p>
                                	</div>
                                	<div class="col-6">
                                		<p><h5>Updated At: </h5>{!! $data->updated_at->diffForHumans() !!}</p>
                                	</div>
{{--                                	<div class="col-6">--}}
{{--                                		<p><h5>Media File: </h5>--}}
{{--                                			@if ($data->filename != '' && file_exists(uploadsDir('front') . $data->filename))--}}
{{--                                				<img src="{!! asset(uploadsDir('front') . $data->filename) !!}" height="200" width="200">--}}
{{--                                			@endif--}}
{{--                                		</p>--}}
{{--                                	</div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
