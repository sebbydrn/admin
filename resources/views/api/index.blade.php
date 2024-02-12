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
    {{-- <div class="row">
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
                            @permission('add_api')
                            <button class="btn btn-primary add_api" style="margin-bottom: 15px;" data-toggle="modal" data-target="#apiModal"><i class="fa fa-plus-circle"></i> Add New API</button>
                            @endpermission
                            <table class="table table-bordered table-striped" id="api_tables" style="width: 100%;"> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="col-12 col-sm-12 col-lg-12">
            <div class="card card-primary card-outline card-tabs">
              <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill" href="#api_monitoring" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Api Monitoring</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-profile-tab" data-toggle="pill" href="#email_recipient" role="tab" aria-controls="custom-tabs-two-profile" aria-selected="false">Email Recipient</a>
                  </li>
                  
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="api_monitoring" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
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
                            <div class="col-lg-2 col-md-2 col-sm-6 mb-1">
                                    <button type="button" class="btn btn-primary" id="filter">
                                        <i class="fa fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="reset">
                                        Reset Dates
                                    </button>
                                </div>
                                <table class="table table-bordered table-striped" id="api_tables" style="width: 100%;"> 
                            </table>
                  </div>
                  <div class="tab-pane fade" id="email_recipient" role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
                     <button class="btn btn-primary add_api" style="margin-bottom: 15px;" data-toggle="modal" data-target="#recipientModal"><i class="fa fa-plus-circle"></i> Add New Recipient</button>

                     <table class="table table-bordered table-striped" id="recipient_table" style="width: 100%;"> 
                            </table>
                  </div>
                  
                </div>
              </div>
              <!-- /.card -->
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

    {{-- Modal for viewing the API data --}}
    <div class="modal fade" id="viewApiModal" tabindex="-1" role="dialog" aria-labelledby="viewApiModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="viewApiModalLabel">View Details</h5>
          </div>
          <div class="modal-body">
                <table class="table table-bordered table-striped" id="apiViewTable" style="width:100%;">
                </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal for adding email recipient --}}
    <div class="modal fade" id="recipientModal" tabindex="-1" role="dialog" aria-labelledby="apiModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="apiModalLabel">New Recipient</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          {!! Form::open(['method' => 'POST', 'route' => 'email_recipient.store']) !!}
          <div class="modal-body">
                <p><span class="required_field">*</span> Required fields.</p>
                <div class="col-md-12">
                    <p class="preview"></p>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="email"><span class="required_field">*</span> E-mail Address</label>
                        <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="email">
                        @if ($errors->has('domain_ip'))
                            <span class="error invalid-feedback">{{$errors->first('provider')}}</span>
                        @endif
                    </div>
                </div>
                {{-- <div class="col-md-12">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" name="is_programmer" value="1" class="custom-control-input" id="customSwitch1">
                          <label class="custom-control-label" for="customSwitch1">is Programmer/Developer?</label>
                        </div>
                    </div>
                </div> --}}
                

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" onsubmit="return false;" class="btn btn-success">Save changes</button>
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>

    {{-- Modal for adding new api link and server --}}
    {{-- <div class="modal fade" id="apiModal" tabindex="-1" role="dialog" aria-labelledby="apiModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="apiModalLabel">Add new API</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          {!! Form::open(['method' => 'POST', 'route' => 'api.store']) !!}
          <div class="modal-body">
                <p><span class="required_field">*</span> Required fields.</p>
                <div class="col-md-12">
                    <p class="preview"></p>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="domain_ip"><span class="required_field">*</span> Provider</label>
                        <input type="text" class="form-control {{ $errors->has('provider') ? ' is-invalid' : '' }}" name="provider" placeholder="e.g. wwww.google.com">
                        @if ($errors->has('domain_ip'))
                            <span class="error invalid-feedback">{{$errors->first('provider')}}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="domain_ip"><span class="required_field">*</span> Domain Name</label>
                        <input type="text" class="form-control {{ $errors->has('domain_ip') ? ' is-invalid' : '' }}" name="domain_ip" placeholder="e.g. wwww.google.com">
                        @if ($errors->has('domain_ip'))
                            <span class="error invalid-feedback">{{$errors->first('domain_ip')}}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="link_address"><span class="required_field">*</span> Domain Path</label>
                        <input type="text" class="form-control {{ $errors->has('link_address') ? ' is-invalid' : '' }}" name="link_address" placeholder="e.g. /getAllPeople">
                        @if ($errors->has('link_address'))
                            <span class="error invalid-feedback">{{$errors->first('link_address')}}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="link_address"><span class="required_field">*</span> Category</label>
                        <input type="text" class="form-control {{ $errors->has('category') ? ' is-invalid' : '' }}" name="category" placeholder="e.g. peoplelist">
                        @if ($errors->has('category'))
                            <span class="error invalid-feedback">{{$errors->first('category')}}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" name="isSSL" value="1" class="custom-control-input" id="customSwitch1">
                          <label class="custom-control-label" for="customSwitch1">is SSL?</label>
                        </div>
                    </div>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" onsubmit="return false;" class="btn btn-success">Save changes</button>
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div> --}}
@endsection

@push('scripts')
	@include('api.script')
    <script>
      @if (count($errors) > 0)
        $('#apiModal').modal('show');
      @endif
    </script>
@endpush