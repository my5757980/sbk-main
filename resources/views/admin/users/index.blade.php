@extends('admin.layouts.app')

@section('content')
    <section id="column-selectors">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Users <i class="feather icon-user"></i></h4>
                        @adminCan('users.create')
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Add User</a>
                        @endadminCan
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
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
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $user)
                                            <tr>
                                                <td>{!! $user->name ?: trim($user->first_name . ' ' . $user->last_name) !!}</td>
                                                <td>{!! $user->email !!}</td>
                                                <td>{!! $user->phone ?? 'N/A' !!}</td>
                                                <td>{!! $user->country ?? 'N/A' !!}</td>
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
