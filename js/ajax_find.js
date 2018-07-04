$(document).ready(function(){ 
    $("#surname").keyup(function(){ 
        ajax_search(); 
    });
	$("#name").keyup(function(){ 
        ajax_search(); 
    });
	$("#patronymic").keyup(function(){ 
        ajax_search(); 
    });
});

function ajax_search(){ 
  var search_name=$("#name").val(); 
  var search_surname=$("#surname").val(); 
  var search_patronymic=$("#patronymic").val(); 

  $.get("/find/", {name : search_name, surname : search_surname, patronymic : search_patronymic}, function(data){
   if (data.length>0){ 
     $(".find_result").html(data); 
   } 
  }) 
};