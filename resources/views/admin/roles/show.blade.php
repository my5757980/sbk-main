@extends('admin.layouts.app')

@section('content')
<section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Role Details <i class="feather icon-shield"></i></h4>
                </div>
                <div class="card-body">
                    <h3>{{ $data->name }}</h3>
                    <p>{{ $data->description ?: 'No description added.' }}</p>
                    <hr>
                    <h5>Permissions</h5>
                    <div class="row">
                        @foreach($data->permissions->groupBy('module') as $module => $permissions)
                            <div class="col-md-4 mb-2">
                                <strong>{{ config('admin_permissions.' . $module . '.label', ucfirst($module)) }}</strong>
                                <ul class="mt-1">
                                    @foreach($permissions as $permission)
                                        <li>{{ $permission->label }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.admin-roles.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
