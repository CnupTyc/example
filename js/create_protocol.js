function create_protocol(Id_patient){
	var id_ticket=$("#id_ticket").val();
	var Id_doctor=$("#id_doc").val();
	include("/js/ticket_find.js");
	if (id_ticket)
	{
		$.ajax({
			type: "put",
			url: "/create_protocol",
			data: 
			{	id_ticket : id_ticket,
				Id_patient : Id_patient,
				Id_doctor : Id_doctor
			}
		})
	
		.done(function(data){ 
			if (data.length>0){ 
				$("#id_visitation").val(data);
				alert("День приёма назначен");
				ticket_find();
			}
		});
	}	  
	else
	{
		alert("Вы не выбрали талон");
	}
};

 function include(url) {
        var script = document.createElement('script');
        script.src = url;
        document.getElementsByTagName('head')[0].appendChild(script);
    }