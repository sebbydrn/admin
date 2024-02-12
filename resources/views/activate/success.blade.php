@extends('layouts.activateAccount')

@section('content')
    <div class="login-box" style="margin-top: -150px;">
        <div class="login-logo">
            <a href="#"><img src="{{'../public/images/logo4.png'}}" alt="" style="height: 250px;"><a>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p>Successfully added password. You can now log in.</p>
                <a href="https://stagingdev.philrice.gov.ph/rsis/portal">Click this link to log in</a>
            </div>
        </div>
    </div>
@endsection