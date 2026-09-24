@extends('admin.layouts.app')

@section('content')
    <section id="number-tabs">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{!! $form['heading'] !!}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form method="POST" action="{!! $form['action'] !!}" class="number-tab-steps wizard-circle"
                                enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                @csrf
                                <input type="hidden" name="_method" value="{!! $form['method'] !!}">
                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="question">Question *</label>
                                                <input type="text" id="question" name="question"
                                                    value="{{ old('question', $data->question) }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="answer">Answer *</label>
                                                <textarea type="text" name="answer" maxlength="190" class="form-control editor-tinymce">{{ old('answer', $data->answer) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    @foreach (pageStatuses() as $val => $label)
                                                        <option value="{!! $val !!}" {!! matchSelected($val, $data->status ?? '') !!}>
                                                            {!! $label !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Save
                                        Changes</button>
                                    <a href="{!! $form['cancel_url'] !!}"
                                        class="btn btn-danger mr-1 mb-1 waves-effect waves-light"
                                        style="color: white">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
