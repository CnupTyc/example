function edit_patient_info(){
	var Id_patient = $("#ident").val();
	var Surname = $("#Surname").val();
	var Name = $("#Name").val();
	var Patronymic = $("#Patronymic").val();
	var Sex_data = $("#Sex_data").val();
	var Date_born = $("#Date_born").val();
	var Number_history = $("#Number_history").val();
	var Date_with = $("#Date_with").val();
	var Date_before = $("#Date_before").val();
	var Series_number = $("#Series_number").val();
	var Passport = $("#Passport").val();
	var SNILS = $("#SNILS").val();
	var OMS = $("#OMS").val();
	var Id_insurer_data = $("#Id_insurer_data").val();
	var Address_propis = $("#Address_propis").val();
	var Address_living = $("#Address_living").val();
	var Id_area_data = $("#Id_area_data").val();
	var Id_organization_data = $("#Id_organization_data").val();
	var Reason_data = $("#Reason_data").val();
	var Phone = $("#Phone").val();
	var Id_blood_data = $("#Id_blood_data").val();
	var Rh_factor_data = $("#Rh_factor_data").val();
	var Alergic = $("#Alergic").val();
	var Id_disability_data = $("#Id_disability_data").val();
	var Id_state_data = $("#Id_state_data").val();
	var FIO_relative = $("#FIO_relative").val();
	var Phone_relative = $("#Phone_relative").val();
	include("/js/ajax_block.js");
	
	$.ajax({
				type: "put",
				url: "/edit_patient_info",
				data: {	Id_patient : Id_patient,
						Surname : Surname,
						Name : Name,
						Patronymic : Patronymic,
						Sex_data : Sex_data,
						Date_born : Date_born,
						Number_history : Number_history,
						Date_with : Date_with,
						Date_before : Date_before,
						Series_number : Series_number,
						Passport : Passport,
						SNILS : SNILS,
						OMS : OMS,
						Id_insurer_data : Id_insurer_data,
						Address_propis : Address_propis,
						Address_living : Address_living,
						Id_area_data : Id_area_data,
						Id_organization_data : Id_organization_data,
						Reason_data : Reason_data,
						Phone : Phone,
						Id_blood_data : Id_blood_data,
						Rh_factor_data : Rh_factor_data,
						Alergic : Alergic,
						Id_disability_data : Id_disability_data,
						Id_state_data : Id_state_data,
						FIO_relative : FIO_relative,
						Phone_relative : Phone_relative
					},
					success: function(data) { console.log(data); }
			})
			.done(function(data) {
				if (data.length>0){ 
				alert("Данные изменены");
				var fio = document.querySelector('[name="patient_name"]');
				fio.innerHTML =  "Работа с пациентом: </br>" + "<h6>" + Surname + " " + Name + " " + Patronymic + "</h6>";
			}
			});
	
};

 function include(url) {
        var script = document.createElement('script');
        script.src = url;
        document.getElementsByTagName('head')[0].appendChild(script);
    }