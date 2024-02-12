<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, inital-scale=1.0">
	<title>Rice Seed Information System</title>

	<link rel="shortcut icon" href="{{url("/").'/public/images/favicon.ico'}}" type="image/x-icon">

	@include('errors.cssLinks')

    <style>
    	h1 {
    		font-size: 60px;
    		color: #246e48;
    	}
    	p {
    		font-size: 20px;
    	}
    </style>
</head>
<body>
	<div class="main">
		<div class="container-fluid">
			<div class="row">
				<div class="col text-center">
					<h1 class="text-center mt-5">We Are Sorry...</h1>
					<p class="text-center">The page you are trying to access has restricted access. <br> Please refer to your system administrator.</p>
					<br>
					<a href="{{url('/')}}" class="btn btn-danger mb-5">BACK TO HOME</a>
				</div>
			</div>
			{{-- Bottom --}}
		    <div class="row">
		        <div class="col-12 col_no_padding">
		            <img src="{{url("/").'/public/images/bottom.png'}}" alt="" class="img-responsive" style="width: 100%;">
		        </div>
		    </div>
		    {{-- End of bottom --}}
		</div>
	</div>

    @include('errors.jsLinks')
</body>
</html>