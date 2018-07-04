function delete_protocol_ticket(Id_ticket, Id_visitation, id_patient){
		$.ajax({
				type: "delete",
				url: "/delete_protocol_ticket",
				data: { Id_visitation : Id_visitation, Id_ticket : Id_ticket, id_patient : id_patient },
				success: function(data) { console.log(data); }
			})
			.done(function(data) {
				if (data.length>0){ 
				alert("Талон удалён");
				$(".ticket").html(data);
			}
			});
};
