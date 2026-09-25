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

    {{-- The customer's record in the SBK CRM (read live; see app/Services/Crm). --}}
    @if($data->hasCrmRecord() || $data->crm_pending_email)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
                        <h4 class="card-title mb-0">CRM Record
                            @if($data->hasCrmRecord())
                                <span class="badge badge-danger ml-1">CRM</span>
                            @endif
                        </h4>
                        <div class="d-flex flex-wrap" style="gap:8px">
                            @if($data->hasCrmRecord())
                                <a href="{{ route('admin.users.show', ['user' => $data->id, 'refresh' => 1]) }}" class="btn btn-outline-secondary btn-sm">Refresh from CRM</a>
                            @endif
                            @adminCan('users.edit')
                                @if(($data->role ?? 'customer') === 'customer')
                                    <form method="POST" action="{{ route('admin.users.edit.generate-password', $data->id) }}" class="d-inline"
                                          onsubmit="return confirm('Generate a new password for this customer? Their current password will stop working.');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Generate password</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.users.edit.send-reset', $data->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm" title="Emails {{ $data->email }} a link to set their own password">Send password setup link</button>
                                </form>
                            @endadminCan
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('generated_password'))
                            <div class="alert alert-success" style="margin-bottom:16px">
                                <b>New login password for {{ $data->email }}:</b>
                                <code id="generatedPassword" style="font-size:16px;margin:0 8px">{{ session('generated_password') }}</code>
                                <button type="button" class="btn btn-sm btn-success" onclick="navigator.clipboard && navigator.clipboard.writeText(document.getElementById('generatedPassword').textContent).then(() => this.textContent = 'Copied')">Copy</button>
                                <br><small>Shown only once - send it to the customer now. They log in on the website with this email and password.</small>
                            </div>
                        @elseif($data->crm_password_generated_at)
                            <p class="text-muted" style="margin-bottom:10px">Login password generated {{ $data->crm_password_generated_at->diffForHumans() }}.</p>
                        @endif
                        @if(!$data->hasCrmRecord())
                            <div class="alert alert-warning mb-0">
                                A CRM customer uses this email (<b>{{ $data->crm_pending_email }}</b>), but this account was not
                                created from the CRM and its owner has not verified the email yet, so the CRM record is not shown to it.
                                It links itself when the owner sets a password through "Forgot password".
                                @adminCan('users.edit')
                                    <form method="POST" action="{{ route('admin.users.edit.crm-link', $data->id) }}" class="mt-1">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">I know this customer - link now</button>
                                    </form>
                                @endadminCan
                            </div>
                        @else
                            <p class="text-muted" style="margin-bottom:14px">
                                Linked to the CRM customer <b>{{ $data->crm_email }}</b>{{ $data->crm_source ? ' - account created from the CRM' : '' }}.
                                @if($data->crm_synced_at) Last synced {{ $data->crm_synced_at->diffForHumans() }}. @endif
                            </p>
                            @include('partials.crm.record', ['crm' => $crm])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
