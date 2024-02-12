<script type="text/javascript">
	// Downloadable datatable
    let downloadable = $('#downloadable_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'downloadables/datatable',
        columns: [
            { data: 'display_name', name: 'display_name', width: '15%' },
            { data: 'version', name: 'version', width: '10%' },
            { data: 'link', name: 'link', width: '20%' },
            { data: 'downloadable_category', name: 'downloadable_category', width: '20%' },
            { data: 'status', name: 'status', width: '15%' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '20%' }
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        order: [[0, 'asc']],
    })

    // Confirm downloadable publish
    function publish_downloadable(downloadableID) {
        Swal.fire({
            title: 'Publish this downloadable?',
            text: "Once this downloadable is published, it will show in the website.",
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
                    url: 'downloadables/publish',
                    data: {
                        _token: _token,
                        downloadableID: downloadableID
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully published this downloadable",
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

    // Confirm downloadable unpublish
    function unpublish_downloadable(downloadableID) {
        Swal.fire({
            title: 'Unpublish this downloadable?',
            text: "Once this downloadable is unpublished, it will not show in the website.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, unpublish it!'
        }).then((result) => {
            if(result.value) {
                HoldOn.open(holdon_options)
                $.ajax({
                    type: 'POST',
                    url: 'downloadables/unpublish',
                    data: {
                        _token: _token,
                        downloadableID: downloadableID
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully unpublished this downloadable",
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

    // Confirm downloadable delete
    function delete_downloadable(downloadableID) {
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
                    url: 'downloadables/'+downloadableID+'',
                    data: {
                        _token: _token
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfuly deleted this downloadable",
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