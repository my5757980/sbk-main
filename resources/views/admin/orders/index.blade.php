@extends('admin.layouts.app')

@section('content')
<section id="column-selectors">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Orders <i class="feather icon-shopping-bag"></i></h4>
                    @adminCan('orders.create')
                        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary btn-sm">Add Order</a>
                    @endadminCan
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table table-striped" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Order No</th>
                                        <th>User</th>
                                        <th>Product</th>
                                        <th>Paid</th>
                                        <th>Remaining</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $order)
                                        <tr>
                                            <td>{{ $order->order_no }}</td>
                                            <td>
                                                {{ optional($order->user)->name ?: $order->name }}<br>
                                                <small>{{ optional($order->user)->email ?: $order->email }}</small>
                                            </td>
                                            <td>
                                                @foreach($order->items as $item)
                                                    <div>{{ $item->product_title }} <small>x {{ $item->quantity }}</small></div>
                                                @endforeach
                                            </td>
                                            <td>${{ number_format($order->paid_amount ?? $order->subtotal, 2) }}</td>
                                            <td>${{ number_format($order->remaining_amount ?? max(($order->subtotal ?? 0) - ($order->paid_amount ?? 0), 0), 2) }}</td>
                                            <td><span class="badge badge-primary">{{ ucfirst($order->status) }}</span></td>
                                            <td>
                                                <span style="display:none">{{ strtotime($order->order_date ?? $order->created_at) }}</span>
                                                {{ $order->order_date ? date('d/m/Y', strtotime($order->order_date)) : date('d/m/Y', strtotime($order->created_at)) }}
                                            </td>
                                            <td class="text-center">
                                                @adminCan('orders.show')
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm"><i class="feather icon-search"></i></a>
                                                @endadminCan
                                                @adminCan('orders.edit')
                                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-sm"><i class="feather icon-edit"></i></a>
                                                @endadminCan
                                                @adminCan('orders.destroy')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteConfirmation({{ $order->id }})"><i class="feather icon-trash"></i></button>
                                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" id="deleteForm{{ $order->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
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

@section('footer-js')
<script>
    $(document).ready(function () {
        $('#datatable').DataTable({
            "order": [[6, "desc"]],
            "stateSave": true
        });
    });
</script>
@endsection
