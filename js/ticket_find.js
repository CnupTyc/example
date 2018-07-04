$(document).ready(function(){ 
    $("#date").change(function(){ 
        ticket_find();	
    });
 
});

function ticket_find(){
	var date=$("#date").val();
	var d = new Date (date)
	$("#week_day").val(d.getDay());
 
	var input = document.querySelector('[name="input_of_visit"]');
	var $date_today = $("#save_date").val();
	var $random_date = $("#date").val();
	if ($date_today ==  $random_date)
	{
		// enable
		input.removeAttribute('disabled');
	}
	else
	{
		// disable
		input.setAttribute('disabled', true);
	}
	
	var input2 = document.querySelector('[name="patient_list"]');
	var $d1 = new Date ($random_date);
	var $d2 = new Date ($date_today);
	var $dm = ($d1 - $d2);
	/*if ($dm >= 0)
	{
		// enable
		input2.removeAttribute('disabled');
	}
	else
	{
		// disable
		input2.setAttribute('disabled', true);
	}*/
	if ($dm >= 0)
	{
		$dm = 1;
	}
	else
	{
		$dm = 0;
	}
		
	
		
	$.get("/ticket_find/", {search_term : date, date_obs : $dm, day : d.getDay()}, function(data){
		if (data.length>0){ 
			$(".ticket").html(data); 
		} 
	})
};