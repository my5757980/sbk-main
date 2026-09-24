@extends('admin.layouts.app')

@section('content')
@php $currentItem = $data->exists ? $data->items->first() : null; @endphp
<section id="number-tabs">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">{{ $form['heading'] }}</h4></div>
                <div class="card-content">
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">Please check the order fields and try again.</div>
                        @endif
                        <form method="POST" action="{{ $form['action'] }}">
                            @csrf
                            @method($form['method'])
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>User *</label>
                                        <select name="user_id" class="form-control select2" required>
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {!! matchSelected($user->id, old('user_id', $data->user_id)) !!}>
                                                    {{ $user->name ?: trim($user->first_name . ' ' . $user->last_name) }} - {{ $user->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product *</label>
                                        <select name="product_id" id="orderProduct" class="form-control select2" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" {!! matchSelected($product->id, old('product_id', $selectedProductId)) !!}>
                                                    {{ $product->title }} - ${{ number_format($product->price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Quantity *</label>
                                        <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity', $quantity) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Amount *</label>
                                        <input type="number" name="amount" id="orderAmount" class="form-control" min="0" step="0.01" value="{{ old('amount', optional($currentItem)->price ?? 0) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Paid Amount *</label>
                                        <input type="number" name="paid_amount" class="form-control" min="0" step="0.01" value="{{ old('paid_amount', $data->paid_amount ?? $data->subtotal ?? 0) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Remaining</label>
                                        <input type="text" class="form-control" value="${{ number_format($data->remaining_amount ?? max(($data->subtotal ?? 0) - ($data->paid_amount ?? 0), 0), 2) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Order Date *</label>
                                        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $data->order_date ? date('Y-m-d', strtotime($data->order_date)) : now()->toDateString()) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status *</label>
                                        <select name="status" class="form-control" required>
                                            @foreach(['pending' => 'Pending', 'processing' => 'Processing', 'paid' => 'Paid', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                                                <option value="{{ $value }}" {!! matchSelected($value, old('status', $data->status)) !!}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method', $data->payment_method ?? 'Manual Admin Order') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Order Notes</label>
                                        <textarea name="message" class="form-control" rows="4">{{ old('message', $data->message) }}</textarea>
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

@section('footer-js')
<script>
    $('#orderProduct').on('change', function () {
        const price = $(this).find('option:selected').data('price');
        if (price && !Number($('#orderAmount').val())) {
            $('#orderAmount').val(price);
        }
    }).trigger('change');
</script>
@endsection
