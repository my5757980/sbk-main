@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Media File Details <i class="feather icon-film"></i></h4>
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
                                		<p><h5>Alternate Text: </h5>{!! $data->alt_text !!}</p>
                                	</div>
                                	<div class="col-6">
                                		<p><h5>Caption: </h5>{!! $data->caption !!}</p>
                                	</div>
                                	<div class="col-6">
                                		<p><h5>Media: </h5>
                                			@if ($data->filename != '' && file_exists(uploadsDir('front') . $data->filename))
                                				<img src="{!! asset(uploadsDir('front') . $data->filename) !!}" height="150" width="150">
                                			@endif
                                		</p>
                                	</div>
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