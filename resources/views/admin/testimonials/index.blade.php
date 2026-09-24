@extends('admin.layouts.app')

@section('content')
    <section id="column-selectors">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Testimonials</h4>
                        @adminCan('testimonial.create')
                            <span><a href="{{ route('admin.testimonial.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a></span>
                        @endadminCan

                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table table-striped" id="datatable">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($data as $key => $result)
                                        <tr>
                                            <td>{!! $result->name !!}</td>
                                            <td><span style="display: none">{!! strtotime($result->created_at) !!}</span>{!! date('d/m/Y H:i:A', strtotime($result->created_at)) !!}</td>
                                            <td><small><span class="badge badge-{!! $result->status == 'published' ? 'success' : 'danger' !!}">{!! strtoupper($result->status) !!}</span></small></td>
                                            <td>
{{--                                                <a href="{!! route('admin.testimonial.show', $result->id) !!}" class="btn btn-info btn-sm waves-effect waves-light"><i class="feather icon-search"></i></a>--}}

                                                @adminCan('testimonial.edit')
                                                    <a href="{!! route('admin.testimonial.edit', $result->id) !!}" class="btn btn-primary btn-sm waves-effect waves-light"><i class="feather icon-edit"></i></a>
                                                @endadminCan

                                                @adminCan('testimonial.destroy')
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="deleteConfirmation({!! $result->id !!})"><i class="feather icon-trash"></i></button>

                                                    <form action="{!! URL::route('admin.testimonial.destroy', $result->id) !!}" method="POST" id="deleteForm{!! $result->id !!}">
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
        $('#datatable').DataTable({
            "order": [[1, "desc"]] // Sort by the fourth column (created_at) in descending order
        });
    </script>
@endsection
