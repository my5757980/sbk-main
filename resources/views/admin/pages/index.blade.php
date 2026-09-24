@extends('admin.layouts.app')

@section('css')
    <style type="text/css">
        .table-responsive img {
            width: 100px !important;
            height: auto !important;
        }
    </style>
@endsection

@section('content')
<section id="column-selectors">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pages</h4>
{{--                    <span><a href="{{ route('admin.pages.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Page</a></span>--}}
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table table-striped" id="datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Page Title</th>
{{--                                        <th>Page Type</th>--}}
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach ($data as $key => $page)
	                                    <tr>
	                                        <td>{!! $page->id !!}</td>
	                                        <td>{!! Str::limit($page->page_title, 20, '...') !!}</td>
{{--	                                        <td>{!! strtoupper($page->type) !!}</td>--}}
                                            <td><span style="display: none">{!! strtotime($page->created_at) !!}</span>{!! date('d/m/Y H:i:A', strtotime($page->created_at)) !!}</td>
                                            <td><small><span class="badge badge-{!! $page->status == 'published' ? 'success' : 'danger' !!}">{!! strtoupper($page->status) !!}</span></small></td>
                                            <td>
                                                @adminCan('pages.show')
	                                        	    <a href="{!! route('admin.pages.show', $page->id) !!}" class="btn btn-info btn-sm waves-effect waves-light">
                                                        <i class="feather icon-search"></i>
                                                    </a>
                                                @endadminCan

                                                @adminCan('pages.edit')
	                                        	    <a href="{!! route('admin.pages.edit', $page->id) !!}" class="btn btn-primary btn-sm waves-effect waves-light">
                                                        <i class="feather icon-edit"></i>
                                                    </a>
                                                @endadminCan

                                                @adminCan('pages.destroy')
	                                        	    <button type="button" onclick="deleteConfirmation({!! $page->id !!})" class="btn btn-danger btn-sm waves-effect waves-light">
                                                        <i class="feather icon-trash"></i>
                                                    </button>

                                                    <form action="{!! URL::route('admin.pages.destroy', $page->id) !!}" method="POST" id="deleteForm{!! $page->id !!}">
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
<!-- Column selectors with Export Options and print table -->
@endsection

@section('footer-js')
    <script>
        $('#datatable').DataTable({
            "order": [[3, "desc"]] // Sort by the fourth column (created_at) in descending order
        });
    </script>
@endsection
