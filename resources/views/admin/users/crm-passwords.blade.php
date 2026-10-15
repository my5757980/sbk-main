@extends('admin.layouts.app')

@section('css')
    {{-- Phones: each customer as a small card (the Password column sat off-screen, to the right of a
         table that had to be scrolled sideways to find it). --}}
    <style>
        @media (max-width: 575.98px) {
            .crm-pw-table thead { display: none; }
            .crm-pw-table, .crm-pw-table tbody, .crm-pw-table tr, .crm-pw-table td { display: block; width: 100%; }
            .crm-pw-table tr { padding: 10px 12px; border-bottom: 1px solid #ebe9f1; }
            .crm-pw-table td { display: flex; justify-content: space-between; align-items: baseline; gap: 12px;
                padding: 4px 0; border: 0; text-align: right; overflow-wrap: anywhere; }
            .crm-pw-table td::before { content: attr(data-label); flex: 0 0 auto; color: #6e6b7b; font-weight: 600; text-align: left; }
        }
    </style>
@endsection

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
                            <table class="table table-striped crm-pw-table">
                                <thead>
                                    <tr><th>#</th><th>Customer</th><th>Email (login)</th><th>Phone</th><th>Password</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $n => $row)
                                        <tr>
                                            <td data-label="#">{{ $n + 1 }}</td>
                                            <td data-label="Customer"><a href="{{ route('admin.users.show', $row['id']) }}">{{ $row['name'] }}</a></td>
                                            <td data-label="Email (login)">{{ $row['email'] }}</td>
                                            <td data-label="Phone">{{ $row['phone'] ?: '-' }}</td>
                                            <td data-label="Password"><code style="font-size:14px">{{ $row['password'] }}</code></td>
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
