function options_complains(){
	var a = $("#input_comp").val();
	var b = $("#comp :selected").val();
	var c = b+','+a;
	$("#input_comp").val(c);
};

function anamnesis_complains(){
	var a = $("#input_anam").val();
	var b = $("#anam :selected").val();
	var c = b+','+a;
	$("#input_anam").val(c);
};

function diagnesis_complains(){
	var b = $("#diag :selected").val();
	$("#input_diag").val(b);
};

function survey_change(){
	var a = $("#input_surv").val();
	var b = $("#surv :selected").val();
	var c = b+','+a;
	$("#input_surv").val(c);
	
};

function sop_diag_complains(){
	var a = $("#input_sop_diag").val();
	var b = $("#sop_diag :selected").val();
	var c = b+','+a;
	$("#input_sop_diag").val(c);
};

function sex_change(){
	var sex = $("#Sex_data_select :selected").val(); 
	$("#Sex_data").val(sex);
};

function company_cnahge(){
	var company = $("#Id_insurer_data_select :selected").val(); 
	$("#Id_insurer_data").val(company);
};

function area_cnahge(){
	var area_ = $("#Id_area_data_select :selected").val(); 
	$("#Id_area_data").val(area_);
};

function organization_cnahge(){
	var organization = $("#Id_organization_data_select :selected").val(); 
	$("#Id_organization_data").val(organization);
};

function reason_cnahge(){
	var reason = $("#Reason_data_select :selected").val(); 
	$("#Reason_data").val(reason);
};

function blood_cnahge(){
	var blood = $("#Id_blood_data_select :selected").val(); 
	$("#Id_blood_data").val(blood);
};

function rh_cnahge(){
	var rh = $("#Rh_factor_data_select :selected").val(); 
	$("#Rh_factor_data").val(rh);
};

function disability_cnahge(){
	var disability = $("#Id_disability_data_select :selected").val(); 
	$("#Id_disability_data").val(disability);
};

function state_cnahge(){
	var state = $("#Id_state_data_select :selected").val(); 
	$(Id_state_data).val(state);
};

