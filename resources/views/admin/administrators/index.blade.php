@extends('admin.layouts.app')

@section('content')
<section id="column-selectors">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Administrators <i class="feather icon-user"></i></h4>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table table-striped dataex-html5-selectors">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $admin)
                                        <tr>
                                            <td>{!! $admin->first_name !!} {!! $admin->last_name !!}</td>
                                            <td>{!! $admin->phone !!}</td>
                                            <td>{!! $admin->email !!}</td>
                                            <td>{{ optional($admin->role)->name ?: 'No Role' }}</td>
                                            <td>{!! ($admin->is_active > 0) ? 'Yes' : 'No' !!}</td>
                                            <td>
                                                @adminCan('administrators.show')
                                                    <a href="{!! route('admin.administrators.show', $admin->id) !!}" class="btn btn-info btn-sm waves-effect waves-light"><i class="feather icon-search"></i></a>
                                                @endadminCan

                                            @adminCan('administrators.edit')
                                                @if(auth('admin')->user()->is_system_admin == 1 || auth('admin')->user()->id == $admin->id)
                                                    <a href="{!! route('admin.administrators.edit', $admin->id) !!}" class="btn btn-primary btn-sm waves-effect waves-light"><i class="feather icon-edit"></i></a>
                                                @else
                                                    <a href="{!! route('admin.administrators.edit', $admin->id) !!}" class="btn btn-primary btn-sm waves-effect waves-light disabled"><i class="feather icon-edit"></i></a>
                                                @endif
                                            @endadminCan

                                            @adminCan('administrators.destroy')
                                                @if(auth('admin')->user()->is_system_admin != 1)
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light disabled"><i class="feather icon-trash"></i></button>
                                                @elseif (auth('admin')->user()->is_system_admin == 1 && auth('admin')->id() != $admin->id)
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="deleteConfirmation({!! $admin->id !!})"><i class="feather icon-trash"></i></button>
                                                    <form action="{!! URL::route('admin.administrators.destroy', $admin->id) !!}" method="POST" id="deleteForm{!! $admin->id !!}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
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
