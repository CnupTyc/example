function create_ticket(id_doc,hour,minut){
	var date=$("#date").val();
	include("/js/ajax_block.js");
	$("#id_doc").val(id_doc);
	$.ajax({
		type: "put",
		url: "/create_ticket",
		data: 
		{	date : date,
			id_doc : id_doc,
			hour : hour, 
			minut : minut 				
		} 
	})
		.done(function(data){ 
			if (data.length>0){ 
			$("#id_ticket").val(data);
			ajax_change_block(9);
			}
		});
};

 function include(url) {
        var script = document.createElement('script');
        script.src = url;
        document.getElementsByTagName('head')[0].appendChild(script);
    }