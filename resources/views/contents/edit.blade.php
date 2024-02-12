@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Contents</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('contents')}}">Contents List</a></li>
                        <li class="breadcrumb-item active">Edit Content</li>
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
                            <h3 class="card-title">Edit Content</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['route' => ['contents.update', $data->content_id], 'method' => 'PATCH']) !!}
                            <p><span class="required_field">*</span> Required fields.</p>

                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="old_page" value="{{$data->page_id}}">
                                    <input type="hidden" name="old_section" value="{{$data->section_id}}">
                                    <input type="hidden" name="old_subtitle" value="{{$data->subtitle}}">
                                    <input type="hidden" name="old_content" value="{{$data->content}}">
                                    <input type="hidden" name="old_image" value="{{$data->image}}">

                                    <div class="form-group">
                                        <label for="page"><span class="required_field">*</span> Page</label>
                                        <select name="page" id="page" class="form-control {{$errors->has('page') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Page</option>
                                            @foreach($pages as $page)
                                                <option value="{{$page->page_id}}" {{$data->page_id == $page->page_id ? 'selected' : ''}}>{{$page->display_name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('page'))
                                            <span class="error invalid-feedback">{{$errors->first('page')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="section">Section</label>
                                        <select name="section" id="section" class="form-control {{$errors->has('section') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Section</option>
                                            @foreach($sections as $section)
                                                <option value="{{$section->section_id}}" {{$data->section_id == $section->section_id ? 'selected' : ''}}>{{$section->display_name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('section'))
                                            <span class="error invalid-feedback">{{$errors->first('section')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="subtitle">Subtitle</label>
                                        <input type="text" class="form-control{{ $errors->has('subtitle') ? ' is-invalid' : '' }}" name="subtitle" value="{{$data->subtitle}}">
                                        @if ($errors->has('subtitle'))
                                            <span class="error invalid-feedback">{{$errors->first('subtitle')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="content"><span class="required_field">*</span> Content</label>
                                        <textarea name="content" class="textarea form-control {{$errors->has('content') ? 'is-invalid' : ''}}">{{$data->content}}</textarea>
                                        @if ($errors->has('content'))
                                            <span class="error invalid-feedback">{{$errors->first('content')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="image">Image <i>(Upload new image to replace old image)</i></label>
                                        <input type="file" name="image" class="form-control {{$errors->has('image') ? 'is-invalid' : ''}}" value="{{old('image')}}">
                                        @if ($errors->has('image'))
                                            <span class="error invalid-feedback">{{$errors->first('image')}}</span>
                                        @endif
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-success" style="float: right; margin-top: 30px;"><i class="fa fa-check"></i> Save Changes</button>
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
@endsection

@push('scripts')
    @include('contents.scripts')
@endpush
