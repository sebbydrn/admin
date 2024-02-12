@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sliders</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('sliders')}}">Sliders List</a></li>
                        <li class="breadcrumb-item active">View Slider</li>
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
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Slider</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Name</th>
                                        <td style="width: 70%;">{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Link</th>
                                        <td>{{$data->link}}</td>
                                    </tr>
                                    <tr>
                                        <th>Image</th>
                                        <td>
                                            @if($_SERVER['SERVER_NAME'] == "stagingdev.philrice.gov.ph")
                                                <img src="{{'https://'.$_SERVER['SERVER_NAME'].'/rsis/portal/public/uploads/'.$data->image}}" style="height: 250px;">
                                            @elseif($_SERVER['SERVER_NAME'] == "rsis.philrice.gov.ph")
                                                <img src="{{'https://'.$_SERVER['SERVER_NAME'].'/portal/public/uploads/'.$data->image}}" style="height: 250px;">
                                            @else
                                                <img src="{{'http://'.$_SERVER['SERVER_NAME'].'/portal/public/uploads/'.$data->image}}" style="height: 250px;">
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
