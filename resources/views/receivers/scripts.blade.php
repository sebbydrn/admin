<script type="text/javascript">
	// Receivers datatable
    let receivers = $('#receivers_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'receivers/datatable',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'receive_type', name: 'receive_type' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        order: [[0, 'asc']],
    })

    // Confirm receiver delete
    function delete_receiver(receiver_id) {
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
                    url: 'receivers/'+receiver_id+'',
                    data: {
                        _token: _token
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfuly deleted this receiver",
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