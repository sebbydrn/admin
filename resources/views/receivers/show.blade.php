@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Receivers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('receivers')}}">Receivers List</a></li>
                        <li class="breadcrumb-item active">View Receiver</li>
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
                            <h3 class="card-title">Receiver</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{$data['name']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Receive Type</th>
                                        <td>
                                            @if($data['receive_type'] == 1)
                                                Main Recipient
                                            @elseif($data['receive_type'] == 2)
                                                Carbon Copy
                                            @elseif($data['receive_type'] == 3)
                                                Blind Carbon Copy
                                            @endif
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
