@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Roles</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('roles')}}">Roles List</a></li>
                        <li class="breadcrumb-item active">Edit Role</li>
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
                            <h3 class="card-title">Edit Role</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['method' => 'PUT', 'route' => ['roles.update', $data->role_id], 'name' => 'rolesupdateform']) !!}
                            <div class="row">
                                <div class="col-lg-12">
                                    <p><span class="required_field">*</span> Required fields</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>

                                    <input type="hidden" name="old_display_name" value="{{$data->display_name}}">
                                    <input type="hidden" name="old_name" value="{{$data->name}}">
                                    <input type="hidden" name="old_description" value="{{$data->description}}">
                                    <?php
                                        $old_permissions = implode (",", $role_permissions_array);;
                                    ?>
                                    <input type="hidden" name="old_permissions" value="{{$old_permissions}}">

                                    <div class="form-group">
                                        <label for="display_name"><span class="required_field">*</span> Display Name</label>
                                        <input type="text" class="form-control{{ $errors->has('display_name') ? ' is-invalid' : '' }}" name="display_name" value="{{$data->display_name}}">
                                        @if ($errors->has('display_name'))
                                            <span class="error invalid-feedback">{{$errors->first('display_name')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="name"><span class="required_field">*</span> Name</label>
                                        <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{$data->name}}">
                                        @if ($errors->has('name'))
                                            <span class="error invalid-feedback">{{$errors->first('name')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" class="form-control" rows="5">{{$data->description}}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="permissions"><span class="required_field">*</span> Permissions</label>
                                        <select multiple class="form-control select2 {{($errors->has('permissions')) ? 'is-invalid' : ''}}" name="permissions[]">
                                            @foreach($permissions as $permission)
                                                @if(in_array($permission->permission_id, $role_permissions_array))
                                                    <option value="{{$permission->permission_id}}" selected>{{$permission->display_name}}</option>
                                                @else
                                                    <option value="{{$permission->permission_id}}" >{{$permission->display_name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('permissions'))
                                            <span class="error invalid-feedback">{{$errors->first('permissions')}}</span>
                                        @endif
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
