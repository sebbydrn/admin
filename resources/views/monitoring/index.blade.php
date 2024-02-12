@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Monitoring</h1>
                </div>
            </div>
        </div>
    </section>
    {{-- End of content header --}}

    {{-- Filtering Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class=" card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Filtering</h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="table_filters">
                                <div class="col-md-4">
                                    <label>System</label>
                                    <select class="form-control" id="system">
                                        <option value="0" selected>Select System</option>
                                        @foreach($systems as $system)
                                            <option value="{{$system->system_id}}">{{$system->display_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Activity</label>
                                    <select class="form-control" id="activity">
                                        <option value="none" selected>Select Activity</option>
                                        @foreach($activities as $activity)
                                            <option value="{{$activity}}">{{$activity}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>User</label>
                                    <select class="form-control" id="user">
                                        <option value="0" selected>Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->user_id}}">{{$user->firstname.' '.$user->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Date From</label>
                                    <input type="text" class="form-control date_from" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Date To</label>
                                    <input type="text" class="form-control date_to"  readonly>
                                </div>
                                <div class="col-md-12 mt-1">
                                    <button type="button" class="btn btn-primary" id="filter">
                                        <i class="fa fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="reset">
                                        Reset Dates
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Admin Monitoring</h3>
                        </div>
                        <div class="card-body">

                            <table class="table table-bordered table-striped" id="monitoring_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">System</th>
                                        <th style="width: 20%;">Activity</th>
                                        <th style="width: 20%;">User</th>
                                        <th style="width: 10%;">Device</th>
                                        <th style="width: 10%;">Browser</th>
                                        <th style="width: 20%;">Date</th>
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
    @include('monitoring.scripts')
@endpush
