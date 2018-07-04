$(document).ready(function(){ 
    $(".find").click(function(){ 
        save_date();
    });
 
});

function save_ticket_info(day,hour,minute,id_ticket,id_visitation){ 
	$("#save_day").val(day);
	$("#save_hour").val(hour);
	$("#save_minute").val(minute);
	$("#id_ticket").val(id_ticket);
	$("#id_visitation").val(id_visitation);
};