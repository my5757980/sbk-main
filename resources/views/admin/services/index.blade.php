@extends('admin.layouts.app')

@section('content')
    <section id="column-selectors">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Services</h4>
                        @adminCan('service.create')
                            <span><a href="{{ route('admin.service.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a></span>
                        @endadminCan
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table table-striped dataex-html5-selectors">
                                    <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Slug</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($data as $result)
                                        <tr>
                                            <td>{!! Str::limit($result->title, 45, '...') !!}</td>
                                            <td>{!! $result->slug !!}</td>
                                            <td>{!! $result->sort_order !!}</td>
                                            <td><small><span class="badge badge-{!! $result->status == 'published' ? 'success' : 'danger' !!}">{!! strtoupper($result->status) !!}</span></small></td>
                                            <td>
                                                @adminCan('service.edit')
                                                    <a href="{!! route('admin.service.edit', $result->id) !!}" class="btn btn-primary btn-sm waves-effect waves-light"><i class="feather icon-edit"></i></a>
                                                @endadminCan
                                                @adminCan('service.destroy')
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="deleteConfirmation({!! $result->id !!})"><i class="feather icon-trash"></i></button>
                                                    <form action="{!! URL::route('admin.service.destroy', $result->id) !!}" method="POST" id="deleteForm{!! $result->id !!}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endadminCan
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
