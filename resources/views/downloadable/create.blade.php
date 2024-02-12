@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Downloadables</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('downloadables')}}">Downloadables List</a></li>
                        <li class="breadcrumb-item active">Add New Downloadable</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    {{-- End of content header --}}
	
	{{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Downloadable</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['method' => 'POST', 'route' => 'downloadables.store', 'enctype' => 'multipart/form-data']) !!}
                            <p><span class="required_field">*</span> Required fields.</p>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="downloadable_category"><span class="required_field">*</span> Downloadable Category</label>
                                        <select name="downloadable_category" id="downloadable_category" class="form-control {{$errors->has('downloadable_category') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Dowloadable Category</option>
                                            @foreach($downloadableCategories as $downloadableCategory)
                                                <option value="{{$downloadableCategory->downloadable_category_id}}">{{$downloadableCategory->display_name}}</option>
                                            @endforeach         
                                        </select>
                                        @if ($errors->has('downloadable_category'))
                                            <span class="error invalid-feedback">{{$errors->first('downloadable_category')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="display_name"><span class="required_field">*</span> Display Name</label>
                                        <input type="text" class="form-control{{ $errors->has('display_name') ? ' is-invalid' : '' }}" name="display_name" value="{{old('display_name')}}">
                                        @if ($errors->has('display_name'))
                                            <span class="error invalid-feedback">{{$errors->first('display_name')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="version"><span class="required_field">*</span> Version</label>
                                        <input type="text" class="form-control{{ $errors->has('version') ? ' is-invalid' : '' }}" name="version" value="{{old('version')}}">
                                        @if ($errors->has('version'))
                                            <span class="error invalid-feedback">{{$errors->first('version')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="link">Link <small>(Only fill-up link if using an external link as download link)</small></label>
                                        <input type="text" class="form-control{{ $errors->has('link') ? ' is-invalid' : '' }}" name="link" value="{{old('link')}}">
                                        @if ($errors->has('link'))
                                            <span class="error invalid-feedback">{{$errors->first('link')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="uploadFile">Upload File</label>
                                        <input type="file" name="uploadFile" class="form-control {{$errors->has('uploadFile') ? 'is-invalid' : ''}}" value="{{old('uploadFile')}}">
                                        @if ($errors->has('uploadFile'))
                                            <span class="error invalid-feedback">{{$errors->first('uploadFile')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                    	<label for="is_public"><span class="required_field">*</span> Publicly Available?</label>
                                        <div class="form-check">
                                            <input type="radio" name="is_public" value="1" class="form-check-input" {{(old('is_public') == "1") ? 'checked' : ''}} {{(old('is_public') == "") ? 'checked' : ''}}>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="is_public" value="0" class="form-check-input" {{(old('is_public') == "0") ? 'checked' : ''}}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="affiliationAccess">Affiliation Access <small>(Fill-up if file will not be publicly available)</small></label>
                                        <select multiple class="form-control select2 {{($errors->has('affiliation_access')) ? 'is-invalid' : ''}}" name="affiliation_access[]">
                                            @foreach($affiliations as $affiliation)
                                                <option value="{{$affiliation->affiliation_id}}">{{$affiliation->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('affiliation_access'))
                                            <span class="error invalid-feedback">{{$errors->first('affiliation_access')}}</span>
                                        @endif
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-success" style="float: right; margin-top: 30px;"><i class="fa fa-check"></i> Submit</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                            {{-- End Form --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
	{{-- End of main content --}}
@endsection

@push('scripts')
    @include('downloadable.scripts')
@endpush