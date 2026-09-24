@extends('admin.layouts.app')

@section('content')
<section id="column-selectors">
    <div class="row"><div class="col-12"><div class="card">
        <div class="card-header">
            <h4 class="card-title">Products</h4>
            @adminCan('product.create')
                <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Add New</a>
            @endadminCan
        </div>
        <div class="card-content"><div class="card-body card-dashboard"><div class="table-responsive">
            <table class="table table-striped dataex-html5-selectors">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Price</th>
                        <th>Home Section</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($data as $result)
                    <tr>
                        <td><img src="{{ $result->primary_image_url }}" alt="{{ $result->title }}" width="70"></td>
                        <td>{{ Str::limit($result->title, 32) }}</td>
                        <td>{{ optional($result->brand)->name }}</td>
                        <td>{{ optional($result->vehicleModel)->name }}</td>
                        <td>{{ $result->year }}</td>
                        <td>${{ number_format($result->price, 2) }}</td>
                        <td>{{ \App\Models\Product::HOME_SECTION_OPTIONS[$result->home_section] ?? '-' }}</td>
                        <td><span class="badge badge-{{ $result->status === 'published' ? 'success' : ($result->status === 'sold' ? 'danger' : 'secondary') }}">{{ strtoupper($result->status) }}</span></td>
                        <td>
                            @adminCan('product.edit')
                                <a href="{{ route('admin.product.edit', $result->id) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i></a>
                            @endadminCan
                            @adminCan('product.destroy')
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirmation({{ $result->id }})"><i class="feather icon-trash"></i></button>
                                <form action="{{ route('admin.product.destroy', $result->id) }}" method="POST" id="deleteForm{{ $result->id }}">@csrf @method('DELETE')</form>
                            @endadminCan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div></div></div>
    </div></div></div>
</section>
@endsection
