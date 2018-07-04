$(document).ready(function(){ 
	$(".ticket").on("click","#enter_input_of_visit",function(){
        enter_input_of_visit();
    });
 
});

function enter_input_of_visit(){ 
	var comp = $("#input_comp").val();
	var anam = $("#input_anam").val();
	var diag = $("#input_diag").val();
	var sop_diag = $("#input_sop_diag").val();
	var appointments = $("#input_appointments").val();
	var Inspection = $("#input_Inspection").val();
	var Appointments = $("#input_appointments").val();
	var Survey = $("#input_surv").val();
	var Date_with = $("#date_with").val();
	var Date_before = $("#date_before").val();
	var Period = $("#active").val();
	var Reappearance = $("#repeat_date").val();
	var id = $("#id_visitation").val();
	include("/js/ticket_find.js");
	
	var $d1 = new Date (Date_with);
	var $d2 = new Date (Date_before);
	var $dm = ($d1 - $d2);
	 
	if ($dm <= 0)
	{
		$.ajax({
			type: "put",
			url: "/enter_input_of_visit",
			data: {
				comp : comp, 
				anam : anam, 
				diag : diag,
				Inspection : Inspection,
				Appointments: Appointments, 
				sop_diag : sop_diag,
				Survey : Survey,
				Date_with : Date_with,
				Date_before : Date_before,
				Period : Period,
				Reappearance : Reappearance,
				id_visitation : id
			}
		})
		alert("Результаты посещения внесены в протокол");
		ticket_find();
	}
	else 
	{
		alert("Дата окончания больничного листа не может быть раньше даты начала");
	}
};

 function include(url) {
        var script = document.createElement('script');
        script.src = url;
        document.getElementsByTagName('head')[0].appendChild(script);
    }