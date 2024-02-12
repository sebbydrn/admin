<script type="text/javascript">
	// Auto response datatable
    let pages = $('#auto_response_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'auto_response/datatable',
        columns: [
            { data: 'sender', name: 'sender' },
            { data: 'title', name: 'title' },
            { data: 'body', name: 'body' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        order: [[0, 'asc']],
    })

    // Body textarea convert to summernote wysiwyg editor
    $(document).ready(()=>{
        $('.textarea').summernote()
    })

    // Confirm content enable
    function enable_auto_response(auto_response_id) {
        Swal.fire({
            title: 'Enable this auto response?',
            text: "Once this auto response is enabled, the enabled auto response before will be disabled.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, enable it!'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'POST',
                    url: 'auto_response/enable',
                    data: {
                        _token: _token,
                        auto_response_id: auto_response_id,
                        enable: 1
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully enabled this auto response",
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

    // Confirm content disable
    function disable_auto_response(auto_response_id) {
        Swal.fire({
            title: 'Disable this auto response?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, disable it!'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'POST',
                    url: 'auto_response/enable',
                    data: {
                        _token: _token,
                        auto_response_id: auto_response_id,
                        enable: 0
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully disabled this auto response",
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

    // Confirm auto response delete
    function delete_auto_response(auto_response_id) {
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
                    url: 'auto_response/'+auto_response_id+'',
                    data: {
                        _token: _token
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfuly deleted this auto response",
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
</script>