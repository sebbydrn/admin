@extends('layouts.index')

@section('content')
	{{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>API</h1>
                </div>
            </div>
        </div>
    </section>
	{{-- Main Content --}}
    <div class="row">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <p class="h3">Filter</p>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Api</label>
                                        <select class="form-control" id="api_name">
                                            <option value="0" selected>Select Api</option>
                                            <option value="sg">Seed Grower</option>
                                            <option value="sc">Seed Cooperative</option>
                                            <option value="st">Seed Testing</option>
                                            <option value="spi">Seed Preliminary Inspection</option>
                                            <option value="sfi">Seed Final Inspection</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Date From</label>
                                        <input type="text" class="form-control date_from" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Date To</label>
                                        <input type="text" class="form-control date_to"  readonly>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                    <button type="button" class="btn btn-primary" id="filter">
                                        <i class="fa fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="reset">
                                        Reset Dates
                                    </button>
                                </div>
                        </div>
                        <div class="card-body">
                            {{-- @permission('add_api') --}}
                            {{-- <button class="btn btn-primary add_api" style="margin-bottom: 15px;" data-toggle="modal" data-target="#apiModal"><i class="fa fa-plus-circle"></i> Add New API</button> --}}
                            {{-- @endpermission --}}
                            <table class="table table-bordered table-striped" id="api_tables" style="width: 100%;"> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body" style="padding: 0;">
                    <div id="active_chart" style="height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
	@include('api.script')
    <script>
      @if (count($errors) > 0)
        $('#apiModal').modal('show');
      @endif
    </script>
@endpush