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
    	}
    	h2 {
    		font-size: 35px;
    		color: #00aebd;
    	}
    	p {
    		font-size: 20px;
    	}
    </style>
</head>
<body>
	<div class="main">
		<div class="container-fluid">
			<h1 class="text-center mt-5">OOPS...</h1>
			<h2 class="text-center">SOMETHING WENT WRONG HERE</h2>
			<p class="text-center mb-5">The link you followed probably broken <br> or the page has been removed.</p>
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