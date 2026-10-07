@extends('admin.layouts.app')

@section('content')
<section id="number-tabs">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">{{ $form['heading'] }}</h4></div>
                <div class="card-content">
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                Please check the user fields and try again.
                                <ul class="mb-0 mt-50">
                                    @foreach($errors->all() as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ $form['action'] }}">
                            @csrf
                            @method($form['method'])
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name *</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $data->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email *</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $data->email) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Phone *</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $data->phone) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Country *</label>
                                        <input type="text" name="country" class="form-control" value="{{ old('country', $data->country) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Role *</label>
                                        <select name="role" class="form-control" required>
                                            {{-- A role that no longer exists must be chosen again, never silently turned into "Customer". --}}
                                            @unless(array_key_exists(old('role', $data->role ?? 'customer'), $roleOptions))
                                                <option value="" selected disabled>- choose a role -</option>
                                            @endunless
                                            @foreach($roleOptions as $value => $label)
                                                <option value="{{ $value }}" {!! matchSelected($value, old('role', $data->role ?? 'customer')) !!}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password {{ $data->exists ? '' : '*' }}</label>
                                        <input type="password" name="password" class="form-control" {{ $data->exists ? '' : 'required' }}>
                                        @if($data->exists)
                                            <small class="text-muted">Leave blank to keep the current password.</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-2">
                                        <fieldset class="checkbox">
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" name="status" value="1" {{ old('status', $data->status ?? 1) ? 'checked' : '' }}>
                                                <span class="vs-checkbox"><span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span></span>
                                                <span>Active User</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                            <a href="{{ $form['cancel_url'] }}" class="btn btn-danger" style="color:white">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
