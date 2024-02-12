@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Partners</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('partners')}}">Partners List</a></li>
                        <li class="breadcrumb-item active">Add New Partner</li>
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
                            <h3 class="card-title">Add New Partner</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['method' => 'POST', 'route' => 'partners.store', 'enctype' => 'multipart/form-data']) !!}
                            <p><span class="required_field">*</span> Required fields.</p>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name"><span class="required_field">*</span> Name</label>
                                        <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{old('name')}}">
                                        @if ($errors->has('name'))
                                            <span class="error invalid-feedback">{{$errors->first('name')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="short_name"><span class="required_field">*</span> Short Name</label>
                                        <input type="text" class="form-control{{ $errors->has('short_name') ? ' is-invalid' : '' }}" name="short_name" value="{{old('short_name')}}">
                                        @if ($errors->has('short_name'))
                                            <span class="error invalid-feedback">{{$errors->first('short_name')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="description"><span class="required_field">*</span> Description</label>
                                        <textarea name="description" class="textarea form-control {{$errors->has('description') ? 'is-invalid' : ''}}"></textarea>
                                        @if ($errors->has('description'))
                                            <span class="error invalid-feedback">{{$errors->first('description')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="website">Website</label>
                                        <input type="text" class="form-control{{ $errors->has('website') ? ' is-invalid' : '' }}" name="website" value="{{old('website')}}">
                                        @if ($errors->has('website'))
                                            <span class="error invalid-feedback">{{$errors->first('website')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="logo">Logo</label>
                                        <input type="file" name="logo" class="form-control {{$errors->has('logo') ? 'is-invalid' : ''}}" value="{{old('logo')}}">
                                        @if ($errors->has('logo'))
                                            <span class="error invalid-feedback">{{$errors->first('logo')}}</span>
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
    @include('partners.scripts')
@endpush