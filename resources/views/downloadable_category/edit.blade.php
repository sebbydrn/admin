@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Downloadable Categories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('downloadable_categories')}}">Downloadable Categories List</a></li>
                        <li class="breadcrumb-item active">Edit Downloadable Category</li>
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
                            <h3 class="card-title">Edit Downloadable Category</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['route' => ['downloadable_categories.update', $data->downloadable_category_id], 'method' => 'PATCH']) !!}
                            <p><span class="required_field">*</span> Required fields.</p>

                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="old_display_name" value="{{$data->display_name}}">
                                    <input type="hidden" name="old_is_public" value="{{$data->is_public}}">

                                    <div class="form-group">
                                        <label for="display_name"><span class="required_field">*</span> Display Name</label>
                                        <input type="text" class="form-control{{ $errors->has('display_name') ? ' is-invalid' : '' }}" name="display_name" value="{{$data->display_name}}">
                                        @if ($errors->has('display_name'))
                                            <span class="error invalid-feedback">{{$errors->first('display_name')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="is_public"><span class="required_field">*</span> Publicly Available?</label>
                                        <div class="form-check">
                                            <input type="radio" name="is_public" value="1" class="form-check-input" {{($data->is_public == "1") ? 'checked' : ''}}>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="is_public" value="0" class="form-check-input" {{($data->is_public == "0") ? 'checked' : ''}}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-success" style="float: right; margin-top: 30px;"><i class="fa fa-check"></i> Save Changes</button>
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
@endsection

@push('scripts')
    @include('downloadable_category.scripts')
@endpush
