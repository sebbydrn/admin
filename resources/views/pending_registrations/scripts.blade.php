<script type="text/javascript">
	
	// Pending registrations datatable
    let pending_registration_table = $('#pending_registrations_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'pending_registrations/datatable',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'email', name: 'email' },
            { data: 'affiliation', name: 'affiliation'},
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        order: [[0, 'asc']],
    })

</script>