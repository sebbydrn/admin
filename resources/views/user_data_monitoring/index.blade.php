@extends('layouts.index')

@section('content')
	{{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Monitoring For User Registration</h1>
                </div>
            </div>
        </div>
    </section>
    {{-- End of content header --}}

    {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="dailyDataReceived" style="font-size: 3.5rem">{{$registrationDataCountDaily}}</h3>
                            <p>Data Received Today ({{date('F d, Y')}})</p>

                            {{-- <div class="row">
                                <div class="col-6">
                                    <span>Actual Data: <strong id="dailyActualDataReceived" style="font-size: 25px; margin-left: 5px;">{{$growAppActualDailyDataCount}}</strong></span>
                                </div>
                                <div class="col-6">
                                    <span>Test Data: <strong id="dailyTestDataReceived" style="font-size: 25px; margin-left: 5px;">{{$growAppTestDailyDataCount}}</strong></span>
                                </div>
                            </div> --}}
                        </div>
                        <div class="icon">
                            <i class="ion ion-document"></i>
                        </div>
                    </div>
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="totalDataReceived" style="font-size: 3.5rem">{{$registrationDataCount}}</h3>
                            <p>Total Data Received</p>

                            {{-- <div class="row">
                                <div class="col-6">
                                    <span>Actual Data: <strong id="totalActualDataReceived" style="font-size: 25px; margin-left: 5px;">{{$growAppActualDataCount}}</strong></span>
                                </div>
                                <div class="col-6">
                                    <span>Test Data: <strong id="totalTestDataReceived" style="font-size: 25px; margin-left: 5px;">{{$growAppTestDataCount}}</strong></span>
                                </div>
                            </div> --}}
                        </div>
                        <div class="icon">
                            <i class="ion ion-document"></i>
                        </div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="card card-success">
                        <div class="card-body" id="dailyData" style="background-color: #000; color: #fff; min-height: 370px; font-family: 'Courier New', Courier, monospace; font-size: 12px;">
                            <strong>Data Log Today: {{date('F d, Y')}}</strong> <hr style="background-color: #fff" class="mt-0 mb-0" />
                            @if($registrationDataCountDaily == 0)
                                <span style="color: red;">--No Data Received--</span>
                            @else
                                @foreach($registrationData as $item)
                                    <span style="color: #28a745;">[ {{date('Y-m-d H:i:s', strtotime($item['timestamp']))}} ]</span> Data: [ Fullname:"{{$item['fullname']}}";Username:"{{$item['username']}}";Email:"{{$item['username']}}";Affiliation:"{{$item['affiliation']}}" ]</span> <br />
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header" style="background-color: #007bff; color: #fff;">
                            <h3 class="card-title">All Data Received</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="input-group date" id="dateStart" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input" id="dateStartInput" data-target="#dateStart" placeholder="Date Start">
                                                <div class="input-group-append" data-target="#dateStart" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="far fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="input-group date" id="dateEnd" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input" id="dateEndInput" data-target="#dateEnd" placeholder="Date End">
                                                <div class="input-group-append" data-target="#dateEnd" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="far fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-default" onclick="filterTable()"><i class="fa fa-filter"></i> Filter</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary" onclick="refreshTable()" style="float: right;"><i class="fa fa-sync-alt"></i> Refresh Table</button>
                                </div>
                            </div>

                            <table class="table table-bordered table-striped" id="allDataTable">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Fullname</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Affiliation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>
@endsection

@push('scripts')
    @include('user_data_monitoring.scripts')
@endpush