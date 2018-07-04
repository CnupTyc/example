$(document).ready(function(){ 
    $(".find").click(function(){ 
        save_date();
    });
 
});

function save_date(){ 
	var date = $("#date").val();
	$("#save_date").val(date);
	var date_today = document.querySelector('[name="date_today"]');
	date_today.innerHTML =  date;
	/*var date = $("#date").val();
	$("#save_date").val(date);
	$.get("/save_date", {date : date}, function(data){ 
		if (data.length>0){ 
			$(".date_today").html(data); 
		}
	})*/
};