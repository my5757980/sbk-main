@extends('admin.layouts.app')

@section('content')
<section id="column-selectors">
    <div class="row"><div class="col-12"><div class="card">
        <div class="card-header">
            <h4 class="card-title">Vehicle Models</h4>
            @adminCan('vehicle-model.create')
                <a href="{{ route('admin.vehicle-model.create') }}" class="btn btn-primary">Add New</a>
            @endadminCan
        </div>
        <div class="card-content"><div class="card-body card-dashboard"><div class="table-responsive">
            <table class="table table-striped dataex-html5-selectors">
                <thead><tr><th>Brand</th><th>Model</th><th>Status</th><th>Sort</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($data as $result)
                    <tr>
                        <td>{{ optional($result->brand)->name }}</td>
                        <td>{{ $result->name }}</td>
                        <td><span class="badge badge-{{ $result->status === 'published' ? 'success' : 'secondary' }}">{{ strtoupper($result->status) }}</span></td>
                        <td>{{ $result->sort_order }}</td>
                        <td>
                            @adminCan('vehicle-model.edit')
                                <a href="{{ route('admin.vehicle-model.edit', $result->id) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i></a>
                            @endadminCan
                            @adminCan('vehicle-model.destroy')
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirmation({{ $result->id }})"><i class="feather icon-trash"></i></button>
                                <form action="{{ route('admin.vehicle-model.destroy', $result->id) }}" method="POST" id="deleteForm{{ $result->id }}">@csrf @method('DELETE')</form>
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
