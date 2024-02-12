@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Seed Inventory Receivers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('data_compliance_receivers')}}">Seed Inventory Receivers List</a></li>
                        <li class="breadcrumb-item active">Add New Data Compliance Receiver</li>
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
                            <h3 class="card-title">Add New Seed Inventory Receiver</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['method' => 'POST', 'route' => 'seed_inventory_receivers.store']) !!}
                            <p><span class="required_field">*</span> Required fields.</p>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="email"><span class="required_field">*</span> Email</label>
                                        <input autocomplete="off" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="email">
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback d-block">{{$errors->first('email')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="receive_type"><span class="required_field">*</span> Receive Type</label>
                                        <select name="receive_type" id="receive_type" class="form-control {{$errors->has('receive_type') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Receive Type</option>
                                            <option value="1">Main Recipient</option>
                                            <option value="2">Carbon Copy</option>
                                            <option value="3">Blind Carbon Copy</option>          
                                        </select>
                                        @if ($errors->has('receive_type'))
                                            <span class="error invalid-feedback">{{$errors->first('receive_type')}}</span>
                                        @endif
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-success" style="float: right; margin-top: 30px;"><i class="fa fa-check"></i> Submit</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                            {{-- End Form --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
	{{-- End of main content --}}
@endsection

@push('scripts')
    @include('seedInventoryReceiver.scripts')
@endpush