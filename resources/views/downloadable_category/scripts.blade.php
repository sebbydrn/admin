<script type="text/javascript">
	// Downloadable category datatable
    let downloadableCategories = $('#downloadable_categories_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'downloadable_categories/datatable',
        columns: [
            { data: 'display_name', name: 'display_name' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        order: [[0, 'asc']],
    })

    // Confirm downloadable category publish
    function publish_downloadable_category(downloadableCategoryID) {
        Swal.fire({
            title: 'Publish this downloadable category?',
            text: "Once this downloadable category is published, it will show in the website.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, publish it!'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'POST',
                    url: 'downloadable_categories/publish',
                    data: {
                        _token: _token,
                        downloadableCategoryID: downloadableCategoryID
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully published this downloadable category",
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

    // Confirm downloadable category delete
    function delete_downloadable_category(downloadableCategoryID) {
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
                    url: 'downloadable_categories/'+downloadableCategoryID+'',
                    data: {
                        _token: _token
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfuly deleted this downloadable category",
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