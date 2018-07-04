$(document).ready(function(){
	$('#date, #Date_born, #date_with, #date_before, #repeat_date').daterangepicker({
		singleDatePicker: true,
		locale: {
		format: 'YYYY-MM-DD'
		}
	});
});

