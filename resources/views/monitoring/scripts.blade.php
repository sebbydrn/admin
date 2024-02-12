<script type="text/javascript">
$.ajaxSetup({headers:{'X-CSRF-Token': $('input[name="_token"]').val()}});
        $('.date_from').datepicker({
            autoclose:true,
            uiLibrary: 'bootstrap4'
        })
        $('.date_to').datepicker({
            autoclose:true,
            uiLibrary: 'bootstrap4'
        })
	let monitoring_table = $('#monitoring_table').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: {
            url:'monitoring/datatable',
            type: 'POST',
            data:function(d){
                d.system = $('#system').val();
                d.activity = $('#activity').val();
                d.user = $('#user').val();
                d.date_from = $('.date_from').val();
                d.date_to = $('.date_to').val();
            }
        },
        columns: [
        	{ data: 'system', name: 'system'},
            { data: 'activity', name: 'activity' },
            { data: 'user', name: 'user'},
            { data: 'device', name: 'device'},
            { data: 'browser', name: 'browser'},
            { data: 'timestamp', name: 'timestamp'}
        
        ],
        oLanguage: {
            sProcessing: '<img src="public/images/loading.gif">'
        },
        /*initComplete:function(settings, json) {

            this.api().columns([0,1,2]).every( function () {
                var column = this;
                // Use column title as label for select
                var title = $(column.header()).text();

                // Generate Label
                var label = $('<div class="col-md-4"><label>'+title+'</label></div>').appendTo($("#table_filters"));


                // Generate select
                var select = $('<select class="form-control"><option value="">Show All</option></select>')
                .appendTo( label )
                // Search when selection is changed
                .on( 'change', function () {
                  var val = $(this).val();
                  column.search( this.value ).draw();
                });
                // Capture the data from the JSON to populate the select boxes with all the options
                var extraData = (function(i) {
                switch(i) {
                case 0:
                    return json.allSystem;
                case 1:
                    return json.allActivity;
                case 2:
                    return json.allUser;
                }
              })(column.index());
                extraData.forEach(function(d) {
                    if(column.search() === d){
                      select.append( '<option value="'+d+'" selected="selected">'+d+'</option>' )
                    } else {
                      select.append( '<option value="'+d+'">'+d+'</option>' )
                    }
                })
                
            });
            $('<div class="col-md-3"><label>Date From</label><input type="text" class="form-control date_from" readonly></div>').appendTo($("#table_filters"));
            $('<div class="col-md-3"><label>Date To</label><input type="text" class="form-control date_to" readonly></div>').appendTo($("#table_filters"));
             $('<div class="col-md-12 mt-1"><button type="button" class="btn btn-primary" id="filter"><i class="fa fa-filter"></i> Filter</button></div>').appendTo($("#table_filters"));
        },*/
        order: [[0, 'asc']],
        
    })

    $('body').on('click','#filter', function() {
        from = $('.date_from').val();
        to = $('.date_to').val();
        
        if( from && !to)
        {
            alert('Date To cannot be empty')
        }
        monitoring_table.draw();
    })

    $('#reset').on('click',function(){
        $('.date_from, .date_to').val("");
    })
</script>