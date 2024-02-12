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
                        <li class="breadcrumb-item active">Add Roles</li>
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
                            <h3 class="card-title">Add Roles</h3>
                        </div>
                        <div class="card-body">
                        	@if($message = Session::get('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
                                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                                    {{$message}}
                                </div>
                            @endif

                            @if($message = Session::get('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
                                    <h5><i class="icon fas fa-ban"></i> Oops!</h5>
                                    {{$message}}
                                </div>
                            @endif

                        	{{-- Form --}}
                            {!! Form::open(['method' => 'POST', 'route' => 'users.roles.store']) !!}
                            <div class="row">
                                <div class="col-lg-12">
                                    <p><span class="required_field">*</span> Required fields</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <input type="hidden" name="user_id" value="{{$user_id}}">

                                    <div class="form-group">
                                        <label for="system"><span class="required_field">*</span> System</label>
                                        <select name="system" id="system" class="form-control {{$errors->has('system') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select System</option>
                                            @foreach($systems as $system)
                                                <option value="{{$system->system_id}}" {{old('system') == $system->system_id ? 'selected' : ''}}>{{$system->display_name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('system'))
                                            <span class="error invalid-feedback">{{$errors->first('system')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="role"><span class="required_field">*</span> Role</label>
                                        <select name="role" id="role" class="form-control {{$errors->has('role') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{$role->role_id}}" {{old('role') == $role->role_id ? 'selected' : ''}}>{{$role->display_name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('role'))
                                            <span class="error invalid-feedback">{{$errors->first('role')}}</span>
                                        @endif
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-success" style="float: right; margin-top: 30px;"><i class="fa fa-check"></i> Submit</button>

                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                	<div class="card card-primary">
                		<div class="card-header">
                			<h3 class="card-title">User Roles</h3>
                		</div>
                		<div class="card-body">
                			<table class="table table-bordered table-striped">
                				<thead>
                					<tr>
                						<th>Role</th>
                						<th>System</th>
                						<th>Actions</th>
                					</tr>
                				</thead>
                				<tbody>
                					@if($user_roles->isEmpty())
                						<tr>
                							<td colspan="3" style="text-align: center;">No data available in table</td>
                						</tr>
                					@endif
                					@foreach($user_roles as $user_role)
										<tr>
											<td>{{$user_role->display_name}}</td>
											<td>{{$user_role->system_display_name}}</td>
											<td><button class="btn btn-danger btn-sm" title="Delete" onclick="delete_user_role({{$user_role->user_role_system_id}}, {{$user_id}})"><i class="fa fa-trash-alt"></i> Delete</button></td>
										</tr>
                					@endforeach
                				</tbody>
                			</table>
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