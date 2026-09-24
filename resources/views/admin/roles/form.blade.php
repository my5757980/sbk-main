@extends('admin.layouts.app')

@section('content')
<section id="number-tabs">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $form['heading'] }} <i class="feather icon-shield"></i></h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form method="POST" action="{{ $form['action'] }}">
                            @csrf
                            @if($form['method'] !== 'POST')
                                @method($form['method'])
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role Name *</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $data->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <input type="text" name="description" class="form-control" value="{{ old('description', $data->description) }}">
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-2 mb-1">Permissions</h5>
                            <div class="row">
                                @foreach($permissions as $module => $items)
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card border">
                                            <div class="card-header py-1">
                                                <strong>{{ config('admin_permissions.' . $module . '.label', ucfirst($module)) }}</strong>
                                            </div>
                                            <div class="card-body">
                                                @foreach($items as $permission)
                                                    <div class="custom-control custom-checkbox mb-1">
                                                        <input type="checkbox" class="custom-control-input" id="permission{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $selectedPermissions)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="permission{{ $permission->id }}">{{ $permission->label }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary mr-1 mb-1">Save Role</button>
                            <a href="{{ route('admin.admin-roles.index') }}" class="btn btn-danger mr-1 mb-1">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
