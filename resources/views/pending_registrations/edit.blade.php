@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pending Registrations</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('pending_registrations')}}">Pending Registrations List</a></li>
                        <li class="breadcrumb-item active">Approve Pending Registration</li>
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
                            <h3 class="card-title">Approve Pending Registration</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Full Name</th>
                                        <td>{{$user_data->fullname}}</td>
                                    </tr>
                                    <tr>
                                        <th>First Name</th>
                                        <td>{{$user_data->firstname}}</td>
                                    </tr>
                                    <tr>
                                        <th>Middle Name</th>
                                        <td>{{$user_data->middlename}}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Name</th>
                                        <td>{{$user_data->lastname}}</td>
                                    </tr>
                                    <tr>
                                        <th>Extension Name</th>
                                        <td>{{$user_data->extname}}</td>
                                    </tr>
                                    <tr>
                                        <th>Birthday</th>
                                        <td>{{date('F d, Y', strtotime($user_data->birthday))}}</td>
                                    </tr>
                                    <tr>
                                        <th>Sex</th>
                                        <td>{{$user_data->sex}}</td>
                                    </tr>
                                    <tr>
                                        <th>E-mail Address</th>
                                        <td>{{$user_data->email}}</td>
                                    </tr>
                                    <tr>
                                        <th>Alternate E-mail Address</th>
                                        <td>{{$user_data->secondaryemail}}</td>
                                    </tr>
                                    <tr>
                                        <th>Username</th>
                                        <td>{{$user_data->username}}</td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td>{{$user_country}}</td>
                                    </tr>
                                    <tr>
                                        <th>Region</th>
                                        <td>{{$user_region}}</td>
                                    </tr>
                                    <tr>
                                        <th>Province</th>
                                        <td>{{$user_province}}</td>
                                    </tr>
                                    <tr>
                                        <th>Municipality</th>
                                        <td>{{$user_municipality}}</td>
                                    </tr>
                                    <tr>
                                        <th>Barangay</th>
                                        <td>{{$user_data->barangay}}</td>
                                    </tr>
                                    <tr>
                                        <th>Designation</th>
                                        <td>{{$user_data->designation}}</td>
                                    </tr>
                                    <tr>
                                        <th>Affiliation</th>
                                        <td>
                                            @if($user_affiliation != '')
                                                {{$user_affiliation->affiliation_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    @if($user_affiliation->affiliation_id == 1)
                                    <tr>
                                        <th>PhilRice Station</th>
                                        <td>
                                            @if($user_affiliation != '')
                                                {{$user_affiliation->station_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>PhilRice ID No.</th>
                                        <td>{{$user_data->philrice_idno}}</td>
                                    </tr>
                                    @endif
                                    @if($user_affiliation->affiliation_id == 3 || $user_affiliation->affiliation_id == 9)
                                    <tr>
                                        <th>Cooperative</th>
                                        <td>{{$user_data->cooperative}}</td>
                                    </tr>
                                    @endif
                                    @if($user_affiliation->affiliation_id == 6)
                                    <tr>
                                        <th>Agency</th>
                                        <td>{{$user_data->agency}}</td>
                                    </tr>
                                    @endif
                                    @if($user_affiliation->affiliation_id == 5)
                                    <tr>
                                        <th>University/ School</th>
                                        <td>{{$user_data->school}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Date User Registered</th>
                                        @if($user_created)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($user_created->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>

                            <br>

                            <h3>Assign User Role</h3>

                            {!! Form::open(['route' => ['pending_registrations.update', $user_data->user_id], 'method' => 'PATCH']) !!}

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

                            <div class="form-group">
                                <label for="with_email">With confirmation email?</label> <br>
                                <input type="checkbox" name="with_email" value="yes">&nbsp; Yes, send confirmation email
                            </div>
                             
                            <button type="submit" class="btn btn-success float-right"><i class="fa fa-check"></i> Approve Registration</button>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection