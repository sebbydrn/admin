<script type="text/javascript">
	$(document).ready(function(){
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		var my_columns = [];
		//lineChartData();
		$('.date_from').datepicker({
            autoclose:true,
            format: 'yyyy-mm-dd',
            uiLibrary: 'bootstrap4'
            
        })
		$('.date_to').datepicker({
            autoclose:true,
            format: 'yyyy-mm-dd',
            uiLibrary: 'bootstrap4'

        })
		let api_table = $('#api_tables').DataTable({
	    processing: true,
	    serverSide: true,
	    stateSave: true,
	    responsive:true,
	    ajax: {
	    	type:'POST',
	    	url:'api-dashboard/datatable',
	    	data:function(d){
	    		d.api_name = $('#api_name').val();
	    		d.date_from = $('.date_from').val();
	    		d.date_to = $('.date_to').val();
	    	}
	    },
	    columns: [
	        { data: 'api_name', name: 'api_name', title:'Api Name' },
	        { data: 'value', name: 'value', title:'Data Available'},
	        { data: 'timestamp', name: 'timestamp',title:'As of'},
	        { data: 'actions', name: 'actions', title:'Action'}
	        
	    ],
	    oLanguage: {
	        sProcessing: '<img src="public/images/loading.gif">'
	    },
	    order: [[2, 'desc']],
	    })

		let recipient_table = $('#recipient_table').DataTable({
			processing: true,
		    serverSide: true,
		    stateSave: true,
		    responsive:true,
		    ajax:{
		    	type:'GET',
		    	url: 'api-dashboard/recipientDatatable'
		    },
		    columns: [
		    	{ data: 'email', name:'email', title: 'E-mail Address'},
		    	{ data: 'actions', name:'actions', title: 'Action'}
		    ]
		})

	    //button to filter the data by date and api name
	    $(document).on('click','#filter',function(){
	    	date_from = $('.date_from').val();
        	date_to = $('.date_to').val();
        
		    if( date_from && !date_to)
		    {
		        alert('Date To cannot be empty')
		    }else if(date_from > date_to)
		    {
		    	alert('Date To cannot be less than Date From')
		    }
		    else{
		    	api_table.draw();
		    	lineChartData();
		    }
	    	
	    });

	    $(document).on('click','#reset', function(){
	    	$('.date_from').val("");
	    	$('.date_to').val("");
	    })
	    //button that will display the data of chosen API
	    $(document).on('click','.viewBtn', function(){
			code = $(this).data('code');
			date = $(this).data('date');
			let apiViewTable;
			//$('#apiViewTable').DataTable().destroy();
			if ($.fn.DataTable.isDataTable('#apiViewTable')) {
				$('#apiViewTable').DataTable().clear().destroy();
			}

			$('#apiViewTable').empty()

			if(code == "st"){
				apiViewTable = $('#apiViewTable').DataTable({
						    processing: true,
						    serverSide: true,
						    stateSave: true,
						    responsive: true,
						    ajax: {
						    	type: 'POST',
					    		url:'api-dashboard/viewApiDetail',
					    		data: function(d){
					    			d.code = code;
					    			d.date = date;
					    		}
						    },
						    columns: [
						        { data: 'fund_source', name:'fund_source', title:' Fund Source' },
						        { data: 'lab_no', title: ' Lab No.'},
						        { data: 'status', title: 'Status'},
						        { data: 'result', title: 'Result'},
						        { data: 'seed_grower', title: ' Seed Grower'},
						        { data: 'coop', title: 'Coop'},
						        { data: 'variety', title: 'Variety'},
						        { data: 'seed_class', title: 'Seed Class'},
						        { data: 'number_of_bags_represented', title: 'Bags Represented'},
						        { data: 'germination', title: 'Germination'}
						    ],
						    oLanguage: {
						        sProcessing: '<img src="public/images/loading.gif">'
						    },
						    order: [[0, 'asc']],
						    })	
			}
			else if(code == "sg"){
				 apiViewTable = $('#apiViewTable').DataTable({
				    processing: true,
				    serverSide: true,
				    stateSave: true,
				    responsive: true,
				    ajax: {
				    	type: 'POST',
			    		url:'api-dashboard/viewApiDetail',
			    		data: function(d){
			    			d.code = code;
			    			d.date = date;
			    		}
				    },
				    columns: [
				        { data: 'FullName', title:'Full Name' },
				        { data: 'AccreNum', title: 'Accreditation No.'},
				        { data: 'ExpDate', title: 'Expiry Date'},
				        { data: 'AccreDate', title: 'Accreditation Date'},
				        { data: 'AccreArea', title: 'Accredited Area'},
				        { data: 'Region', title: 'Region'},
				        { data: 'Province', title: 'Province'}
				    ],
				    oLanguage: {
				        sProcessing: '<img src="public/images/loading.gif">'
				    },
				    order: [[0, 'asc']],
				    })
			}
			else if(code == "sc"){
				 apiViewTable = $('#apiViewTable').DataTable({
				    processing: true,
				    serverSide: true,
				    stateSave: true,
				    responsive: true,
				    ajax: {
				    	type: 'POST',
			    		url:'api-dashboard/viewApiDetail',
			    		data: function(d){
			    			d.code = code;
			    			d.date = date;
			    		}
				    },
				    columns: [
				        { data: 'AffiliateName', title:'Cooperative' },
				        { data: 'AccreArea', title: 'Accredited Area'},
				        { data: 'AccreNum', title: 'Accreditation No'},
				        { data: 'ExpDate', title: 'Expiry Date'}
				    ],
				    oLanguage: {
				        sProcessing: '<img src="public/images/loading.gif">'
				    },
				    order: [[0, 'asc']],
				    })
			}
			else if(code == "spi"){
				 apiViewTable = $('#apiViewTable').DataTable({
				    processing: true,
				    serverSide: true,
				    stateSave: true,
				    responsive: true,
				    ajax: {
				    	type: 'POST',
			    		url:'api-dashboard/viewApiDetail',
			    		data: function(d){
			    			d.code = code;
			    			d.date = date;
			    		}
				    },
				    columns: [
				        { data: 'seed_inspector', title:'Seed Inspector' },
				        { data: 'seed_grower', title: 'Seed Grower'},
				        { data: 'farm_location', title: 'Farm Location'},
				        { data: 'crop', title: 'Crop'},
				        { data: 'variety', title: 'Variety'},
				        { data: 'seed_class_planted', title:'Seed Class Planted'},
				        { data: 'area_planted', title:'Area Planted'},
				        { data: 'date_sown', title:'Date Sown'},
				        { data: 'date_planted', title: 'Date Planted'},
				        { data: 'date_transplanted', title: 'Date Transplanted'},
				        { data: 'condition_of_the_seed_fields', title: 'Condition of Seed Fields'},
				        { data: 'remarks_and_recommendation', title: 'Remarks and Recommendation'},
				        { data: 'date_inspected', title: 'Date Inspected'}
				    ],
				    oLanguage: {
				        sProcessing: '<img src="public/images/loading.gif">'
				    },
				    order: [[0, 'asc']],
				    })
			}
			else if(code == "sfi"){
				apiViewTable = $('#apiViewTable').DataTable({
				    processing: true,
				    serverSide: true,
				    stateSave: true,
				    responsive: true,
				    ajax: {
				    	type: 'POST',
			    		url:'api-dashboard/viewApiDetail',
			    		data: function(d){
			    			d.code = code;
			    			d.date = date;
			    		}
				    },
				    columns: [
				        { data: 'seed_inspector', title:'Seed Inspector' },
				        { data: 'seed_grower', title: 'Seed Grower'},
				        { data: 'farm_location', title: 'Farm Location'},
				        { data: 'crop', title: 'Crop'},
				        { data: 'variety', title: 'Variety'},
				        { data: 'seed_class_planted', title:'Seed Class Planted'},
				        { data: 'area_planted', title:'Area Planted'},
				        { data: 'date_sown', title:'Date Sown'},
				        { data: 'date_planted', title: 'Date Planted'},
				        { data: 'date_transplanted', title: 'Date Transplanted'},
				        { data: 'field_lot_no', title: 'Field Lot No.'},
				        { data: 'estimated_harvest', title: 'Estimated Harvest'},
				        { data: 'estimated_kg_bag', title: 'Estimated bags(kgs)'},
				        { data: 'field_purity', title:'Field Purity'}
				    ],
				    oLanguage: {
				        sProcessing: '<img src="public/images/loading.gif">'
				    },
				    order: [[0, 'asc']],
				    })
				apiViewTable.columns.adjust();
			}
			else if(code == "rceplabtest"){
				apiViewTable = $('#apiViewTable').DataTable({
				    processing: true,
				    serverSide: true,
				    stateSave: true,
				    responsive: true,
				    ajax: {
				    	type: 'POST',
			    		url:'api-dashboard/viewApiDetail',
			    		data: function(d){
			    			d.code = code;
			    			d.date = date;
			    		}
				    },
				    columns: [
				        { data: 'SGSrcID', title:'Seed Grower' },
				        { data: 'VarSrcID', title: 'Variety'},
				        { data: 'LabSrcID', title: 'Lab No.'},
				        { data: 'BagsRepresented', title: 'Bags'},
				        { data: 'Status', title: 'Status'},
				        { data: 'NumBagPass', title: 'No. of Bag Passed'},
				        { data: 'NumBagReject', title:'No. of Bag Rejected'},
				        { data: 'PercentGermination', title:'Germination %'},
				        { data: 'PercentFresh', title:'Fresh %'},
				        { data: 'PercentMoisture', title: 'Moisture %'}
				    ],
				    oLanguage: {
				        sProcessing: '<img src="public/images/loading.gif">'
				    },
				    order: [[0, 'asc']],
				    })
			}
			else if(code == 'growapp'){
				apiViewTable = $('#apiViewTable').DataTable({
				    processing: true,
				    serverSide: true,
				    stateSave: true,
				    responsive: true,
				    ajax: {
				    	type: 'POST',
			    		url:'api-dashboard/viewApiDetail',
			    		data: function(d){
			    			d.code = code;
			    			d.date = date;
			    		}
				    },
				    columns: [
				        { data: 'SGID', title:'Accreditation Number' },
				        { data: 'CroppingYear', title: 'Cropping Year'},
				        { data: 'Semester', title: 'Semester'},
				        { data: 'CropType', title: 'Crop Type'},
				        { data: 'VarietySrcID', title: 'Variety'},
				        { data: 'AreaPlanted', title: 'Area Planted'},
				        { data: 'SeedClass', title:'Seed Class'},
				    ],
				    oLanguage: {
				        sProcessing: '<img src="public/images/loading.gif">'
				    },
				    order: [[0, 'asc']],
				    })
			}
			/*$.ajax({
				type: 'POST',
		    	url:'api-dashboard/viewApiDetail',
		    	dataType:'json',
		    	data:{
		    		code: code,
		    		date:date
		    	},
		    	success:function(response){
		    		html = "";
		    		var arr = []
		    		console.log(response);
		    		
		    		if(code == "st"){
		    			html += '<table class="table table-bordered table-striped" id="api_tables" style="width: 100%;">';
			    			html += '<tr>';
			    				html += '<th>Fund Source</th>';
			    				html += '<th> Lab No.</th>';
			    				html += '<th> Status</th>';
			    				html += '<th> Result</th>';
			    				html += '<th> Seed Grower</th>';
			    				html += '<th> Coop</th>';
			    				html += '<th> Variety</th>';
			    				html += '<th> Seed Class</th>';
			    				html += '<th> No. of bags presented</th>';
			    				html += '<th> Germination</th>';
			    			html += '</tr>';
		    			
		    			$.each(response,function(k,v){
		    				html += '<tr>';
		    					html += '<td>'+v.fund_source+'</td>';
		    					html += '<td>'+v.lab_no+'</td>';
		    					html += '<td>'+v.status+'</td>';
		    					html += '<td>'+v.result+'</td>';
		    					html += '<td>'+v.seed_grower+'</td>';
		    					html += '<td>'+v.coop+'</td>';
		    					html += '<td>'+v.variety+'</td>';
		    					html += '<td>'+v.seed_class+'</td>';
		    					html += '<td>'+v.number_of_bags_represented+'</td>';
		    					html += '<td>'+v.germination+'</td>';
		    				html += '</tr>';
		    			})
		    			html += '</table>';
		    		} else if(code == "sg"){
		    			html += '<table class="table table-bordered table-striped" id="api_tables" style="width: 100%;">';
			    			html += '<tr>';
			    				html += '<th>First Name</th>';
			    				html += '<th>Middle Name</th>';
			    				html += '<th>Last Name</th>';
			    				html += '<th>Suffix</th>';
			    				html += '<th>Accreditation No.</th>';
			    				html += '<th>Expiry Date</th>';
			    				html += '<th>Date Applied</th>';
			    				html += '<th>Accredited Area</th>';
			    				html += '<th>Home Address</th>';
			    				html += '<th>Status</th>';
			    			html += '</tr>';
			    		$.each(response,function(k,v){
		    				html += '<tr>';
		    					html += '<td>'+v.FirstName+'</td>';
		    					html += '<td>'+v.MiddleName+'</td>';
		    					html += '<td>'+v.LastName+'</td>';
		    					html += '<td>'+v.Suffix+'</td>';
		    					html += '<td>'+v.AccreditationNo+'</td>';
		    					html += '<td>'+v.AccreditationExpiryDate+'</td>';
		    					html += '<td>'+v.DateApplied+'</td>';
		    					html += '<td>'+v.AccreditatedArea+'</td>';
		    					html += '<td>'+v.homeAddress+'</td>';
		    					html += '<td>'+v.AccreditationStatus+'</td>';
		    				html += '</tr>';
		    			})
		    			html += '</table>'
		    		}
		    		$.each(response,function(k,v){
		    			//console.log(k.length)
		    		})
		    		$('#viewApiModal .modal-body').append(html);
		    		$('#viewApiModal').modal('show');
		    		//console.log(response);
		    	}
			})*/

			$('#viewApiModal').modal('show');
		})

		/* apiDetail_table = $('#apiViewTable').DataTable({
				processing:true,
				serverSide:true,
				stateSave:true,
				ajax:{
					type: 'POST',
		    		url:'api-dashboard/viewApiDetail',
		    		data: function(d){
		    			d.code = code;
		    			d.date = date;
		    		}
				},
				columns: [
			        { data: 'fund_source', name: 'fund_source', title:' Fund Source' }
			        
			    ],
				oLanguage: {
			        sProcessing: '<img src="public/images/loading.gif">'
			    }
			})
			apiDetail_table.destroy();
			$('#viewApiModal').modal('show');*/
	})
</script>