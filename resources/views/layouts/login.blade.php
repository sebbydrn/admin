<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>RSIS Admin |  Login</title>

        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('public/assets/AdminLTE-3.0.0/plugins/fontawesome-free/css/all.min.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{asset('public/assets/ionicons/ionicons.min.css')}}">
        <!-- icheck bootstrap -->
        <link rel="stylesheet" href="{{asset('public/assets/AdminLTE-3.0.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('public/assets/AdminLTE-3.0.0/dist/css/adminlte.min.css')}}">
        <!-- Google Font: Source Sans Pro -->
        <link href="{{asset('public/assets/fonts/sourcesanspro.css')}}" rel="stylesheet">
    </head>
    <body class="hold-transition login-page">
        @yield('content')

        <!-- jQuery -->
        <script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/jquery/jquery.min.js')}}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{asset('public/assets/AdminLTE-3.0.0/dist/js/adminlte.min.js')}}"></script>
    </body>
</html>
