function show_about_diagnosis(id){ 
	$.get("/show_about_diagnosis", {Id_diagnosis : id}, function(data){ 
		if (data.length>0){ 
			$(".help_content").html(data); 
		}
	  })	
};

function add_obs(observe){ 
	var obs = document.querySelector('[name="input_surv"]');
	obs.value = obs.value + ", " + observe;
};

function add_th(medic, dosa){ 
	var th = document.querySelector('[name="input_appointments"]');
	th.value = th.value + ", " + medic + "---" + dosa;
};