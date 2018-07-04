function edit_list(){ 
	
	  $.get("/edit_list", function(data){ 
		if (data.length>0){ 
		$(".menu_").html(data); 
		}
	  })
	  
};
