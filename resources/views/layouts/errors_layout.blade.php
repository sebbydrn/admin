<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>RSIS Admin</title>
	
	{{-- Bootstrap --}}
	<link rel="stylesheet" href="{{asset('public/assets/bootstrap/css/bootstrap.min.css')}}">

	{{-- Font awesome --}}
	<link rel="stylesheet" href="{{asset('public/assets/AdminLTE-3.0.0/plugins/fontawesome-free/css/all.min.css')}}">

	<style>
		.error_title {
			text-align: center;
			font-size: 40px;
		}

		.error_message {
			text-align: center;
			font-size: 25px;
		}
	</style>
</head>
<body>
	@yield('content')
	

	{{-- jQuery --}}
	<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/jquery/jquery.min.js')}}"></script>

	{{-- Boostrap --}}
	<script src="{{asset('public/assets/bootstrap/js/bootstrap.min.js')}}"></script>
</body>
</html>