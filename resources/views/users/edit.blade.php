@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('users')}}">Users List</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
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
                            <h3 class="card-title">Edit User</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['method' => 'PATCH', 'route' => ['users.update', $user_data->user_id], 'name' => 'usersupdateform']) !!}
                            <div class="row">
                                <div class="col-lg-12">
                                    <p><span class="required_field">*</span> Required fields</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    {{-- User country --}}
                                    <?php $country = str_replace(' ', '', $user_data->country); ?>

                                    {{-- User affiliation --}}
                                    <?php $user_affiliation2 = ($user_affiliation != '') ? $user_affiliation->affiliation_id : ''; ?>

                                    {{-- User philrice station --}}
                                    <?php $philrice_station_id = ($user_affiliation != '') ? $user_affiliation->philrice_station_id : ''; ?>

                                    <input type="hidden" name="old_firstname" value="{{$user_data->firstname}}">
                                    <input type="hidden" name="old_middlename" value="{{$user_data->middlename}}">
                                    <input type="hidden" name="old_lastname" value="{{$user_data->lastname}}">
                                    <input type="hidden" name="old_extname" value="{{$user_data->extname}}">
                                    <input type="hidden" name="old_username" value="{{$user_data->username}}">
                                    <input type="hidden" name="old_email" value="{{$user_data->email}}">
                                    <input type="hidden" name="old_secondaryemail" value="{{$user_data->secondaryemail}}">
                                    {{-- <input type="hidden" name="old_birthday" value="{{$user_data->birthday}}"> --}}
                                    <input type="hidden" name="old_sex" value="{{$user_data->sex}}">
                                    <input type="hidden" name="old_contact_no" value="{{$user_data->contact_no}}">
                                    {{-- <input type="hidden" name="old_country" value="{{$country}}"> --}}
                                    <input type="hidden" name="old_region" value="{{$user_data->region}}">
                                    <input type="hidden" name="old_province" value="{{$user_data->province}}">
                                    <input type="hidden" name="old_municipality" value="{{$user_data->municipality}}">
                                    <input type="hidden" name="old_barangay" value="{{$user_data->barangay}}">
                                    <input type="hidden" name="old_designation" value="{{$user_data->designation}}">
                                    <input type="hidden" name="old_affiliation" value="{{$user_affiliation2}}">
                                    <input type="hidden" name="old_station" value="{{$philrice_station_id}}">
                                    <input type="hidden" name="old_philrice_idno" value="{{$user_data->philrice_idno}}">
                                    <input type="hidden" name="old_fullname" value="{{$user_data->fullname}}">
                                    <input type="hidden" name="old_coop" value="{{$user_data->cooperative}}">
                                    <input type="hidden" name="old_agency" value="{{$user_data->agency}}">
                                    <input type="hidden" name="old_school" value="{{$user_data->school}}">
                                    <input type="hidden" name="old_age" value="{{$user_data->age}}">
                                    <input type="hidden" name="old_accreditation_no" value="{{$user_data->accreditation_no}}">

                                    <div class="form-group">
                                        <label for="firstname"><span class="required_field">*</span> First Name</label>
                                        <input type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" value="{{$user_data->firstname}}">
                                        @if ($errors->has('firstname'))
                                        <span class="error invalid-feedback">{{$errors->first('firstname')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text" class="form-control{{ $errors->has('middlename') ? ' is-invalid' : '' }}" name="middlename" value="{{$user_data->middlename}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="lastname"><span class="required_field">*</span> Last Name</label>
                                        <input type="text" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" value="{{$user_data->lastname}}">
                                        @if ($errors->has('lastname'))
                                        <span class="error invalid-feedback">{{$errors->first('lastname')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="extname">Extension Name</label>
                                        <input type="text" class="form-control{{ $errors->has('extname') ? ' is-invalid' : '' }} col-lg-6" name="extname" value="{{$user_data->extname}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="username"><span class="required_field">*</span> Username</label>
                                        <input type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{$user_data->username}}">
                                        @if ($errors->has('username'))
                                        <span class="error invalid-feedback">{{$errors->first('username')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="email"><span class="required_field">*</span> E-mail Address</label>
                                        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{$user_data->email}}">
                                        @if ($errors->has('email'))
                                        <span class="error invalid-feedback">{{$errors->first('email')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="secondaryemail">Alternate E-mail Address</label>
                                        <input type="text" class="form-control {{ $errors->has('secondaryemail') ? ' is-invalid' : '' }}" name="secondaryemail" value="{{$user_data->secondaryemail}}">
                                        @if ($errors->has('secondaryemail'))
                                        <span class="error invalid-feedback">{{$errors->first('secondaryemail')}}</span>
                                        @endif
                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="birthday">Birthday</label>
                                        <input type="text" class="form-control birthday {{ $errors->has('birthday') ? ' is-invalid' : '' }}" name="birthday" value="{{$user_data->birthday}}" readonly="readonly">
                                        @if ($errors->has('birthday'))
                                        <span class="error invalid-feedback" style="{{$errors->first('birthday') ? 'display: block' : ''}}">{{$errors->first('birthday')}}</span>
                                        @endif
                                    </div> --}}

                                    <div class="form-group">
                                        <label for="age"><span class="required_field">*</span> Age</label>
                                        <input type="number" class="form-control age {{$errors->has('age') ? 'is-invalid' : ''}}" name="age" value="{{$user_data->age}}">
                                        @if($errors->has('age'))
                                            <span class="error invalid-feedback" style="{{$errors->first('age') ? 'display:  block' : ''}}">{{$errors->first('age')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="sex"><span class="required_field">*</span> Sex</label>
                                        <div class="form-check">
                                            <input type="radio" name="sex" value="Male" class="form-check-input" {{($user_data->sex == "Male") ? 'checked' : ''}}>
                                            <label class="form-check-label">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="sex" value="Female" class="form-check-input" {{($user_data->sex == "Female") ? 'checked' : ''}}>
                                            <label class="form-check-label">Female</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_no">Contact No.</label>
                                        <input type="text" class="form-control input_mask {{ $errors->has('contact_no') ? ' is-invalid' : '' }}" name="contact_no" value="{{$user_data->contact_no}}" data-inputmask="'mask': '9999-999-9999'">
                                        @if ($errors->has('contact_no'))
                                        <span class="error invalid-feedback">{{$errors->first('contact_no')}}</span>
                                        @endif
                                    </div>
                                    
                                    {{-- <div class="form-group">
                                        <label for="country"><span class="required_field">*</span> Country</label>
                                        <select name="country" id="country" class="form-control {{$errors->has('country') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Country</option>
                                            @foreach($countries as $key => $value)
                                                <option value="{{$key}}" {{$country == $key ? 'selected' : ''}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country'))
                                            <span class="error invalid-feedback">{{$errors->first('country')}}</span>
                                        @endif
                                    </div> --}}

                                    <input type="hidden" name="region" id="region" value="{{$user_data->region}}">
                                    
                                    <div class="form-group" id="province_input">
                                        <label for="province"><span class="required_field">*</span> Province</label>
                                        <select name="province" id="province" class="form-control {{$errors->has('province') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Province</option>
                                            @foreach($provinces as $province)
                                                <option value="{{$province->prov_code}}" region_id="{{$province->region_id}}" province_id="{{$province->province_id}}" {{$user_data->province == $province->prov_code ? 'selected' : ''}}>{{$province->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('province'))
                                            <span class="error invalid-feedback">{{$errors->first('province')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="municipality_input">
                                        <label for="municipality"><span class="required_field">*</span> Municipality</label>
                                        <select name="municipality" id="municipality" class="form-control {{$errors->has('municipality') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Municipality</option>
                                            @if($municipalities != '')
                                                @foreach($municipalities as $municipality)
                                                    <option value="{{$municipality->mun_code}}" {{$user_data->municipality == $municipality->mun_code ? 'selected' : ''}}>{{$municipality->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('municipality'))
                                            <span class="error invalid-feedback">{{$errors->first('municipality')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="barangay_input">
                                        <label for="barangay">Barangay</label>
                                        <input type="text" class="form-control {{$errors->has('barangay') ? 'is-invalid' : ''}}" name="barangay" value="{{$user_data->barangay}}" placeholder="Enter your barangay">
                                        @if ($errors->has('barangay'))
                                            <span class="error invalid-feedback">{{$errors->first('barangay')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="affiliation"><span class="required_field">*</span> Affiliation</label>
                                        <select name="affiliation" id="affiliation" class="form-control {{$errors->has('affiliation') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Affiliation</option>
                                            @foreach($affiliations as $affiliation)
                                                <option value="{{$affiliation->affiliation_id}}" {{$user_affiliation2 == $affiliation->affiliation_id ? 'selected' : ''}}>{{$affiliation->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('affiliation'))
                                            <span class="error invalid-feedback">{{$errors->first('affiliation')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="designation">Designation</label>
                                        <input type="text" class="form-control" name="designation" value="{{$user_data->designation}}" placeholder="Enter your designation">
                                        @if ($errors->has('designation'))
                                            <span class="error invalid-feedback">{{$errors->first('designation')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="station_input" style="display: {{$errors->has('station')||$user_affiliation2 == 1 ? 'block' : 'none'}};">
                                        <label for="station"><span class="required_field">*</span> PhilRice Station</label>
                                        <select class="form-control {{$errors->has('station') ? 'is-invalid' : ''}}" name="station" id="station">
                                            <option value="0" selected disabled>Select PhilRice station</option>
                                            @foreach($stations as $station)
                                                <option value="{{$station->philrice_station_id}}" {{$philrice_station_id == $station->philrice_station_id ? 'selected' : ''}}>{{$station->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('station'))
                                            <span class="error invalid-feedback">{{$errors->first('station')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="philrice_idno_input" style="display: {{$errors->has('philrice_idno')||$user_affiliation2 == 1  ? 'block' : 'none'}};">
                                        <label for="philrice_idno"><span class="required_field">*</span> PhilRice ID No.</label>
                                        <input type="text" name="philrice_idno" id="philrice_idno" class="form-control input_mask {{$errors->has('philrice_idno') ? 'is-invalid' : ''}}" data-inputmask="'mask': '99-9999'" value="{{$user_data->philrice_idno}}">
                                        @if ($errors->has('philrice_idno'))
                                            <span class="error invalid-feedback">{{$errors->first('philrice_idno')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="coop" style="display: {{$errors->has('coop')||$user_affiliation2 == 3 || $user_affiliation2 == 9 ? 'block' : 'none'}};">
                                        <label for="coop">Cooperative</label>
                                        <input type="text" class="form-control" name="coop" value="{{$user_data->cooperative}}" placeholder="Enter your cooperative">
                                        @if ($errors->has('coop'))
                                            <span class="error invalid-feedback">{{$errors->first('coop')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="agency" style="display: {{$errors->has('agency')||$user_affiliation2 == 6 ? 'block' : 'none'}};">
                                        <label for="agency">Agency</label>
                                        <input type="text" class="form-control" name="agency" value="{{$user_data->agency}}" placeholder="Enter your agency">
                                        @if ($errors->has('agency'))
                                            <span class="error invalid-feedback">{{$errors->first('agency')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="school" style="display: {{$errors->has('school')||$user_affiliation2 == 5 ? 'block' : 'none'}};">
                                        <label for="school">School</label>
                                        <input type="text" class="form-control" name="school" value="{{$user_data->school}}" placeholder="Enter your school">
                                        @if ($errors->has('school'))
                                            <span class="error invalid-feedback">{{$errors->first('school')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="accreditation_no" style="display: {{$errors->has('accreditation_no') || $user_affiliation2 == 3 || $user_affiliation2 == 9 ? 'block' : 'none'}}">
                                        <label for="accreditation_no">Accreditation No.</label>
                                        <input type="text" class="form-control {{$errors->has('accreditation_no') ? ' is-invalid' : ''}}" name="accreditation_no" value="{{$user_data->accreditation_no}}" placeholder="Enter your accreditation no.">
                                    </div>

                                    <button type="submit" name="save" class="btn btn-success" style="float: right; margin-top: 30px;"><i class="fa fa-check"></i> Save Changes</button>
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
    {{-- End of Main Content --}}
@endsection

@push('scripts')
    @include('users.scripts')
@endpush
