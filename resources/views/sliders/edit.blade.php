@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sliders</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('sliders')}}">Sliders List</a></li>
                        <li class="breadcrumb-item active">Edit Slider</li>
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
                            <h3 class="card-title">Edit Slider</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['route' => ['sliders.update', $data->slider_id], 'method' => 'PATCH']) !!}
                            <p><span class="required_field">*</span> Required fields.</p>

                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="old_name" value="{{$data->name}}">
                                    <input type="hidden" name="old_link" value="{{$data->link}}">
                                    <input type="hidden" name="old_image" value="{{$data->image}}">

                                    <div class="form-group">
                                        <label for="name"><span class="required_field">*</span> Name</label>
                                        <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{$data->name}}">
                                        @if ($errors->has('name'))
                                            <span class="error invalid-feedback">{{$errors->first('name')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="link">Link</label>
                                        <input type="text" class="form-control{{ $errors->has('link') ? ' is-invalid' : '' }}" name="link" value="{{$data->link}}">
                                        @if ($errors->has('link'))
                                            <span class="error invalid-feedback">{{$errors->first('link')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="image"><span class="required_field">*</span> Image <i>(Upload new image to replace old image)</i></label>
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
    @include('sliders.scripts')
@endpush
