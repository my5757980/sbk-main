@extends('admin.layouts.app')

@push('css')
    <style>
        .sbk-admin-dashboard{color:#222}.sbk-dash-hero{position:relative;overflow:hidden;border-radius:10px;padding:28px 30px;margin-bottom:24px;background:linear-gradient(135deg,#121317 0%,#25272d 62%,#d71920 100%);color:#fff;box-shadow:0 18px 45px rgba(17,18,20,.16)}.sbk-dash-hero:after{content:"SBK";position:absolute;right:18px;bottom:-24px;font-size:118px;font-weight:900;line-height:1;color:rgba(255,255,255,.07);letter-spacing:0}.sbk-dash-hero h1{position:relative;margin:0;color:#fff;font-size:32px;font-weight:800}.sbk-dash-hero p{position:relative;max-width:680px;margin:8px 0 0;color:rgba(255,255,255,.78);line-height:1.6}.sbk-dash-actions{position:relative;display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}.sbk-dash-actions a{display:inline-flex;align-items:center;gap:8px;min-height:38px;padding:0 14px;border-radius:6px;background:#fff;color:#111;font-weight:700}.sbk-dash-actions a:hover{color:#d71920}.sbk-stat-card{position:relative;overflow:hidden;border:0;border-radius:10px;box-shadow:0 14px 34px rgba(17,18,20,.08)}.sbk-stat-card .card-body{position:relative;display:flex;justify-content:space-between;gap:16px;min-height:132px}.sbk-stat-label{margin:0 0 6px;color:#6b6f77;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.6px}.sbk-stat-number{margin:0;color:#15161a;font-size:31px;font-weight:900}.sbk-stat-note{margin:8px 0 0;color:#8a8f98;font-size:12px}.sbk-stat-icon{display:flex;align-items:center;justify-content:center;flex:0 0 48px;width:48px;height:48px;border-radius:9px;background:#111216;color:#fff}.sbk-stat-icon.red{background:#d71920}.sbk-stat-icon.gray{background:#eef0f3;color:#111}.sbk-panel{border:0;border-radius:10px;box-shadow:0 14px 34px rgba(17,18,20,.08);overflow:hidden}.sbk-panel .card-header{border-bottom:1px solid #edf0f3;background:#fff}.sbk-panel-title{margin:0;color:#17181c;font-weight:800}.sbk-chart{display:grid;grid-template-columns:repeat(6,1fr);gap:13px;align-items:end;min-height:236px;padding-top:12px}.sbk-chart-col{display:flex;flex-direction:column;align-items:center;gap:8px;min-width:0}.sbk-chart-bar{width:100%;max-width:46px;border-radius:8px 8px 3px 3px;background:linear-gradient(180deg,#d71920,#8f0f14);box-shadow:0 10px 18px rgba(215,25,32,.16)}.sbk-chart-col strong{font-size:12px;color:#111}.sbk-chart-col span{font-size:11px;color:#7b8088}.sbk-mix{display:grid;gap:16px}.sbk-mix-row{display:grid;gap:7px}.sbk-mix-top{display:flex;justify-content:space-between;color:#454951;font-size:13px;font-weight:700}.sbk-mix-track{height:9px;border-radius:999px;background:#edf0f3;overflow:hidden}.sbk-mix-fill{height:100%;border-radius:999px;background:#d71920}.sbk-mix-fill.dark{background:#111216}.sbk-mix-fill.soft{background:#8a8f98}.sbk-table .badge{padding:6px 9px;border-radius:999px;text-transform:capitalize}.sbk-empty{padding:26px;color:#777;text-align:center}.sbk-user-name{font-weight:800;color:#111}.sbk-user-email{color:#7b8088;font-size:12px}@media(max-width:767px){.sbk-dash-hero{padding:24px 20px}.sbk-dash-hero h1{font-size:26px}.sbk-chart{gap:8px}.sbk-chart-bar{max-width:34px}}
    </style>
@endpush

@section('content')
    @php
        $maxOrders = max($orderChart->max('orders') ?? 0, 1);
        $contentTotal = max(($productCount ?? 0) + ($blogCount ?? 0) + ($pageCount ?? 0), 1);
        $publishedProductPercent = ($productCount ?? 0) > 0 ? round((($publishedProductCount ?? 0) / $productCount) * 100) : 0;
        $publishedBlogPercent = ($blogCount ?? 0) > 0 ? round((($publishedBlogCount ?? 0) / $blogCount) * 100) : 0;
        $paidOrderPercent = ($ordersCount ?? 0) > 0 ? round((($paidOrdersCount ?? 0) / $ordersCount) * 100) : 0;
    @endphp

    <section id="dashboard-analytics" class="sbk-admin-dashboard">
        <div class="sbk-dash-hero">
            <h1>SBK Control Dashboard</h1>
            <p>Track vehicle inventory, checkout requests, paid amounts, customer activity, and publishing health from one clean admin view.</p>
            <div class="sbk-dash-actions">
                @adminCan('product.create')
                    <a href="{{ route('admin.product.create') }}"><i class="feather icon-plus"></i> Add Vehicle</a>
                @endadminCan
                @adminCan('orders.index')
                    <a href="{{ route('admin.orders.index') }}"><i class="feather icon-shopping-bag"></i> View Orders</a>
                @endadminCan
                @adminCan('blog.create')
                    <a href="{{ route('admin.blog.create') }}"><i class="feather icon-edit-3"></i> Add Blog</a>
                @endadminCan
            </div>
        </div>

        <div class="row">
            @adminCan('orders.index')
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('admin.orders.index') }}">
                    <div class="card sbk-stat-card">
                        <div class="card-body">
                            <div>
                                <p class="sbk-stat-label">Orders</p>
                                <h2 class="sbk-stat-number">{{ number_format($ordersCount ?? 0) }}</h2>
                                <p class="sbk-stat-note">{{ number_format($pendingOrdersCount ?? 0) }} pending requests</p>
                            </div>
                            <div class="sbk-stat-icon red"><i class="feather icon-shopping-bag font-medium-5"></i></div>
                        </div>
                    </div>
                </a>
            </div>
            @endadminCan
            @adminCan('product.index')
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('admin.product.index') }}">
                    <div class="card sbk-stat-card">
                        <div class="card-body">
                            <div>
                                <p class="sbk-stat-label">Vehicles</p>
                                <h2 class="sbk-stat-number">{{ number_format($productCount ?? 0) }}</h2>
                                <p class="sbk-stat-note">{{ number_format($publishedProductCount ?? 0) }} published inventory items</p>
                            </div>
                            <div class="sbk-stat-icon"><i class="feather icon-truck font-medium-5"></i></div>
                        </div>
                    </div>
                </a>
            </div>
            @endadminCan
            @adminCan('users.index')
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('admin.users.index') }}">
                    <div class="card sbk-stat-card">
                        <div class="card-body">
                            <div>
                                <p class="sbk-stat-label">Customers</p>
                                <h2 class="sbk-stat-number">{{ number_format(($usersCount ?? 0) + ($crmLeadsCount ?? 0)) }}</h2>
                                <p class="sbk-stat-note">
                                    @if(($crmLeadsCount ?? 0) > 0)
                                        {{ number_format($crmLeadsCount) }} of them without an email in the CRM
                                    @else
                                        Latest buyer accounts
                                    @endif
                                </p>
                            </div>
                            <div class="sbk-stat-icon gray"><i class="feather icon-users font-medium-5"></i></div>
                        </div>
                    </div>
                </a>
            </div>
            @endadminCan
            @adminCan('blog.index')
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('admin.blog.index') }}">
                    <div class="card sbk-stat-card">
                        <div class="card-body">
                            <div>
                                <p class="sbk-stat-label">Blogs</p>
                                <h2 class="sbk-stat-number">{{ number_format($blogCount ?? 0) }}</h2>
                                <p class="sbk-stat-note">{{ number_format($publishedBlogCount ?? 0) }} published articles</p>
                            </div>
                            <div class="sbk-stat-icon red"><i class="feather icon-file-text font-medium-5"></i></div>
                        </div>
                    </div>
                </a>
            </div>
            @endadminCan
        </div>

        <div class="row">
            <div class="col-xl-8 col-12">
                <div class="card sbk-panel">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="sbk-panel-title">Order Activity</h4>
                        <span class="text-muted">Last 6 months</span>
                    </div>
                    <div class="card-body">
                        <div class="sbk-chart">
                            @foreach($orderChart as $point)
                                @php $height = 28 + (($point['orders'] / $maxOrders) * 150); @endphp
                                <div class="sbk-chart-col">
                                    <span>${{ number_format($point['revenue'], 0) }}</span>
                                    <div class="sbk-chart-bar" style="height:{{ $height }}px"></div>
                                    <strong>{{ $point['label'] }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
                <div class="card sbk-panel">
                    <div class="card-header">
                        <h4 class="sbk-panel-title">Business Snapshot</h4>
                    </div>
                    <div class="card-body sbk-mix">
                        <div>
                            <p class="sbk-stat-label">Paid Amount</p>
                            <h2 class="sbk-stat-number">${{ number_format($totalRevenue ?? 0, 2) }}</h2>
                            <p class="sbk-stat-note">${{ number_format($remainingBalance ?? 0, 2) }} remaining across orders</p>
                        </div>
                        <div class="sbk-mix-row">
                            <div class="sbk-mix-top"><span>Paid/Completed Orders</span><strong>{{ $paidOrderPercent }}%</strong></div>
                            <div class="sbk-mix-track"><div class="sbk-mix-fill" style="width:{{ $paidOrderPercent }}%"></div></div>
                        </div>
                        <div class="sbk-mix-row">
                            <div class="sbk-mix-top"><span>Published Vehicles</span><strong>{{ $publishedProductPercent }}%</strong></div>
                            <div class="sbk-mix-track"><div class="sbk-mix-fill dark" style="width:{{ $publishedProductPercent }}%"></div></div>
                        </div>
                        <div class="sbk-mix-row">
                            <div class="sbk-mix-top"><span>Published Blogs</span><strong>{{ $publishedBlogPercent }}%</strong></div>
                            <div class="sbk-mix-track"><div class="sbk-mix-fill soft" style="width:{{ $publishedBlogPercent }}%"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @adminCan('pages.index')
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card sbk-stat-card">
                    <div class="card-body">
                        <div>
                            <p class="sbk-stat-label">Pages</p>
                            <h2 class="sbk-stat-number">{{ number_format($pageCount ?? 0) }}</h2>
                            <p class="sbk-stat-note">Static site content</p>
                        </div>
                        <div class="sbk-stat-icon"><i class="feather icon-globe font-medium-5"></i></div>
                    </div>
                </div>
            </div>
            @endadminCan
            @adminCan('contact-us.index')
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card sbk-stat-card">
                    <div class="card-body">
                        <div>
                            <p class="sbk-stat-label">Contact Leads</p>
                            <h2 class="sbk-stat-number">{{ number_format($contactCount ?? 0) }}</h2>
                            <p class="sbk-stat-note">Customer inquiries</p>
                        </div>
                        <div class="sbk-stat-icon red"><i class="feather icon-message-square font-medium-5"></i></div>
                    </div>
                </div>
            </div>
            @endadminCan
            @adminCan('newsletters.index')
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card sbk-stat-card">
                    <div class="card-body">
                        <div>
                            <p class="sbk-stat-label">Newsletter</p>
                            <h2 class="sbk-stat-number">{{ number_format($newsletterCount ?? 0) }}</h2>
                            <p class="sbk-stat-note">Subscribers</p>
                        </div>
                        <div class="sbk-stat-icon gray"><i class="feather icon-mail font-medium-5"></i></div>
                    </div>
                </div>
            </div>
            @endadminCan
        </div>

        <div class="row">
            @adminCan('orders.index')
            <div class="col-xl-7 col-12">
                <div class="card sbk-panel">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="sbk-panel-title">Recent Orders</h4>
                        <a href="{{ route('admin.orders.index') }}">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover-animation mb-0 sbk-table">
                            <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Paid</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recentOrders ?? [] as $order)
                                <tr>
                                    <td>
                                        @adminCan('orders.show')
                                            <a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_no }}</a>
                                        @else
                                            {{ $order->order_no }}
                                        @endadminCan
                                    </td>
                                    <td>{{ $order->name }}</td>
                                    <td>${{ number_format($order->paid_amount, 2) }}</td>
                                    <td><span class="badge badge-light-{{ in_array($order->status, ['paid', 'completed']) ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">{{ $order->status }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="sbk-empty">No orders yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endadminCan
            @adminCan('users.index')
            <div class="col-xl-5 col-12">
                <div class="card sbk-panel">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="sbk-panel-title">Latest Customers</h4>
                        <a href="{{ route('admin.users.index') }}">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover-animation mb-0">
                            <tbody>
                            @forelse($users ?? [] as $user)
                                <tr>
                                    <td>
                                        @adminCan('users.show')
                                            <a class="sbk-user-name" href="{{ route('admin.users.show', $user->id) }}">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? 'Customer') }}</a>
                                        @else
                                            <span class="sbk-user-name">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? 'Customer') }}</span>
                                        @endadminCan
                                        <div class="sbk-user-email">{{ $user->email }}</div>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-light-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="sbk-empty">No customers yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endadminCan
        </div>
    </section>
@endsection
