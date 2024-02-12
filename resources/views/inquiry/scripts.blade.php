<script type="text/javascript">
	// Inquiries datatable
    let inquiries = $('#inquiries_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'inquiries/datatable',
        columns: [
            { data: 'sender', name: 'sender' },
            { data: 'email', name: 'email' },
            { data: 'inquiry', name: 'inquiry' },
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
</script>