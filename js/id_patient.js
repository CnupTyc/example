$(document).ready(function(){ 
    $(".find").click(function(){ 
        save_id();
    });
 
});

function save_id(id, surname, name, patronymic){
	$("#ident").val(id); 
	$("#date_of_visit").val($("#date").val());
	var fio = document.querySelector('[name="patient_name"]');
	fio.innerHTML =  "Работа с пациентом: </br>" + "<h6>" + surname + " " + name + " " + patronymic + "</h6>";
};