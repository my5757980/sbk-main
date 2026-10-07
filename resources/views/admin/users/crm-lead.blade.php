@extends('admin.layouts.app')

{{-- A CRM customer with NO email in the CRM (app/Models/CrmLead): their whole CRM record,
     read live like an account's. There is no website login for them until the CRM has an email. --}}
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
                    <h4 class="card-title mb-0">
                        {{ $data->name ?: 'CRM customer' }}
                        <span class="badge badge-danger ml-1">CRM</span>
                        <span class="badge badge-warning">No email</span>
                    </h4>
                    <div class="d-flex flex-wrap" style="gap:8px">
                        <a href="{{ route('admin.users.show.crm-lead', ['lead' => $data->lead_id, 'refresh' => 1]) }}" class="btn btn-outline-secondary btn-sm">Refresh from CRM</a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        This customer has no email in the CRM, so there is no website login for them yet.
                        Add their email on their lead in the CRM: within 5 minutes they get a website account under Users,
                        with all these invoices, and their password can be set there.
                    </div>
                    <p class="text-muted" style="margin-bottom:14px">
                        CRM lead {{ $data->lead_id }}{{ $data->agent_email ? ' - agent ' . ($data->agent_name ?: $data->agent_email) : '' }}.
                        Last synced {{ $data->updated_at->diffForHumans() }}.
                    </p>
                    @include('partials.crm.record', ['crm' => $crm])
                </div>
            </div>
        </div>
    </div>
@endsection
