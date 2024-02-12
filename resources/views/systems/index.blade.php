@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Systems</h1>
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
                            <h3 class="card-title">Systems List</h3>
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
                            
                            @permission('add_system')
                            <a href="{{route('systems.create')}}" class="btn btn-primary" style="margin-bottom: 15px;"><i class="fa fa-plus-circle"></i> Add New System</a>
                            @endpermission
                            
                            <table class="table table-bordered table-striped" id="systems_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Name</th>
                                        <th style="width: 40%;">Description</th>
                                        <th style="width: 20%;">Actions</th>
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
    @include('systems.scripts')
@endpush