@extends('admin.layouts.app')

@section('content')
<section id="number-tabs">
    <div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4 class="card-title">{{ $form['heading'] }}</h4></div>
        <div class="card-content"><div class="card-body">
            <form method="POST" action="{{ $form['action'] }}">
                @csrf
                @method($form['method'])
                <div class="row">
                    <div class="col-md-6"><div class="form-group">
                        <label>Brand *</label>
                        <select name="vehicle_brand_id" class="form-control select2" required>
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {!! matchSelected($brand->id, old('vehicle_brand_id', $data->vehicle_brand_id)) !!}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-6"><div class="form-group">
                        <label>Model Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $data->name) }}" required>
                    </div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control">
                            @foreach(pageStatuses() as $value => $label)
                                <option value="{{ $value }}" {!! matchSelected($value, old('status', $data->status)) !!}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $data->sort_order ?? 0) }}">
                    </div></div>
                    <div class="col-md-12"><div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $data->description) }}</textarea>
                    </div></div>
                </div>
                <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                <a href="{{ $form['cancel_url'] }}" class="btn btn-danger" style="color:white">Cancel</a>
            </form>
        </div></div>
    </div></div></div>
</section>
@endsection
