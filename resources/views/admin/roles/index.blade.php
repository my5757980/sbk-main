@extends('admin.layouts.app')

@section('content')
<section id="column-selectors">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Roles & Permissions <i class="feather icon-shield"></i></h4>
                    @adminCan('admin-roles.create')
                        <a href="{{ route('admin.admin-roles.create') }}" class="btn btn-primary">Add Role</a>
                    @endadminCan
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table table-striped dataex-html5-selectors">
                                <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th>Admins</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $role)
                                    <tr>
                                        <td>
                                            <strong>{{ $role->name }}</strong>
                                            @if($role->is_system)<span class="badge badge-light-primary ml-1">System</span>@endif
                                            <div class="text-muted">{{ $role->description }}</div>
                                        </td>
                                        <td>{{ $role->permissions_count }}</td>
                                        <td>{{ $role->admins_count }}</td>
                                        <td class="text-center">
                                            @adminCan('admin-roles.show')
                                                <a href="{{ route('admin.admin-roles.show', $role->id) }}" class="btn btn-info btn-sm"><i class="feather icon-search"></i></a>
                                            @endadminCan
                                            @if(!$role->is_system)
                                                @adminCan('admin-roles.edit')
                                                    <a href="{{ route('admin.admin-roles.edit', $role->id) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i></a>
                                                @endadminCan
                                                @adminCan('admin-roles.destroy')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirmation({{ $role->id }})"><i class="feather icon-trash"></i></button>
                                                    <form action="{{ route('admin.admin-roles.destroy', $role->id) }}" method="POST" id="deleteForm{{ $role->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endadminCan
                                            @endif
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
