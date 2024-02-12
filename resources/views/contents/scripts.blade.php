<script type="text/javascript">
	// Contents datatable
    let contents = $('#contents_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: 'contents/datatable',
        columns: [
            { data: 'page', name: 'page' },
            { data: 'section', name: 'section' },
            { data: 'subtitle', name: 'subtitle' },
            { data: 'content', name: 'content' },
            { data: 'status', name: 'status' },
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

    // Get sections under page when page dropdown changed value
    $('#page').on('change', ()=>{
    	var page_id = $('#page option:selected').val()

    	$('#section').empty() // empty section
        $('#section').append(`<option selected disabled>Loading...</option>`)

    	$.ajax({
            type: 'POST',
            url: '{{route('contents.sections')}}',
            data: {
                _token: _token,
                page_id: page_id
            },
            success: (res)=>{
                $('#section').empty() // empty municipality
                var options = `<option value="0" selected disabled>Select Section</option>`
                res = JSON.parse(res)
                res.forEach((item)=> {
                    options += `<option value="`+item.section_id+`">`+item.display_name+`</option>`
                })
                $('#section').append(options)
            }
        })
    })

    // Confirm content publish
    function publish_content(content_id) {
        Swal.fire({
            title: 'Publish this content?',
            text: "Once this content is published, it will show in the website.",
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
                    url: 'contents/publish',
                    data: {
                        _token: _token,
                        content_id: content_id
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfully published this content",
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

    // Confirm content delete
    function delete_content(content_id) {
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
                    url: 'contents/'+content_id+'',
                    data: {
                        _token: _token
                    },
                    success: function(res) {
                        HoldOn.close()
                        if (res=='"success"') {
                            Swal.fire({
                                title: 'Success!',
                                text: "You have successfuly deleted this content",
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