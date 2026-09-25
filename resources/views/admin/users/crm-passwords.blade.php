@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
                    <h4 class="card-title mb-0">CRM customer passwords</h4>
                    <div class="d-flex flex-wrap" style="gap:8px">
                        @if(count($rows))
                            <button type="button" class="btn btn-danger btn-sm" id="copyAllPasswords">Copy all</button>
                        @endif
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Back to users</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($rows))
                        <div class="alert alert-warning">
                            <b>This list is shown only once.</b> Copy it now and send each customer their own email and
                            password (WhatsApp, phone or email). Only a scrambled form is stored, so it cannot be shown again -
                            a new password can always be generated on the customer's page.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr><th>#</th><th>Customer</th><th>Email (login)</th><th>Phone</th><th>Password</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $n => $row)
                                        <tr>
                                            <td>{{ $n + 1 }}</td>
                                            <td><a href="{{ route('admin.users.show', $row['id']) }}">{{ $row['name'] }}</a></td>
                                            <td>{{ $row['email'] }}</td>
                                            <td>{{ $row['phone'] ?: '-' }}</td>
                                            <td><code style="font-size:14px">{{ $row['password'] }}</code></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <textarea id="passwordList" style="position:absolute;left:-9999px" readonly>@foreach($rows as $row){{ $row['name'] }} | {{ $row['email'] }} | {{ $row['password'] }}
@endforeach</textarea>
                        <p class="text-muted mb-0">Customers log in at the website's <b>Login</b> page with this email and password,
                            then see their invoices under My Profile &rarr; My Invoices. They can change the password there.</p>
                    @else
                        <div class="alert alert-info mb-0">Every CRM customer already has a password - nobody new to list.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-js')
    <script>
        (function () {
            var btn = document.getElementById('copyAllPasswords');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var text = document.getElementById('passwordList').value;
                var done = function () { btn.textContent = 'Copied'; };
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(done);
                } else {
                    var area = document.getElementById('passwordList');
                    area.select();
                    document.execCommand('copy');
                    done();
                }
            });
        })();
    </script>
@endsection
