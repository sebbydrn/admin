<script type="text/javascript">
    // Permissions datatable
    let permissions_table = $('#permissions_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'permissions/datatable',
        columns: [
            { data: 'display_name', name: 'display_name' },
            { data: 'description', name: 'description' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        order: [[0, 'asc']],
    })

    // Confirm permission delete
    function delete_permission(id) {
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
                    url: 'permissions/'+id+'',
                    data: {
                        _token: _token
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfuly deleted this permission",
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
