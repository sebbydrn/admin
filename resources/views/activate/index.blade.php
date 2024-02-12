@extends('layouts.activateAccount')

@section('content')
    <div class="login-box" style="margin-top: -150px;">
        <div class="login-logo">
            <a href="#"><img src="{{'../public/images/logo4.png'}}" alt="" style="height: 250px;"><a>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Add password to activate your account</p>
                
                @if ($errors->has('password'))
                <p style="color: red;">{{$errors->first('password')}}</p>
                @endif

                {!! Form::open(['route' => ['activate_account.update', request()->segment(count(request()->segments()))], 'method' => 'PUT']) !!}

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-success btn-block">Submit</button>
                        </div>
                        <!-- /.col -->
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection