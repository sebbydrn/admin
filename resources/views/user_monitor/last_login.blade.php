@extends('layouts.index')

@section('content')
	{{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Users Last Login</h1>
                </div>
            </div>
        </div>
    </section>
    {{-- End of content header --}}

     {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
        	<div class="row">
        		<div class="col-12">
        			<div class="card card-primary">
		                <div class="card-header">
		                	<h3 class="card-title">Users Last Login List</h3>
		                </div>
		                <div class="card-body">
		                	<table class="table table-bordered table-striped" id="users_last_login_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Name</th>
                                        <th style="width: 25%;">Username</th>
                                        <th style="width: 25%;">E-mail Address</th>
                                        <th style="width: 25%;">Last User Login</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
		                </div>
		            </div>
        		</div>
        	</div>
        </div>
    </section>
@endsection

@push('scripts')

@endpush
