@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Detail</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <div class="row" style="width: 100%;">
                                    <div class="col-6">
                                        <p><h5>Name: </h5>{!! $data->name ?: trim($data->first_name . ' ' . $data->last_name) !!}</p>
                                    </div>
                                    <div class="col-6">
                                        <p><h5>Email: </h5>{!! $data->email ?? 'N/A' !!}</p>
                                    </div>
                                    <div class="col-6">
                                        <p><h5>Phone: </h5>{!! $data->phone ?? 'N/A' !!}</p>
                                    </div>
                                    <div class="col-6">
                                        <p><h5>Country: </h5>{!! $data->country ?? 'N/A' !!}</p>
                                    </div>
                                    <div class="col-6">
                                        <p><h5>Role: </h5>{!! \App\Models\User::ROLE_OPTIONS[$data->role ?? 'customer'] ?? ucfirst($data->role ?? 'customer') !!}</p>
                                    </div>
                                    <div class="col-6">
                                        <p><h5>Profile Picture: </h5><img src="{{ $data->profile_picture_url }}" alt="Profile" height="80" width="80" style="object-fit:cover;border-radius:50%"></p>
                                    </div>
                                    <div class="col-6">
                                        <p><h5>Status: </h5><small><span class="badge badge-{!! $data->status == 1 ? 'success' : 'danger' !!}">{!! $data->status == 1 ? 'Active' : 'Inactive' !!}</span></small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Order Record</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order No</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data->orders()->latest()->get() as $order)
                                    <tr>
                                        <td>{{ $order->order_no }}</td>
                                        <td>${{ number_format($order->paid_amount ?? $order->subtotal, 2) }}</td>
                                        <td>{{ ucfirst($order->status) }}</td>
                                        <td>{{ $order->order_date ? date('d/m/Y', strtotime($order->order_date)) : date('d/m/Y', strtotime($order->created_at)) }}</td>
                                        <td><a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">View</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">No orders found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{!! route('admin.users.index') !!}"
                               class="btn btn-primary waves-effect waves-light">
                                <i class="ti ti-check me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
