@extends('layouts.index')

@section('content')
    {{-- Filtering Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class=" card card-primary mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Filtering</h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="table_filters">
                                <div class="col-md-4">
                                    <label>System</label>
                                    <select class="form-control" id="system">
                                        <option value="0" selected>Select System</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Activity</label>
                                    <select class="form-control" id="activity">
                                        <option value="none" selected>Select Activity</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>User</label>
                                    <select class="form-control" id="user">
                                        <option value="0" selected>Select User</option>
                                    </select>
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
            <div class="card card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-3 col-lg-3">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon">
                                    <i class="far fa-bookmark"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Active
                                    </span>
                                    <span class="info-box-number">
                                        1,023
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon">
                                    <i class="far fa-bookmark"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Approve
                                    </span>
                                    <span class="info-box-number">
                                        1,023
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon">
                                    <i class="far fa-bookmark"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        pending
                                    </span>
                                    <span class="info-box-number">
                                        1,023
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon">
                                    <i class="far fa-bookmark"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Online
                                    </span>
                                    <span class="info-box-number">
                                        1,023
                                    </span>
                                </div>
                            </div>
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
