@extends('admin.layouts.app')

@section('content')
<section id="column-selectors">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Media Files <i class="feather icon-film"></i></h4>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table table-striped dataex-html5-selectors">
                                <thead>
                                    <tr>
                                        <th>Alternate Name</th>
                                        <th>Image</th>
                                        <th>Caption</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach ($data as $key => $media)
	                                    <tr>
	                                        <td>{!! $media->alt_text !!}</td>
	                                        <td>
	                                        	@if ($media->filename != '' && file_exists(uploadsDir('front') . $media->filename))
	                                        		<img src="{!! asset(uploadsDir('front') . $media->filename) !!}" height="80" width="80">
	                                        	@endif
	                                        </td>
	                                        <td>{!! $media->caption !!}</td>
	                                        <td>
                                                @adminCan('media-files.show')
	                                        	    <a href="{!! route('admin.media-files.show', $media->id) !!}" class="btn btn-info btn-sm waves-effect waves-light"><i class="feather icon-search"></i></a>
                                                @endadminCan

                                                @adminCan('media-files.edit')
	                                        	    <a href="{!! route('admin.media-files.edit', $media->id) !!}" class="btn btn-primary btn-sm waves-effect waves-light"><i class="feather icon-edit"></i></a>
                                                @endadminCan

                                                @adminCan('media-files.destroy')
	                                        	    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="deleteConfirmation({!! $media->id !!})"><i class="feather icon-trash"></i></button>

	                                        	    <form action="{!! URL::route('admin.media-files.destroy', $media->id) !!}" method="POST" id="deleteForm{!! $media->id !!}">
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
