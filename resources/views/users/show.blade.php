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
                        <li class="breadcrumb-item active">View User</li>
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
                            <h3 class="card-title">User Data</h3>
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
                                    {{-- <tr>
                                        <th>Birthday</th>
                                        <td>{{date('F d, Y', strtotime($user_data->birthday))}}</td>
                                    </tr> --}}
                                    <tr>
                                        <th>Age</th>
                                        <td>{{$user_data->age}}</td>
                                    </tr>
                                    <tr>
                                        <th>Sex</th>
                                        <td>{{$user_data->sex}}</td>
                                    </tr>
                                    <tr>
                                        <th>Contact no.</th>
                                        <td>{{$user_data->contact_no}}</td>
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
                                    {{-- <tr>
                                        <th>Country</th>
                                        <td>{{$user_country}}</td>
                                    </tr> --}}
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
                                    @if($user_affiliation->affiliation_id == 3 || $user_affiliation->affiliation_id == 9)
                                    <tr>
                                        <th>Accreditation No.</th>
                                        <td>{{$user_data->accreditation_no}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Date User Created</th>
                                        @if($user_created)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($user_created->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th>Last User Update</th>
                                        @if($user_updated)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($user_updated->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
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
