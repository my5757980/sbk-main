@extends('admin.layouts.app')

@section('content')
    <section id="column-selectors">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
                        <h4 class="card-title">Users <i class="feather icon-user"></i></h4>
                        <div class="d-flex align-items-center flex-wrap" style="gap:8px">
                            <small class="text-muted">
                                CRM customers sync every 5 min{{ !empty($crmLastSync) ? ' - last ' . \Carbon\Carbon::parse($crmLastSync)->diffForHumans() : '' }}
                            </small>
                            @adminCan('users.edit')
                                <form method="POST" action="{{ route('admin.users.edit.crm-sync') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Sync from CRM</button>
                                </form>
                                @if(($crmAwaitingPasswords ?? 0) > 0)
                                    <form method="POST" action="{{ route('admin.users.edit.crm-passwords') }}" class="d-inline"
                                          onsubmit="return confirm('Generate a separate password for {{ $crmAwaitingPasswords }} CRM customer(s)? The list is shown once so you can send each customer their own.');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Generate passwords ({{ $crmAwaitingPasswords }})</button>
                                    </form>
                                @endif
                            @endadminCan
                            @adminCan('users.create')
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Add User</a>
                            @endadminCan
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @if(!empty($crmProblem))
                                <div class="alert alert-danger" role="alert" style="margin-bottom:16px">
                                    <b>CRM:</b> {{ $crmProblem }}
                                    <br><small>A developer can run <code>php artisan crm:check</code> on the server to see exactly what is missing.</small>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-striped" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Country</th>
                                            <th>Role</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>CRM</th>
                                            @if(!empty($seesAll))
                                                <th>Agent</th>
                                            @endif
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $user)
                                            <tr>
                                                <td>
                                                    {{ $user->name ?: trim($user->first_name . ' ' . $user->last_name) }}
                                                    @if($user->hasCrmRecord())
                                                        <span class="badge badge-danger ml-25" title="Linked to the CRM">CRM</span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->phone ?: 'N/A' }}</td>
                                                <td>{{ $user->country ?: 'N/A' }}</td>
                                                <td>{!! $roleOptions[$user->role ?? 'customer'] ?? ucfirst($user->role ?? 'customer') !!}</td>
                                                <td><span
                                                        style="display: none">{!! strtotime($user->created_at) !!}</span>{!! date('d/m/Y H:i:A', strtotime($user->created_at)) !!}
                                                </td>
                                                <td class="text-center">
                                                    @adminCan('users.edit')
                                                        <div class="form-check form-switch mb-2">
                                                            <input class="form-check-input switch-button" type="checkbox"
                                                                id="isActive_{{ $user->id }}" data-id="{{ $user->id }}"
                                                                data-column="status" {{ $user->status ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="isActive_{{ $user->id }}"></label>
                                                        </div>
                                                    @else
                                                        {{ $user->status ? 'Active' : 'Inactive' }}
                                                    @endadminCan
                                                </td>
                                                <td style="white-space:nowrap">
                                                    @if($user->hasCrmRecord() && is_array($user->crm_summary))
                                                        @php $crmSummary = $user->crm_summary; @endphp
                                                        <span style="display:none">{{ $crmSummary['invoices'] ?? 0 }}</span>
                                                        {{ $crmSummary['invoices'] ?? 0 }} invoice{{ ($crmSummary['invoices'] ?? 0) == 1 ? '' : 's' }}<br>
                                                        @if(($crmSummary['balance'] ?? 0) > 0)
                                                            <small class="text-danger">${{ number_format($crmSummary['balance']) }} due</small>
                                                        @else
                                                            <small class="text-success">Paid in full</small>
                                                        @endif
                                                    @elseif($user->crm_pending_email)
                                                        <small class="text-warning">Pending link</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                @if(!empty($seesAll))
                                                    {{-- Whose customer this is: the CRM lead's owner, or who added them here. --}}
                                                    <td style="white-space:nowrap">
                                                        @forelse($user->relationLoaded('agents') ? $user->agents : [] as $owner)
                                                            <div title="{{ $owner->agent_email }}">
                                                                {{ $agentNames[$owner->agent_email] ?? $owner->agent_email }}
                                                                @unless(isset($agentNames[$owner->agent_email]))
                                                                    <small class="text-warning" title="No admin account with this email on the website">(no account)</small>
                                                                @endunless
                                                            </div>
                                                        @empty
                                                            <span class="text-muted">-</span>
                                                        @endforelse
                                                    </td>
                                                @endif

                                                <td>
                                                    @adminCan('users.show')
                                                        <a href="{!! route('admin.users.show', $user->id) !!}"
                                                            class="btn btn-info btn-sm waves-effect waves-light"><i
                                                                class="feather icon-search"></i></a>
                                                    @endadminCan

                                                    @adminCan('users.edit')
                                                        <a href="{!! route('admin.users.edit', $user->id) !!}"
                                                            class="btn btn-warning btn-sm waves-effect waves-light"><i
                                                                class="feather icon-edit"></i></a>
                                                    @endadminCan

                                                    @adminCan('users.destroy')
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm waves-effect waves-light"
                                                            onclick="deleteConfirmation({!! $user->id !!})">
                                                            <i class="feather icon-trash"></i>
                                                        </button>

                                                        <form action="{!! URL::route('admin.users.destroy', $user->id) !!}" method="POST"
                                                            id="deleteForm{!! $user->id !!}">
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
                "order": [[5, "desc"]],
                "stateSave": true
            });
        });

        $(document).on('change', '.switch-button', function() {
            var status = 0;
            var column = $(this).data('column');
            var id = $(this).data('id');
            if ($(this).is(':checked')) {
                status = 1;
            } else {
                status = 0;
            }
            updateStatus(id, status, column)
        });

        function updateStatus(id, status, column) {
            jQuery.ajax({
                url: '{{ route('admin.update-status') }}',
                type: 'POST',
                data: {
                    _token: "{!! csrf_token() !!}",
                    id,
                    status,
                    column
                },
                dataType: "json",
                success: function(data) {

                    if (data.status == false) {
                        toastr.error(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                    if (data.status == true) {
                        toastr.success(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function(reject) {
                    var errors = $.parseJSON(reject.responseText);

                    if (reject.status === 422) {
                        $.each(errors.errors, function(key, val) {
                            toastr.error(val, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        });
                    }

                    toastr.error(errors.message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }
    </script>
@endsection
