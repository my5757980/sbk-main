@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Order Detail</h4>
                <a href="{{ route('admin.orders.edit', $data->id) }}" class="btn btn-warning btn-sm">Edit Order</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4"><h5>Order No</h5><p>{{ $data->order_no }}</p></div>
                    <div class="col-md-4"><h5>Status</h5><p>{{ ucfirst($data->status) }}</p></div>
                    <div class="col-md-4"><h5>Order Date</h5><p>{{ $data->order_date ? date('d/m/Y', strtotime($data->order_date)) : date('d/m/Y', strtotime($data->created_at)) }}</p></div>
                    <div class="col-md-4"><h5>User</h5><p>{{ optional($data->user)->name ?: $data->name }}</p></div>
                    <div class="col-md-4"><h5>Email</h5><p>{{ optional($data->user)->email ?: $data->email }}</p></div>
                    <div class="col-md-4"><h5>Phone</h5><p>{{ optional($data->user)->phone ?: $data->phone }}</p></div>
                    <div class="col-md-4"><h5>Country</h5><p>{{ optional($data->user)->country ?: $data->country }}</p></div>
                    <div class="col-md-4"><h5>Subtotal</h5><p>${{ number_format($data->subtotal, 2) }}</p></div>
                    <div class="col-md-4"><h5>Paid Amount</h5><p>${{ number_format($data->paid_amount ?? $data->subtotal, 2) }}</p></div>
                    <div class="col-md-4"><h5>Remaining Amount</h5><p>${{ number_format($data->remaining_amount ?? max(($data->subtotal ?? 0) - ($data->paid_amount ?? 0), 0), 2) }}</p></div>
                    <div class="col-md-4"><h5>Payment Percentage</h5><p>{{ $data->payment_percentage ?? 'N/A' }}%</p></div>
                    <div class="col-md-4"><h5>PayPal Order ID</h5><p>{{ $data->paypal_order_id ?? 'N/A' }}</p></div>
                </div>
                <hr>
                <h5>Products</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->items as $item)
                                <tr>
                                    <td>{{ $item->product_title }}</td>
                                    <td>{{ $item->brand ?: 'N/A' }}</td>
                                    <td>{{ $item->model ?: 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($data->message)
                    <hr>
                    <h5>Notes</h5>
                    <p>{{ $data->message }}</p>
                @endif
                <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
