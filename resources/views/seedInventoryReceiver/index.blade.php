@extends('layouts.index')

@push('styles')
    <style>
        .action_buttons {
            margin-bottom: 5px;
        }
    </style>
@endpush

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Seed Inventory Receivers</h1>
                </div>
            </div>
        </div>
    </section>
    {{-- End of content header --}}
	
	{{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Seed Inventory Receivers List</h3>
                        </div>
                        <div class="card-body">
                            @if($message = Session::get('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
                                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                                    {{$message}}
                                </div>
                            @endif

                            @if($message = Session::get('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
                                    <h5><i class="icon fas fa-ban"></i> Oops!</h5>
                                    {{$message}}
                                </div>
                            @endif
                            
                            @permission('add_data_compliance_receivers')
                            <a href="{{route('seed_inventory_receivers.create')}}" class="btn btn-primary" style="margin-bottom: 15px;"><i class="fa fa-plus-circle"></i> Add New Seed Inventory Receiver Receiver</a>
                            @endpermission
                            
                            <table class="table table-bordered table-striped" id="seed_inventory_receivers_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Email</th>
                                        <th style="width: 40%;">Receive Type</th>
                                        <th style="width: 20%">Actions</th>
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
    {{-- End of main content --}}
@endsection

@push('scripts')
    @include('seedInventoryReceiver.scripts')
@endpush