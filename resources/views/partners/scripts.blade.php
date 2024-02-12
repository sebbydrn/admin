<script type="text/javascript">
	// Partners datatable
    let partners = $('#partners_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'partners/datatable',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'website', name: 'website' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        order: [[0, 'asc']],
    })

    // Content textarea convert to summernote wysiwyg editor
    $(document).ready(()=>{
        $('.textarea').summernote()
    })

    // Confirm partner delete
    function delete_partner(partner_id) {
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
                    url: 'partners/'+partner_id+'',
                    data: {
                        _token: _token
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfuly deleted this partner",
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