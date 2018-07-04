$(document).ready(function(){ 
	$("#patient_data").click(function(){ 
        ajax_change_block(1); 
    });
	
	$("#shedule").click(function(){ 
        ajax_change_block(2); 
    });
	
	$("#patient_card_button").click(function(){ 
        ajax_change_block(3); 
    });
	
	$("#input_of_visit").click(function(){ 
        var ident = $('#ident').val();
		var date = $('#save_date').val();
		
		ajax_change_block(4,ident,date);
    }); 	
	$(".ticket").on("click","#input_of_visit_from_card",function(){
        var ident = $('#ident').val();
		var date = $('#save_date').val();
		ajax_change_block(4,ident,date);
    }); 
	
	$(".ticket").on("click","#more_info",function(){
		var Id_patient = $(this).data("id");
		var Date_ = $(this).data("date");
		var Id_visitation = $(this).data("protocol");
        ajax_change_block(5,Id_patient,Date_,Id_visitation); 
    });
	
	$(".ticket").on("click","#patient_card_button",function(){
        ajax_change_block(3);  	
	});
	
	$(".ticket").on("click","#documents",function(){
		var Id_patient = $(this).data("id");
        ajax_change_block(6,Id_patient); 
    });
	
	$(".ticket").on("click","#survey",function(){
		var Id_patient = $(this).data("id");
		var Survey = $(this).data("idsurvey");		
        ajax_change_block(7,Id_patient,Survey); 
    });	
	
	$(".ticket").on("click","#signal",function(){
		var Id_patient = $(this).data("id");
		var Signal = $(this).data("idsignal");		
        ajax_change_block(8,Id_patient,Signal); 
    });	

	$("#patient_list").click(function(){ 
        ajax_change_block(9); 
    });
	
	$(".ticket").on("click","#more_information",function(){
		var Id_patient = $(this).data("id");
        ajax_change_block(10,Id_patient); 
    });
	
	$(".ticket").on("click","#help",function(){	
        ajax_change_block(11); 
    });		
	$(".ticket").on("click","#show_patient_for_edit",function(){
		var Id_patient = $(this).data("id");
        ajax_change_block(12,Id_patient); 
    });	
	
	$(".ticket").on("click","#more_information_card",function(){
		var Id_patient = $(this).data("id");
        ajax_change_block(13,Id_patient); 
    });
	
	$(".ticket").on("click","#more_info_detail",function(){
		var Id_patient = $(this).data("id");
		var Date_ = $(this).data("date");
		var Id_visitation = $(this).data("protocol");
        ajax_change_block(14,Id_patient,Date_,Id_visitation); 
    });	
});

function ajax_change_block(n,id,date, Id_visitation){ 
  var id_patient = $("#ident").val();
  if (n == 1) 
  {
	  if($("#ident").val().length > 0)
	  {
	  $.get("/patient_data", {id_patient : id_patient}, function(data){ 
		if (data.length>0){ 
		$(".ticket").html(data); 
		}
	  })
	  }
  } 
  
  if (n == 2) 
  {
	var date_=$("#date").val(); 
	var d = new Date (date_)
	$("#week_day").val(d.getDay());
	
	$.get("/ticket_find/", {search_term : date_, day : d.getDay()}, function(data){
		if (data.length>0){ 
			$(".ticket").html(data); 
		} 
	})
  }
  
   if (n == 3) 
   {
	  if($("#ident").val().length > 0)
	  {
		$.get("/patient_card", {id_patient : id_patient}, function(data){ 
		if (data.length>0){ 
		$(".ticket").html(data); 
		}
		})
	  }		
   }
   if (n == 4) 
   {
		if($("#ident").val().length > 0)
		{
			$.get("/input_of_visit", {Id_patient : id , date : date}, function(data){ 
				if (data.length>0){ 
					$(".ticket").html(data); 
				}
			})
		}
		else
		{
			alert("Вы не выбрали пациента");
		}
   }
   
   if (n == 5) 
   {
	  if($("#ident").val().length > 0)
	  {
		$.get("/more_info" , {Id_patient : id , Date_ : date, Id_visitation : Id_visitation}, function(data){ 
		if (data.length>0){ 
		$(".ticket").html(data); 
		}
		})
	  }	
   }
   
   if (n == 6) 
   {
	  if($("#ident").val().length > 0)
	  {
		$.get("/documents" , {Id_patient : id}, function(data){ 
		if (data.length>0){ 
		$(".ticket").html(data); 
		}
		})
	  }	
   }
   
   if (n == 7) 
   {
	  if($("#ident").val().length > 0)
	  {
		$.get("/survey_content" , {Id_patient : id, Id_survey : date}, function(data){ 
		if (data.length>0){ 
		$(".picture_content").html(data); 
		}
		})
	  }	
   }
   
   if (n == 8) 
   {
	  if($("#ident").val().length > 0)
	  {
		$.get("/signal_content" , {Id_patient : id, Id_signal : date}, function(data){ 
		if (data.length>0){ 
		$(".picture_content").html(data); 
		}
		})
	  }	
   }   
   
   if (n == 9) 
   {
	$.get("/patient_list" , function(data){ 
		if (data.length>0){ 
			$(".ticket").html(data); 
		}
	})
   }
   
   if (n == 10) 
   {
	$.get("/more_information" , {id_patient : id}, function(data){ 
		if (data.length>0){ 
			$(".personal_content").html(data); 
		}
	})
   }   

   if (n == 11) 
   {
	var anam = $("#input_anam").val();
	var comp = $("#input_comp").val();
	$.get("/help", {anam : anam, comp : comp}, function(data){ 
		if (data.length>0){ 
			$(".help_content").html(data); 
		}
	})
   }   
   
   if (n == 12) 
   {
	$.get("/show_patient_for_edit", {Id_patient: id}, function(data){ 
		if (data.length>0){ 
			$(".personal_info_edit").html(data); 
		}
	})
   }   
   
   if (n == 13) 
   {
	$.get("/more_information_card" , {id_patient : id}, function(data){ 
		if (data.length>0){ 
			$(".personal_content").html(data); 
		}
	})
   }     
   if (n == 14) 
   {
	$.get("/more_info_detail" , {Id_patient : id , Date_ : date, Id_visitation : Id_visitation}, function(data){ 
		if (data.length>0){ 
			$(".personal_content").html(data); 
		}
	})
   }  
};
