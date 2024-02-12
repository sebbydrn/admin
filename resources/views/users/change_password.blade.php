@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('users')}}">Users List</a></li>
                        <li class="breadcrumb-item active">Change Password</li>
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
                            <h3 class="card-title">Change Password</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['method' => 'POST', 'route' => 'users.change_password.store']) !!}
                            <div class="row">
                                <div class="col-lg-12">
                                    <p><span class="required_field">*</span> Required fields</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="user_id" value="{{$user_id}}">

                                    <div class="form-group">
                                        <label for="password"><span class="required_field">*</span> New Password</label>
                                        <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="" placeholder="Enter your password">
                                        @if ($errors->has('password'))
                                            <span class="error invalid-feedback">{{$errors->first('password')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation"><span class="required_field">*</span> Confirm New Password</label>
                                        <input type="password" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" value="" placeholder="Enter your password">
                                        @if ($errors->has('password_confirmation'))
                                            <span class="error invalid-feedback">{{$errors->first('password_confirmation')}}</span>
                                        @endif
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-success" style="float: right; margin-top: 30px;"><i class="fa fa-check"></i> Save New Password</button>
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- End of main content --}}
@endsection

@push('scripts')
    @include('users.scripts')
@endpush