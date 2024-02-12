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
                        <li class="breadcrumb-item active">Add New User</li>
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
                            <h3 class="card-title">Add New User</h3>
                        </div>
                        <div class="card-body">
                            {{-- Form --}}
                            {!! Form::open(['method' => 'POST', 'route' => 'users.store', 'name' => 'usersform']) !!}
                            <p><span class="required_field">*</span> Required fields.</p>

                            <div class="row">
                                <div class="col-lg-12">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>

                                    <div class="form-group">
                                        <label for="firstname"><span class="required_field">*</span> First Name</label>
                                        <input type="text" class="form-control {{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" value="{{old('firstname')}}" placeholder="Enter your first name">
                                        @if ($errors->has('firstname'))
                                            <span class="error invalid-feedback">{{$errors->first('firstname')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text" class="form-control {{ $errors->has('middlename') ? ' is-invalid' : '' }}" name="middlename" value="{{old('middlename')}}" placeholder="Enter your middle name">
                                    </div>

                                    <div class="form-group">
                                        <label for="lastname"><span class="required_field">*</span> Last Name</label>
                                        <input type="text" class="form-control {{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" value="{{old('lastname')}}" placeholder="Enter your last name">
                                        @if ($errors->has('lastname'))
                                            <span class="error invalid-feedback">{{$errors->first('lastname')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="extname">Extension Name</label>
                                        <input type="text" class="form-control {{ $errors->has('extname') ? ' is-invalid' : '' }} col-lg-6" name="extname" value="{{old('extname')}}" placeholder="Enter your extension name">
                                    </div>

                                    <div class="form-group">
                                        <label for="username"><span class="required_field">*</span> Username</label>
                                        <input type="text" class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{old('username')}}" placeholder="Enter your username">
                                        @if ($errors->has('username'))
                                            <span class="error invalid-feedback">{{$errors->first('username')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="password"><span class="required_field">*</span> Password</label>
                                        <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="" placeholder="Enter your password">
                                        @if ($errors->has('password'))
                                            <span class="error invalid-feedback">{{$errors->first('password')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation"><span class="required_field">*</span> Confirm Password</label>
                                        <input type="password" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" value="" placeholder="Enter your password">
                                        @if ($errors->has('password_confirmation'))
                                            <span class="error invalid-feedback">{{$errors->first('password_confirmation')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="email"><span class="required_field">*</span> E-mail Address</label>
                                        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{old('email')}}" placeholder="Enter your email">
                                        @if ($errors->has('email'))
                                            <span class="error invalid-feedback">{{$errors->first('email')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="secondaryemail">Alternate E-mail Address</label>
                                        <input type="text" class="form-control {{ $errors->has('secondaryemail') ? ' is-invalid' : '' }}" name="secondaryemail" value="{{old('secondaryemail')}}" placeholder="Enter your alternate email">
                                        @if ($errors->has('secondaryemail'))
                                            <span class="error invalid-feedback">{{$errors->first('secondaryemail')}}</span>
                                        @endif
                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="birthday"><span class="required_field">*</span> Birthday</label>
                                        <input type="text" class="form-control birthday {{ $errors->has('birthday') ? ' is-invalid' : '' }}" name="birthday" value="{{old('birthday')}}" placeholder="Enter your birthday" readonly="readonly">
                                        @if ($errors->has('birthday'))
                                            <span class="error invalid-feedback" style="{{$errors->first('birthday') ? 'display: block' : ''}}">{{$errors->first('birthday')}}</span>
                                        @endif
                                    </div> --}}

                                    <div class="form-group">
                                        <label for="age"><span class="required_field">*</span> Age</label>
                                        <input type="number" class="form-control age {{$errors->has('age') ? 'is-invalid' : ''}}" name="age" value="{{old('age')}}">
                                        @if($errors->has('age'))
                                            <span class="error invalid-feedback" style="{{$errors->first('age') ? 'display:  block' : ''}}">{{$errors->first('age')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="sex"><span class="required_field">*</span> Sex</label>
                                        <div class="form-check">
                                            <input type="radio" name="sex" value="Male" class="form-check-input" {{(old('sex') == "Male") ? 'checked' : ''}} {{(old('sex') == "") ? 'checked' : ''}}>
                                            <label class="form-check-label">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="sex" value="Female" class="form-check-input" {{(old('sex') == "Female") ? 'checked' : ''}}>
                                            <label class="form-check-label">Female</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_no"><span class="required_field">*</span> Contact No.</label>
                                        <input type="text" class="form-control input_mask {{ $errors->has('contact_no') ? ' is-invalid' : '' }}" name="contact_no" value="{{old('contact_no')}}" placeholder="Enter your contact no." data-inputmask="'mask': '9999-999-9999'">
                                        @if ($errors->has('contact_no'))
                                            <span class="error invalid-feedback">{{$errors->first('contact_no')}}</span>
                                        @endif
                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="country"><span class="required_field">*</span> Country</label>
                                        <select name="country" id="country" class="form-control {{$errors->has('country') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Country</option>
                                            @foreach($countries as $key => $value)
                                                <option value="{{$key}}" {{old('country') == $key ? 'selected' : ''}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country'))
                                            <span class="error invalid-feedback">{{$errors->first('country')}}</span>
                                        @endif
                                    </div> --}}

                                    <input type="hidden" name="region" id="region" value="{{($errors->has('region')) ? '' : old('region')}}">

                                    <div class="form-group" id="province_input">
                                        <label for="province"><span class="required_field">*</span> Province</label>
                                        <select name="province" id="province" class="form-control {{$errors->has('province') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Province</option>
                                            @foreach($provinces as $province)
                                                <option value="{{$province->prov_code}}" region_id="{{$province->region_id}}" province_id="{{$province->province_id}}" {{old('province') == $province->prov_code ? 'selected' : ''}}>{{$province->name}}</option>
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
                                        </select>
                                        @if ($errors->has('municipality'))
                                            <span class="error invalid-feedback">{{$errors->first('municipality')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="barangay_input">
                                        <label for="barangay"><span class="required_field">*</span> Barangay</label>
                                        <input type="text" class="form-control {{$errors->has('barangay') ? 'is-invalid' : ''}}" name="barangay" value="{{old('barangay')}}" placeholder="Enter your barangay">
                                        @if ($errors->has('barangay'))
                                            <span class="error invalid-feedback">{{$errors->first('barangay')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="affiliation"><span class="required_field">*</span> Affiliation</label>
                                        <select name="affiliation" id="affiliation" class="form-control {{$errors->has('affiliation') ? 'is-invalid' : ''}}">
                                            <option value="0" selected disabled>Select Affiliation</option>
                                            @foreach($affiliations as $affiliation)
                                                <option value="{{$affiliation->affiliation_id}}" {{old('affiliation') == $affiliation->affiliation_id ? 'selected' : ''}}>{{$affiliation->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('affiliation'))
                                            <span class="error invalid-feedback">{{$errors->first('affiliation')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="designation">Designation</label>
                                        <input type="text" class="form-control" name="designation" value="{{old('designation')}}" placeholder="Enter your designation">
                                        @if ($errors->has('designation'))
                                            <span class="error invalid-feedback">{{$errors->first('designation')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="station_input" style="display: {{$errors->has('station') ? 'block' : 'none'}};">
                                        <label for="station"><span class="required_field">*</span> PhilRice Station</label>
                                        <select class="form-control {{$errors->has('station') ? 'is-invalid' : ''}}" name="station" id="station">
                                            <option value="0" selected disabled>Select PhilRice station</option>
                                            @foreach($stations as $station)
                                                <option value="{{$station->philrice_station_id}}" {{old('station') == $station->philrice_station_id ? 'selected' : ''}}>{{$station->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('station'))
                                            <span class="error invalid-feedback">{{$errors->first('station')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="philrice_idno_input" style="display: {{$errors->has('philrice_idno') ? 'block' : 'none'}};">
                                        <label for="philrice_idno"><span class="required_field">*</span> PhilRice ID No.</label>
                                        <input type="text" name="philrice_idno" id="philrice_idno" class="form-control input_mask {{$errors->has('philrice_idno') ? 'is-invalid' : ''}}" data-inputmask="'mask': '99-9999'" value="{{old('philrice_idno')}}">
                                        @if ($errors->has('philrice_idno'))
                                            <span class="error invalid-feedback">{{$errors->first('philrice_idno')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="coop" style="display: {{$errors->has('coop') ? 'block' : 'none'}};">
                                        <label for="coop">Cooperative</label>
                                        <input type="text" class="form-control" name="coop" value="{{old('coop')}}" placeholder="Enter your cooperative">
                                        @if ($errors->has('coop'))
                                            <span class="error invalid-feedback">{{$errors->first('coop')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="agency" style="display: {{$errors->has('agency') ? 'block' : 'none'}};">
                                        <label for="agency">Agency</label>
                                        <input type="text" class="form-control {{ $errors->has('agency') ? ' is-invalid' : '' }}" name="agency" value="{{old('agency')}}" placeholder="Enter your agency">
                                        @if ($errors->has('agency'))
                                            <span class="error invalid-feedback">{{$errors->first('agency')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="school" style="display: {{$errors->has('school') ? 'block' : 'none'}};">
                                        <label for="school">University/ school</label>
                                        <input type="text" class="form-control {{ $errors->has('school') ? ' is-invalid' : '' }}" name="school" value="{{old('school')}}" placeholder="Enter your school">
                                        @if ($errors->has('school'))
                                            <span class="error invalid-feedback">{{$errors->first('school')}}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" id="accreditation_no" style="display: {{$errors->has('accreditation_no') ? 'block' : 'none'}}">
                                        <label for="accreditation_no">Accreditation No.</label>
                                        <input type="text" class="form-control {{$errors->has('accreditation_no') ? ' is-invalid' : ''}}" name="accreditation_no" value="{{old('accreditation_no')}}" placeholder="Enter your accreditation no.">
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
@endsection

@push('scripts')
    @include('users.scripts')

    <script>
        $(document).ready(()=> {
            var province_id = $('#province option:selected').attr('province_id')

            if (province_id != 0) {
                // Get municipalities
                $.ajax({
                    type: 'POST',
                    url: "{{route('users.municipalities')}}",
                    data: {
                        _token: _token,
                        province_id: province_id
                    },
                    dataType: 'json',
                    success: (res)=>{
                        $('#municipality').empty() // empty municipality
                        var options = `<option value="0" selected disabled>Municipality</option>`
                        res.forEach((item)=> {
                            options += `<option value="`+item.mun_code+`">`+item.name+`</option>`
                        })
                        $('#municipality').append(options)
                        var old_municipality = "{{old('municipality')}}"
                        console.log(old_municipality)
                        if (old_municipality) {
                            $('#municipality option[value="'+old_municipality+'"]').prop('selected', true)
                        }
                    }
                })
            }
        })
    </script>
@endpush
