<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>RSIS Admin</title>

        <link rel="shortcut icon" href="{{url("/").'/public/images/favicon.ico'}}" type="image/x-icon">

        @include('layouts.cssLinks')

        @stack('styles')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
        <div class="wrapper">
            @include('layouts.navbar')

            @include('layouts.sidebar')

            <div class="content-wrapper">
                <div class="content">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
            </div>

            @include('layouts.footer')
        </div>

        @include('layouts.lockscreen')

        @include('layouts.jsLinks')

        @stack('scripts')
    </body>
</html>
