@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Roles</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('roles')}}">Roles List</a></li>
                        <li class="breadcrumb-item active">View Role</li>
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
                            <h3 class="card-title">Role</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Role Name</th>
                                        <td>{{$data->display_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{$data->description}}</td>
                                    </tr>
                                    <tr>
                                        <th>Permissions</th>
                                        <td>
                                            @foreach($permissions as $permission)
                                                <span class="badge badge-primary">{{$permission->display_name}}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date Created</th>
                                        @if($date_created)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($date_created->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th>Last Update</th>
                                        @if($date_updated)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($date_updated->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
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
