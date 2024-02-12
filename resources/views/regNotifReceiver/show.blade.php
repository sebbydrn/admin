@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Registration Notification Receivers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('reg_notif_receivers')}}">Registration Notification Receivers List</a></li>
                        <li class="breadcrumb-item active">View Registration Notification Receiver</li>
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
                            <h3 class="card-title">View Registration Notification Receiver</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{$user->fullname}}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{$user->email}}</td>
                                    </tr>
                                    <tr>
                                        <th>Receive Type</th>
                                        <td>
                                            @if($receiver->receive_type == 1)
                                                Main Recipient
                                            @elseif($receiver->receive_type == 2)
                                                Carbon Copy
                                            @elseif($receiver->receive_type == 3)
                                                Blind Carbon Copy
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date Created</th>
                                        @if($receiver_created)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($receiver_created->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th>Last Update</th>
                                        @if($receiver_updated)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($receiver_updated->timestamp))}}</td>
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
