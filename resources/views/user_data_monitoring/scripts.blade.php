<script>
	let allDataTable

	$().ready(() => {

		$('#dateStart').datetimepicker({
	    	format: 'L'
	    })

	    $('#dateEnd').datetimepicker({
	    	format: 'L'
	    })

		// All data datatable
		// allDataTable = $('#allDataTable').DataTable({
		// 	processing: true,
		// 	serverSide: true,
		// 	ajax: {
		// 		type: 'POST',
		// 		url: "{{route('user_data_monitoring.allDataDatatable')}}",
		// 		data: {
		// 			_token: "{{csrf_token()}}"
		// 		}
		// 	},
		// 	columns: [
		// 		{data: 'timestamp', name: 'timestamp'},
		// 		{data: 'fullname', name: 'fullname'},
		// 		{data: 'username', name: 'username'},
		// 		{data: 'email', name: 'email'},
		// 		{data: 'affiliation', name: 'affiliation'}
		// 	],
		// 	oLanguage: {
		// 		sProcessing: '<img src="public/images/loading.gif">'
		// 	},
		// 	order: [[0, 'desc']]
		// })

		setInterval(function() {
			$.ajax({
				type: 'GET',
				url: "{{route('user_data_monitoring.show_registration_data')}}",
				dataType: 'JSON',
				success: (res) => {
					console.log(res)
					document.getElementById('totalDataReceived').innerHTML = res.registrationDataCount
					document.getElementById('dailyDataReceived').innerHTML = res.registrationDataCountDaily

					document.getElementById('dailyData').innerHTML = ""

					let dateToday = new Date()
					dateToday = monthText(dateToday) + " " + dayDate(dateToday) + ", " + year(dateToday)
					let dailyData = res.registrationData
					let dailyDataRows = '<strong>Data Log Today: '+dateToday+'</strong> <hr style="background-color: #fff" class="mt-0 mb-0" />'
					if (res.registrationDataCount == 0) {
						dailyDataRows += '<span style="color: red;">--No Data Received--</span>'
					} else {
						dailyData.forEach(function (item, index) {
							let dateReceived = new Date(item.timestamp)
							dateReceived = year(dateReceived) + "-" + month(dateReceived) + "-" + dayDate(dateReceived) + " " + hours(dateReceived) + ":" + minutes(dateReceived) + ":" + seconds(dateReceived)

							dailyDataRows += '<span style="color: #28a745;">[ '+dateReceived+' ]</span> Data: [ Fullname:"'+item.fullname+'";Username:"'+item.username+'";Email:"'+item.email+'";Affiliation:"'+item.affiliation+'" ]</span> <br />'
						})		
					}

					document.getElementById('dailyData').innerHTML = dailyDataRows

					// Reload table to update contents
					// $('#dailyDataTable').DataTable().ajax.reload(null, false)
					// $('#allDataTable').DataTable().ajax.reload(null, false)
				}
			})
		}, 3000)
	})

	function refreshTable() {
		$('#allDataTable').DataTable().ajax.reload(null, false)
	}

	function filterTable() {
		let dateStart = document.getElementById('dateStartInput').value
		let dateEnd = document.getElementById('dateEndInput').value

		if (dateStart && dateEnd) {
			// Destroy all data DataTable
			if ($.fn.DataTable.isDataTable('#allDataTable')) {
				$('#allDataTable').DataTable().destroy()
			}

			// Empty all data tbody
			$('#allDataTable tbody').empty()

			// All data datatable
			allDataTable = $('#allDataTable').DataTable({
				processing: true,
				serverSide: true,
				ajax: {
					type: 'POST',
					url: "{{route('user_data_monitoring.allDataDatatable')}}",
					data: {
						_token: "{{csrf_token()}}",
						dateStart: dateStart,
						dateEnd: dateEnd
					}
				},
				columns: [
					{data: 'timestamp', name: 'timestamp'},
					{data: 'fullname', name: 'fullname'},
					{data: 'username', name: 'username'},
					{data: 'email', name: 'email'},
					{data: 'affiliation', name: 'affiliation'}
				],
				oLanguage: {
					sProcessing: '<img src="public/images/loading.gif">'
				},
				order: [[0, 'desc']]
			})
		}
	}

	function year(date) {
		return date.getFullYear()
	}

	function month(date) {
		return `${date.getMonth() + 1}`.padStart(2, '0')
	}

	function monthText(date) {
		let month = date.getMonth()
		let monthArr = new Array()
		monthArr[0] = "January"
		monthArr[1] = "February"
		monthArr[2] = "March"
		monthArr[3] = "April"
		monthArr[4] = "May"
		monthArr[5] = "June"
		monthArr[6] = "July"
		monthArr[7] = "August"
		monthArr[8] = "September"
		monthArr[9] = "October"
		monthArr[10] = "November"
		monthArr[11] = "December"
		return monthArr[month]
	}

	function dayDate(date) {
		return `${date.getDate()}`.padStart(2, '0')
	}

	function hours(date) {
		return `${date.getHours()}`.padStart(2, '0')
	}

	function minutes(date) {
		return `${date.getMinutes()}`.padStart(2, '0')
	}

	function seconds(date) {
		return `${date.getSeconds()}`.padStart(2, '0')
	}
</script>