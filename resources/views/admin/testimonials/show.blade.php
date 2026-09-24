@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Testimonial Details <i class="feather icon-film"></i></h4>
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
                                            <p><h5>Name: </h5>{!! $data->name !!}</p>
                                        </div>
                                        <div class="col-6">
                                            <p><h5>Image: </h5>
                                            <td><img src="{{$data->image_url}}" height="50px;"></td>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <p><h5>Description: </h5>{!! $data->description !!}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <a href="{!! route('admin.testimonial.index') !!}" style="margin-left: -1rem;
                                                    margin-top: 2rem;"
                                       class="btn btn-primary waves-effect waves-light">
                                        <i class="ti ti-check me-1"></i>Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
