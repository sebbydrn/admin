<script type="text/javascript">
    // Users datatable
    let users_table = $('#users_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'users/datatable',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'email', name: 'email' },
            { data: 'status', name: 'status'},
            { data: 'roles', name: 'roles', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        order: [[0, 'asc']],
    })

    // Confirm user delete
    function delete_user(user_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'DELETE',
                    url: 'users/'+user_id+'',
                    data: {
                        _token: _token
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully deleted this user",
                                icon: 'success'
                            }).then((result) => {
                                location.reload()
                            })
                        }
                    }
                })
            }
        })
    }

    // Confirm force logout user
    function force_logout(user_id) {
        Swal.fire({
            title: 'Force Logout',
            text: "Force logout user from all devices?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Logout'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'POST',
                    url: 'users/force_logout',
                    data: {
                        _token: _token,
                        user_id: user_id
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have forced logout this user",
                                icon: 'success'
                            }).then((result) => {
                                location.reload()
                            })
                        }
                    }
                })
            }
        })
    }

    // Show philrice idno input when selected a philrice station
    /*$('#stationid').on('change', function() {
        if($(this).val() == 0) {
            $('#philrice_idno_input').css('display', 'none')
        } else {
            $('#philrice_idno_input').css('display', 'block')
        }
    })*/

    // Show province, municipality and barangay when selected ph as country
    // $('#country').on('change', function() {
    //     if($(this).val() == "PH") {
    //         $('#province_input').css('display', 'block')
    //         $('#municipality_input').css('display', 'block')
    //         $('#barangay_input').css('display', 'block')
    //     } else {
    //         $('#province_input').css('display', 'none')
    //         $('#municipality_input').css('display', 'none')
    //         $('#barangay_input').css('display', 'none')
    //     }
    // })

    // Get region when selected province
    $('#province').on('change', ()=>{
        var region_id = $('#province option:selected').attr('region_id')
        var province_id = $('#province option:selected').attr('province_id')

        $('#municipality').empty() // empty municipality
        $('#municipality').append(`<option selected disabled>Loading...</option>`)

        // Get region code
        $.ajax({
            type: 'POST',
            url: "{{route('users.regions.region_code')}}",
            data: {
                _token: _token,
                region_id: region_id
            },
            dataType: 'json',
            success: (res)=>{
                $('#region').val(res)
            }
        })

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
                // console.log(res)
                $('#municipality').empty() // empty municipality
                var options = `<option value="0" selected disabled>Select Municipality</option>`
                res.forEach((item)=> {
                    options += `<option value="`+item.mun_code+`">`+item.name+`</option>`
                })
                $('#municipality').append(options)
            }
        })
    })

    // Show philrice stations dropdown and id no input when selected philrice as affiliation
    $('#affiliation').on('change', ()=>{
        var affiliation_id = $('#affiliation option:selected').val()
        if (affiliation_id == 1) {
            $('#station_input').css('display', 'block')
            $('#philrice_idno_input').css('display', 'block')
            $('#coop').css('display', 'none')
            $('#agency').css('display', 'none')
            $('#school').css('display', 'none')
            $('#accreditation_no').css('display', 'none')
        } else if(affiliation_id == 3 || affiliation_id == 9) {
            $('#station_input').css('display', 'none')
            $('#philrice_idno_input').css('display', 'none')
            $('#coop').css('display', 'block')
            $('#agency').css('display', 'none')
            $('#school').css('display', 'none')
            $('#accreditation_no').css('display', 'block')
        } else if(affiliation_id == 5) {
            $('#station_input').css('display', 'none')
            $('#philrice_idno_input').css('display', 'none')
            $('#coop').css('display', 'none')
            $('#agency').css('display', 'none')
            $('#school').css('display', 'block')
            $('#accreditation_no').css('display', 'none')
        } else if(affiliation_id == 6) {
            $('#station_input').css('display', 'none')
            $('#philrice_idno_input').css('display', 'none')
            $('#coop').css('display', 'none')
            $('#agency').css('display', 'block')
            $('#school').css('display', 'none')
            $('#accreditation_no').css('display', 'none')
        } else if(affiliation_id == 9) {
            $('#station_input').css('display', 'none')
            $('#philrice_idno_input').css('display', 'none')
            $('#coop').css('display', 'block')
            $('#agency').css('display', 'none')
            $('#school').css('display', 'none')
            $('#accreditation_no').css('display', 'block')
        } else {
            $('#station_input').css('display', 'none')
            $('#philrice_idno_input').css('display', 'none')
            $('#coop').css('display', 'none')
            $('#agency').css('display', 'none')
            $('#school').css('display', 'none')
            $('#accreditation_no').css('display', 'none')
        }
    })

    // Confirm user role delete
    function delete_user_role(user_role_system_id, user_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'POST',
                    url: "{{route('users.roles.destroy')}}",
                    data: {
                        _token: _token,
                        user_id: user_id,
                        user_role_system_id: user_role_system_id
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully deleted this user role",
                                icon: 'success'
                            }).then((result) => {
                                location.reload()
                            })
                        }
                    }
                })
            }
        })
    }

    // Confirm user deactivate
    function deactivate_user(user_id) {
        Swal.fire({
            title: 'Deactivate this user?',
            text: "User will not be able to log in after this",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, deactivate user!'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'POST',
                    url: "{{route('users.deactivate')}}",
                    data: {
                        _token: _token,
                        user_id: user_id
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully deactivated this user",
                                icon: 'success'
                            }).then((result) => {
                                location.reload()
                            })
                        }
                    }
                })
            }
        })
    }

    // Confirm user activate
    function activate_user(user_id) {
        Swal.fire({
            title: 'Activate this user?',
            text: "User will be able to log in after this",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, activate user!'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'POST',
                    url: "{{route('users.activate')}}",
                    data: {
                        _token: _token,
                        user_id: user_id
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully activated this user",
                                icon: 'success'
                            }).then((result) => {
                                location.reload()
                            })
                        }
                    }
                })
            }
        })
    }

    /*
     * For Laravel validation
     *
     * If country selected is Philippines and province or municipality has value
     * and validation returned error on other fields
     * province, municipality and barangay fields must be displayed
     *
     * If affiliation selected is PhilRice and PhilRice station or PhilRice id no has value
     * and validation returned error on other fields
     * PhilRice station or PhilRice id no must be displayed
     *
     */
    $(document).ready(()=> {
        // Country
        var country = $('#country option:selected').val()
        if (country == "PH") {
            $('#province_input').css('display', 'block')
            $('#municipality_input').css('display', 'block')
            $('#barangay_input').css('display', 'block')
        }

        // Affiliation
        var affiliation = $('#affiliation option:selected').val()
        if (affiliation == 1) {
            $('#station_input').css('display', 'block')
            $('#philrice_idno_input').css('display', 'block')
        } else if (affiliation == 3) {
            $('#coop').css('display', 'block')
            $('#accreditation_no').css('display', 'block')
        } else if (affiliation == 9) {
            $('#coop').css('display', 'block')
            $('#accreditation_no').css('display', 'block')
        }

        // Birthday datepicker
        $('.birthday').datepicker({
            autoclose: true,
            uiLibrary: 'bootstrap4'
        })
        
        
    })
</script>
