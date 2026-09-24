@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Administrator's Details <i class="feather icon-user"></i></h4>
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
                                		<p><h5>First Name: </h5>{!! $data->first_name !!}</p>
                                	</div>
                                	<div class="col-6">
                                		<p><h5>Last Name: </h5>{!! $data->last_name !!}</p>
                                	</div>
                                    <div class="col-6">
                                        <p><h5>Contact Number: </h5>{!! $data->phone !!}</p>
                                    </div>
                                    <div class="col-6">
                                        <p><h5>Email: </h5>{!! $data->email !!}</p>
                                    </div>
                                    @if ($data->image != '' && file_exists(uploadsDir('admin') . $data->image))
                                    	<div class="col-6">
                                    		<p><h5>Media: </h5>
                                    			<img src="{!! asset(uploadsDir('admin') . $data->image) !!}" height="150" width="150">
                                    		</p>
                                    	</div>
                                    @endif
                                    <div class="col-6">
                                        <p><h5>Is Active: </h5>{!! ($data->is_active > 0) ? 'Yes' : 'No' !!}</p>
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