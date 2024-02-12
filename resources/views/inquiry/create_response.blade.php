@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Inquiries</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('inquiries')}}">Inquiries List</a></li>
                        <li class="breadcrumb-item active">Create Response</li>
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
                            <h3 class="card-title">Inquiry</h3>
                        </div>
                        <div class="card-body">
                            <label for="sender">Sender</label>
                            <p id="sender">{{$inquiry['sender']}}</p>

                            <label for="inquiry">Inquiry</label>
                            <p id="inquiry">{{$inquiry['inquiry']}}</p>
                        </div>    
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Create Response</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['method' => 'POST', 'route' => 'inquiries.send_response']) !!}
                            <p><span class="required_field">*</span> Required fields.</p>

                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="inquiry_id" value="{{$inquiry['inquiry_id']}}">

                                    <div class="form-group">
                                        <label for="title"><span class="required_field">*</span> Title</label>
                                        <input type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{old('title')}}">
                                        @if ($errors->has('title'))
                                            <span class="error invalid-feedback">{{$errors->first('title')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="body"><span class="required_field">*</span> Body</label>
                                        <textarea name="body" class="textarea form-control {{$errors->has('body') ? 'is-invalid' : ''}}"></textarea>
                                        @if ($errors->has('body'))
                                            <span class="error invalid-feedback">{{$errors->first('body')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" value="rsis.bpi.philrice@gmail.com" readonly>
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-success" style="float: right; margin-top: 30px;"><i class="fa fa-paper-plane"></i> Send</button>
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
    @include('inquiry.scripts')
@endpush