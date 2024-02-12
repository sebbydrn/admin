@extends('layouts.index')

@push('styles')
    <style>
        .action_buttons {
            margin-bottom: 5px;
        }
    </style>
@endpush

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Users</h1>
                </div>
            </div>
        </div>
    </section>
    {{-- End of content header --}}

    {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fa fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Users</span>
                            <span class="info-box-number">{{$users}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fa fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Active Users</span>
                            <span class="info-box-number">{{$active_users}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fa fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Deactivated Users</span>
                            <span class="info-box-number">{{$deactivated_users}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Users</h3>
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
                            
                            @permission('add_user')
                            <a href="{{route('users.create')}}" class="btn btn-primary" style="margin-bottom: 15px;"><i class="fa fa-plus-circle"></i> Add New User</a>
                            @endpermission

                            <table class="table table-bordered table-striped" id="users_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Name</th>
                                        <th style="width: 15%;">Username</th>
                                        <th style="width: 15%;">E-mail Address</th>
                                        <th style="width: 15%;">Status</th>
                                        <th style="width: 15%;">Roles</th>
                                        <th style="width: 20%;">Actions</th>
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
    @include('users.scripts')
@endpush
