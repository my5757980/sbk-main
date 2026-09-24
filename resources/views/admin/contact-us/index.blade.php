@extends('admin.layouts.app')

@section('content')
    <section id="column-selectors">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Contact Us </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table table-striped datatab">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $result)
                                            <tr>
                                                <td>{!! trim($result->first_name . ' ' . $result->last_name) !!}</td>
                                                <td>{!! $result->email !!}</td>
                                                <td>{!! $result->created_at->format('d-m-Y H:i:s') !!}</td>
                                                <td>
                                                    @adminCan('contact-us.show')
                                                        <a href="{!! route('admin.contact-us.show', $result->id) !!}"
                                                            class="btn btn-info btn-sm waves-effect waves-light"><i
                                                                class="feather icon-search"></i></a>
                                                    @endadminCan
                                                    @adminCan('contact-us.destroy')
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm waves-effect waves-light"
                                                            onclick="deleteConfirmation({!! $result->id !!})"><i
                                                                class="feather icon-trash"></i></button>

                                                        <form action="{!! URL::route('admin.contact-us.destroy', $result->id) !!}" method="POST"
                                                            id="deleteForm{!! $result->id !!}">
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
