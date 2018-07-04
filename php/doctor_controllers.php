<?php
// web/doctor_controllers.php
namespace doctor_controllers;
require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
Request::enableHttpMethodParameterOverride();
use Silex\Application;
use Silex\Api\ControllerProviderInterface;


class Doctor_controllers implements ControllerProviderInterface {
 
  public function connect(Application $application) {
    $factory=$application['controllers_factory'];
    $factory->get('/work_page','Doctor_controllers\doctor_controllers::work_page');
    $factory->get('/ticket_find/','Doctor_controllers\doctor_controllers::ticket_find');
	$factory->get('/patient_data','Doctor_controllers\doctor_controllers::patient_data');
	$factory->get('/patient_card','Doctor_controllers\doctor_controllers::patient_card');
	$factory->get('/input_of_visit','Doctor_controllers\doctor_controllers::input_of_visit');
	$factory->get('/more_info','Doctor_controllers\doctor_controllers::more_info');
	$factory->get('/more_info_detail','Doctor_controllers\doctor_controllers::more_info_detail');
	$factory->get('/documents','Doctor_controllers\doctor_controllers::documents');
	$factory->get('/survey_content','Doctor_controllers\doctor_controllers::survey_content');
	$factory->get('/signal_content','Doctor_controllers\doctor_controllers::signal_content');
	$factory->get('/save_date','Doctor_controllers\doctor_controllers::save_date');
	$factory->put('/create_ticket','Doctor_controllers\doctor_controllers::create_ticket');
	$factory->get('/patient_list','Doctor_controllers\doctor_controllers::patient_list');
	$factory->get('/find/','Doctor_controllers\doctor_controllers::find');
	$factory->get('/more_information','Doctor_controllers\doctor_controllers::more_information');
	$factory->get('/more_information_card','Doctor_controllers\doctor_controllers::more_information_card');
	$factory->put('/create_protocol','Doctor_controllers\doctor_controllers::create_protocol');
	$factory->put('/enter_input_of_visit','Doctor_controllers\doctor_controllers::enter_input_of_visit');
	$factory->get('/show_patient_for_edit','Doctor_controllers\doctor_controllers::show_patient_for_edit');
	$factory->put('/edit_patient_info','Doctor_controllers\doctor_controllers::edit_patient_info');
	$factory->delete('/delete_protocol_ticket','Doctor_controllers\doctor_controllers::delete_protocol_ticket');
	$factory->get('/edit_list','Doctor_controllers\doctor_controllers::edit_list');


	
    return $factory;
  }
 
 //рабочая страница врача
 public function work_page(Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$User = $app['session']->get('account');
	$Id_doctor = $User['user']['Id_doctor'];
	$Id_spec = $conn->fetchColumn('select Id_specialization from Doctor_specialization where Id_doctor = ?', [$Id_doctor]);
	$User['user']['Specialization'] = $conn->fetchColumn('select Name from Specialization where Id_specialization = ?', [$Id_spec]);
	return $app['twig']->render('work_page.twig', ['user' => $User['user']]);
}

//поиск пациентов по расписанию на день
 public function ticket_find(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$User = $app['session']->get('account');
	$Id_doctor = $User['user']['Id_doctor'];
	$term = $req->get('search_term');
	$day_num = $req->get('day');
	$date_obs = $req->get('date_obs');
	
	if ($date_obs == 0)
	{
		$date_obs = false;
	}
	else 
	{
		$date_obs = true;
	}
	
	switch ($day_num) {
    case 1:
        $day = "Понедельник";
        break;
    case 2:
        $day = "Вторник";
        break;
    case 3:
        $day = "Среда";
        break;
	case 4:
        $day = "Четверг";
        break;
	case 5:
        $day = "Пятница";
        break;
	default:
		$day = "Выходной";
	}
	
	$schedule_list = $conn->fetchAssoc('select Work_time_with, Work_time_before from Schedule join Date_work join Doctor where Schedule.Name = ? and Schedule.Id_date_work = Date_work.Id_date_work and Date_work.Id_doctor = ?', [$day, $Id_doctor]);
	$with = $schedule_list['Work_time_with'];
	$before = $schedule_list['Work_time_before'];
	
	$with = $with + 1 - 1;
	$before = $before + 1 - 1;
	
	$result = array();
	$minute = 0;
	$i = 0;
	
	while ($with < $before)
	{
		if ($minute == 60)
		{
			$with = $with + 1;
			$minute = 0;
		}
		$result[$i]['hour'] = $with;
		$result[$i]['minutes'] = $minute;
		$minute = $minute +10;
		$i++;
	}
	
	
	$id_date_work = $conn->fetchColumn('select Id_date_work from Date_work where Id_doctor = ?', [$Id_doctor]);
	
	//$ticket = $conn->fetchAll('select * from Daily_schedule where Id_date_work = ? and Date = ?', [$id_date_work, $term]);
	
	$ticket = $conn->fetchAll('select Protocol.Id_visitation, Daily_schedule.Id_work_day, Hour, Minute, Detail, Surname, Patronymic, Name, Patient.Id_patient from Date_work join Daily_schedule join Protocol join Patient where Date_work.Id_doctor = ? and Date_work.Id_date_work = Daily_schedule.Id_date_work and Daily_schedule.Date = ? and Daily_schedule.Id_work_day = Protocol.Id_day and Protocol.Id_patient = Patient.Id_patient', [$Id_doctor, $term]);
	
	$j = 0;
	$k = 0;
	$two_mass_result = array();
	$a=0;
	$a = count($ticket);

	while ($j < $i)
	{
		$two_mass_result[$j]['Hour'] = $result[$j]['hour'];
		$two_mass_result[$j]['Minute'] = $result[$j]['minutes'];
		$two_mass_result[$j]['Id_work_day'] = null;
		$two_mass_result[$j]['Detail'] = "талон свободен";
		$two_mass_result[$j]['Id_visitation'] = null;
		$two_mass_result[$j]['Surname'] = null;
		$two_mass_result[$j]['Patronymic'] = null;
		$two_mass_result[$j]['Name'] = null;
		$two_mass_result[$j]['Id_patient'] = null;
		$k = 0;
		while ($k < $a)
		{
			if ($two_mass_result[$j]['Hour'] == $ticket[$k]['Hour'])
			{	
				if ($two_mass_result[$j]['Minute'] == $ticket[$k]['Minute'])
				{
					$two_mass_result[$j]['Id_visitation'] = $ticket[$k]['Id_visitation'];
					$two_mass_result[$j]['Id_work_day'] = $ticket[$k]['Id_work_day'];
					$two_mass_result[$j]['Detail'] = $ticket[$k]['Detail'];
					$two_mass_result[$j]['Surname'] = $ticket[$k]['Surname'];
					$two_mass_result[$j]['Patronymic'] = $ticket[$k]['Patronymic'];
					$two_mass_result[$j]['Name'] = $ticket[$k]['Name'];
					$two_mass_result[$j]['Id_patient'] = $ticket[$k]['Id_patient'];
				}
			}
			$k++;
		}
		$j++;
	}
	
	
	
	return $app['twig']->render('ticket_find.twig', ['date' => $term, 'Id_doctor' => $Id_doctor, 'result' => $two_mass_result, 'date_obs' => $date_obs]);
}
//персональные данные пациента
 public function patient_data(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$id_patient = $req->get('id_patient');
	
	$patient = $conn -> fetchAssoc('select * from patient where Id_patient = ?',[$id_patient]);
	
	$Id_insurer = $patient['Id_insurer'];
	$patient['Id_insurer_data'] = $conn->fetchColumn('select Name from company where Id_insurer = ?', [$Id_insurer]);
	
	$Id_disability = $patient['Id_disability'];
	$patient['Id_disability_data'] = $conn->fetchColumn('select Number from group_disability where Id_disability = ?', [$Id_disability]);
	
	$Id_organization = $patient['Id_organization'];
	$patient['Id_organization_data'] = $conn->fetchColumn('select Name from organization where Id_organization = ?', [$Id_organization]);
	
	$Id_blood = $patient['Id_blood'];
	$patient['Id_blood_data'] = $conn->fetchColumn('select Number from group_of_blood where Id_blood = ?', [$Id_blood]);
	
	$Id_state = $patient['Id_state'];
	$patient['Id_state_data'] = $conn->fetchColumn('select Name from state_patient where Id_state = ?', [$Id_state]);
	
	$Id_area = $patient['Id_area'];
	$patient['Id_area_data'] = $conn->fetchColumn('select Number from area where Id_area = ?', [$Id_area]);
	
	if ($patient['Sex'] == 0) {$patient['Sex_data'] = "Мужской";}
	else {$patient['Sex_data'] = "Женский";}
	
	if ($patient['Reason'] == 0) {$patient['Reason_data'] = "По месту жительства";}
	else {$patient['Reason_data'] = "По заявлению";}
	
	if ($patient['Rh_factor'] == 0) {$patient['Rh_factor_data'] = "Отрицательный";}
	else {$patient['Rh_factor_data'] = "Положительный";}
	
	return $app['twig']->render('patient_data.twig', ['patient' => $patient]);
}

//амбулаторная карта пациента
 public function patient_card(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$id_patient = $req->get('id_patient');
		
	$card = $conn->fetchAll('select * from Protocol join Doctor join Daily_schedule join Date_work join MO where Id_patient = ? and Protocol.Id_doctor = Doctor.Id_doctor and Protocol.Id_day = Daily_schedule.Id_work_day and Daily_schedule.Id_date_work = Date_work.Id_date_work and Date_work.Id_MO = MO.Id_MO', [$id_patient]);
	
	$FIO = $conn->fetchAssoc('select Surname, Name, Patronymic, Id_patient from Patient where Id_patient = ?', [$id_patient]);
	
	return $app['twig']->render('patient_card.twig', ['card_list' => $card, 'fio' => $FIO]);
}

//ввод посещения
 public function input_of_visit(Request $req, Application $app) {
	$conn = $app['db'];;
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$Id_patient = $req->get('Id_patient');
	
	$FIO = $conn->fetchAssoc('select Surname, Name, Patronymic, Id_patient from Patient where Id_patient = ?', [$Id_patient]);

	$complaints = $conn->fetchAll('select Name, Id_complaints from Complaints');
	$anamnesis = $conn->fetchAll('select Name from Anamnesis');
	$diagnosis = $conn->fetchAll('select Name, Name_additional from Diagnosis');
	$surveys = $conn->fetchAll('select Name from Method_of_examination');
	
	return $app['twig']->render('input_of_visit.twig', ['fio' => $FIO, 'complaints' => $complaints, 'anamnesis' => $anamnesis, 'diagnosis' => $diagnosis, 'surveys' => $surveys]);
}

//Вывод подробного протокола посещения
 public function more_info(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$Id_patient = $req->get('Id_patient');
	$Date = $req->get('Date_');
	$Id_visitation = $req->get('Id_visitation');
	$info = $conn->fetchAssoc('select * from Daily_schedule join Protocol join Patient where Patient.Id_patient = ? and Daily_schedule.Date  = ? and Daily_schedule.Id_work_day = Protocol.Id_day and Protocol.Id_visitation = ?', [$Id_patient, $Date, $Id_visitation]);
	
	if (empty ($info['List_with']))
		{$info['List_data'] = "Больничный не требутся";}
	else 
	{
		$info['List_data'] = "С ";
		$info['List_data'] .= $info['List_with'];
		$info['List_data'] .= " по ";
		$info['List_data'] .= $info['List_before'];
	}
	
	if (empty ($info['Period']))
		{$info['Period_data'] = "На учёте не состоит";}
	else 
	{
		$info['Period_data'] = $info['Period'];
	}
	
	$Complaints = $conn->fetchAll('select * from Complaints join Complaints_date where Complaints_date.Id_visitation = ? and Complaints_date.Id_complaints = Complaints.Id_complaints', [$info['Id_visitation']]);
	
	$Anamnesis = $conn->fetchAll('select * from Anamnesis join Anamnesis_date where Anamnesis_date.Id_visitation = ? and Anamnesis_date.Id_anamnesis = Anamnesis.Id_anamnesis', [$info['Id_visitation']]);
	
	$Diagnosis = $conn->fetchAssoc('select * from Diagnosis join Diagnosis_date where Diagnosis_date.Id_visitation = ? and Diagnosis_date.Id_diagnosis = Diagnosis.Id_diagnosis', [$info['Id_visitation']]);
	
	$surveys = $conn->fetchAll ('select Name from Add_surveys join Method_of_examination where Add_surveys.Id_visitation = ? and Add_surveys.Id_method = Method_of_examination.Id_method',[$Id_visitation]);
	
	if ($info['Appointments'] == "Пациент не был на приёме")
	{
		$info['Appointments_bool'] = true;
	}
	else
	{
		$info['Appointments_bool'] = false;
	}
	
	return $app['twig']->render('more_info.twig', ['more_info' => $info, 'surveys' => $surveys, 'complaints' => $Complaints, 'anamnesis' => $Anamnesis, 'diagnosis' => $Diagnosis]);
}

//Вывод подробного протокола посещения пациента из списка
 public function more_info_detail(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$Id_patient = $req->get('Id_patient');
	$Date = $req->get('Date_');
	$Id_visitation = $req->get('Id_visitation');
	$info = $conn->fetchAssoc('select * from Daily_schedule join Protocol join Patient where Patient.Id_patient = ? and Daily_schedule.Date  = ? and Daily_schedule.Id_work_day = Protocol.Id_day and Protocol.Id_visitation = ?', [$Id_patient, $Date, $Id_visitation]);
	
	if (empty ($info['List_with']))
		{$info['List_data'] = "Больничный не требутся";}
	else 
	{
		$info['List_data'] = "С ";
		$info['List_data'] .= $info['List_with'];
		$info['List_data'] .= " по ";
		$info['List_data'] .= $info['List_before'];
	}
	
	if (empty ($info['Period']))
		{$info['Period_data'] = "На учёте не состоит";}
	else 
	{
		$info['Period_data'] = $info['Period'];
	}
	
	$Complaints = $conn->fetchAll('select * from Complaints join Complaints_date where Complaints_date.Id_visitation = ? and Complaints_date.Id_complaints = Complaints.Id_complaints', [$info['Id_visitation']]);
	
	$Anamnesis = $conn->fetchAll('select * from Anamnesis join Anamnesis_date where Anamnesis_date.Id_visitation = ? and Anamnesis_date.Id_anamnesis = Anamnesis.Id_anamnesis', [$info['Id_visitation']]);
	
	$Diagnosis = $conn->fetchAssoc('select * from Diagnosis join Diagnosis_date where Diagnosis_date.Id_visitation = ? and Diagnosis_date.Id_diagnosis = Diagnosis.Id_diagnosis', [$info['Id_visitation']]);
	
	$surveys = $conn->fetchAll ('select Name from Add_surveys join Method_of_examination where Add_surveys.Id_visitation = ? and Add_surveys.Id_method = Method_of_examination.Id_method',[$Id_visitation]);

	if ($info['Appointments'] == "Пациент не был на приёме")
	{
		$info['Appointments_bool'] = true;
	}
	else
	{
		$info['Appointments_bool'] = false;
	}
	
	return $app['twig']->render('more_info_detail.twig', ['more_info' => $info, 'surveys' => $surveys, 'complaints' => $Complaints, 'anamnesis' => $Anamnesis, 'diagnosis' => $Diagnosis]);
}
//Вывод вложений к амбулаторной карте пациента
 public function documents(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$Id_patient = $req->get('Id_patient');
	
	$sertificate = $conn->fetchAll('select * from Graft join MO where Id_patient = ? and MO.Id_MO = Graft.Id_MO', [$Id_patient]);
	
	$survey = $conn->fetchAll('select * from Survey join Method_of_examination where Id_patient = ? and Survey.Id_method = Method_of_examination.Id_method', [$Id_patient]);
	
	
	$signal = $conn->fetchAll('select * from Signal_list where Id_patient = ?', [$Id_patient]);
	return $app['twig']->render('documents.twig', ['sertificate_list' => $sertificate, 'survey_list' =>  $survey, 'signal_list' => $signal, 'id_patient' => $Id_patient]);
}

//вывод сканов документов обследования
 public function survey_content(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$Id_patient = $req->get('Id_patient');
	$Id_survey = $req->get('Id_survey');

	$detail = $conn->fetchAssoc('select * from Survey where Id_patient = ? and Id_survey = ?', [$Id_patient, $Id_survey]);

	return $app['twig']->render('detail.twig', ['detail' => $detail]);
}

//вывод сканов сигнальных листов
 public function signal_content(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$Id_patient = $req->get('Id_patient');
	$Id_signal = $req->get('Id_signal');

	$detail = $conn->fetchAssoc('select * from Signal_list where Id_patient = ? and Id_signal = ?', [$Id_patient, $Id_signal]);

	return $app['twig']->render('detail.twig', ['detail' => $detail]);
}

//сохранение текущей даты //устарело, решено посредством js
 public function save_date(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$date = $req->get('date');

	return $app['twig']->render('date_today.twig', ['date' => $date]);
}

//создание талона
 public function create_ticket(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	
	$id_doctor = $req->get('id_doc');
	$hour = $req->get('hour');
	$minute = $req->get('minut');
	$date = $req->get('date');
	$id_date_work = $conn->fetchColumn('select Id_date_work from Date_work where Id_doctor = ?', [$id_doctor]);

	$conn->insert('Daily_schedule', ['Date' => $date, 'Hour' => $hour, 'Minute' => $minute, 'Detail' => "Интернет-запись", 'Id_date_work' => $id_date_work]);
	$id_work_day = $conn->fetchColumn('select Id_work_day from Daily_schedule where Id_date_work = ? and Hour = ? and Minute = ? and Date = ?', [$id_date_work, $hour, $minute, $date]);

	return $id_work_day;
}

//Добавление физического лица
 public function patient_list(Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
    
	return $app['twig']->render('patient_list.twig');
}

 public function find(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
    
	$surname = $req->get('surname');
	$name = $req->get('name');
	$patronymic = $req->get('patronymic');

	$patients = $conn->fetchAll("select Id_patient, Surname, Name, Patronymic from Patient
	where Surname like ? and Name like ? and Patronymic like ?", ['%'.$surname.'%', '%'.$name.'%', '%'.$patronymic.'%']);
	
	return $app['twig']->render('find_result.twig', ['patients' => $patients]);
}

//Информация о найденном человеке
 public function more_information(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$id_patient = $req->get('id_patient');
	
	$patient = $conn -> fetchAssoc('select * from patient where Id_patient = ?',[$id_patient]);
	
	$Id_insurer = $patient['Id_insurer'];
	$patient['Id_insurer_data'] = $conn->fetchColumn('select Name from company where Id_insurer = ?', [$Id_insurer]);
	
	$Id_disability = $patient['Id_disability'];
	$patient['Id_disability_data'] = $conn->fetchColumn('select Number from group_disability where Id_disability = ?', [$Id_disability]);
	
	$Id_organization = $patient['Id_organization'];
	$patient['Id_organization_data'] = $conn->fetchColumn('select Name from organization where Id_organization = ?', [$Id_organization]);
	
	$Id_blood = $patient['Id_blood'];
	$patient['Id_blood_data'] = $conn->fetchColumn('select Number from group_of_blood where Id_blood = ?', [$Id_blood]);
	
	$Id_state = $patient['Id_state'];
	$patient['Id_state_data'] = $conn->fetchColumn('select Name from state_patient where Id_state = ?', [$Id_state]);
	
	$Id_area = $patient['Id_area'];
	$patient['Id_area_data'] = $conn->fetchColumn('select Number from area where Id_area = ?', [$Id_area]);
	
	if ($patient['Sex'] == 0) {$patient['Sex_data'] = "Мужской";}
	else {$patient['Sex_data'] = "Женский";}
	
	if ($patient['Reason'] == 0) {$patient['Reason_data'] = "По месту жительства";}
	else {$patient['Reason_data'] = "По заявлению";}
	
	if ($patient['Rh_factor'] == 0) {$patient['Rh_factor_data'] = "Отрицательный";}
	else {$patient['Rh_factor_data'] = "Положительный";}
	
	return $app['twig']->render('detail_data.twig', ['patient' => $patient]);
}


//амбулаторная карта пациента при выводе списка пациентов
 public function more_information_card(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$id_patient = $req->get('id_patient');
		
	$card = $conn->fetchAll('select * from Protocol join Doctor join Daily_schedule join Date_work join MO where Id_patient = ? and Protocol.Id_doctor = Doctor.Id_doctor and Protocol.Id_day = Daily_schedule.Id_work_day and Daily_schedule.Id_date_work = Date_work.Id_date_work and Date_work.Id_MO = MO.Id_MO', [$id_patient]);
	
	$FIO = $conn->fetchAssoc('select Surname, Name, Patronymic, Id_patient from Patient where Id_patient = ?', [$id_patient]);
	
	return $app['twig']->render('detail_data_card.twig', ['card_list' => $card, 'fio' => $FIO]);
}


//выдача талона конкретному пациенту
 public function create_protocol(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	
	$Id_patient = $req->get('Id_patient');
	$id_ticket = $req->get('id_ticket');
	$Id_doctor = $req->get('Id_doctor');

	$conn->insert('Protocol', ['Id_day' => $id_ticket, 'Id_patient' => $Id_patient, 'Id_doctor' => $Id_doctor, 'Id_group_observation' => 1, 'Appointments' => "Пациент не был на приёме"]);
	$id_visitation = $conn->fetchColumn('select Id_visitation from Protocol where Id_day = ? and Id_patient = ? and Id_doctor = ?', [$id_ticket, $Id_patient, $Id_doctor]);
	
	return $id_visitation;
}

//результат посещения врача
 public function enter_input_of_visit(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	
	$comp = $req->get('comp');
	$anam = $req->get('anam');
	$Appointments = $req->get('appointments');
	$Inspection = $req->get('Inspection');
	$sop_diag = $req->get('sop_diag');
	$diag = $req->get('diag');
	$Survey = $req->get('Survey');
	$Date_with = $req->get('Date_with');
	$Date_before = $req->get('Date_before');
	$Period = $req->get('Period');
	$Reappearance = $req->get('Reappearance');
	
	$id_visitation = $req->get('id_visitation');
	
	$diagn = explode(".",$diag);
	if (diagn[1] == null)
	{
		$word_to_add = $conn->fetchColumn('select Id_diagnosis from Diagnosis join Protocol where Diagnosis.Name = ? and Protocol.Id_visitation = ?', [$diagn[0], $id_visitation]);
		$conn->insert('Diagnosis_date', ['Id_diagnosis' => $word_to_add, 'Id_visitation' => $id_visitation]);
	}
	else
	{
		$word_to_add = $conn->fetchColumn('select Id_diagnosis from Diagnosis join Protocol where Diagnosis.Name = ? and Diagnosis.Name_additional = ? and Protocol.Id_visitation = ?', [$diagn[0], $diagn[1], $id_visitation]);
		$conn->insert('Diagnosis_date', ['Id_diagnosis' => $word_to_add, 'Id_visitation' => $id_visitation]);
	}
	
	
	$word = explode(",",$Survey);
	
	$i = 0;
	
	while (empty($word[$i]) != True)
	{
		$word_to_add = $conn->fetchAssoc('select Id_method from Method_of_examination where Name = ?', [$word[$i]]);

		$conn->insert('Add_surveys', ['Id_method' => $word_to_add['Id_method'], 'Id_visitation' => $id_visitation]);
		$i++;
	}

	$word = explode(",",$comp);
	
	$i = 0;
	
	while (empty($word[$i]) != True)
	{
		$word_to_add = $conn->fetchAssoc('select Id_complaints from Complaints join Protocol where Complaints.Name = ? and Protocol.Id_visitation = ?', [$word[$i], $id_visitation]);
		$conn->insert('Complaints_date', ['Id_complaints' => $word_to_add['Id_complaints'], 'Id_visitation' => $id_visitation]);
		
		$i++;
	}
	
	
	$word = explode(",",$anam);
	
	$i = 0;
	
	while (empty($word[$i]) != True)
	{
		$word_to_add = $conn->fetchAssoc('select Id_anamnesis from Anamnesis join Protocol where Anamnesis.Name = ? and Protocol.Id_visitation = ?', [$word[$i], $id_visitation]);
		$conn->insert('Anamnesis_date', ['Id_anamnesis' => $word_to_add['Id_anamnesis'], 'Id_visitation' => $id_visitation]);
		
		$i++;
	}
		
	$word = explode(",",$sop_diag);
	
	$i = 0;
		
	$sql1 = "UPDATE Protocol SET 
	Appointments = '$Appointments',
	Inspection = '$Inspection',
	List_with = '$Date_with',
	List_before = '$Date_before',
	Period = '$Period',
	Reappearance = '$Reappearance'
	WHERE Id_visitation = $id_visitation";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute();
	
	
	return $i;
}



//Вывод персональных данных пациента для редактирования
 public function show_patient_for_edit(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	
	$id_patient = $req->get('Id_patient');
	
	$patient = $conn -> fetchAssoc('select * from patient where Id_patient = ?',[$id_patient]);
	
	$Id_insurer = $patient['Id_insurer'];
	$patient['Id_insurer_data'] = $conn->fetchColumn('select Name from company where Id_insurer = ?', [$Id_insurer]);
	$company =  $conn->fetchAll('select Name from company');
	
	$Id_disability = $patient['Id_disability'];
	$patient['Id_disability_data'] = $conn->fetchColumn('select Number from group_disability where Id_disability = ?', [$Id_disability]);
	$group_disability =  $conn->fetchAll('select Number from group_disability');
	
	$Id_organization = $patient['Id_organization'];
	$patient['Id_organization_data'] = $conn->fetchColumn('select Name from organization where Id_organization = ?', [$Id_organization]);
	$organization = $conn->fetchAll('select Name from organization');

	$Id_blood = $patient['Id_blood'];
	$patient['Id_blood_data'] = $conn->fetchColumn('select Number from group_of_blood where Id_blood = ?', [$Id_blood]);
	
	$Id_state = $patient['Id_state'];
	$patient['Id_state_data'] = $conn->fetchColumn('select Name from state_patient where Id_state = ?', [$Id_state]);
	$state = $conn->fetchAll('select Name from state_patient');
	
	$Id_area = $patient['Id_area'];
	$patient['Id_area_data'] = $conn->fetchColumn('select Number from area where Id_area = ?', [$Id_area]);
	$area = $conn->fetchAll('select Number from Area');

	if ($patient['Sex'] == 0) {$patient['Sex_data'] = "Мужской";}
	else {$patient['Sex_data'] = "Женский";}
	
	if ($patient['Reason'] == 0) {$patient['Reason_data'] = "По месту жительства";}
	else {$patient['Reason_data'] = "По заявлению";}
	
	if ($patient['Rh_factor'] == 0) {$patient['Rh_factor_data'] = "Отрицательный";}
	else {$patient['Rh_factor_data'] = "Положительный";}
	return $app['twig']->render('personal_info_edit.twig', ['patient' => $patient, 'group_disability' => $group_disability, 'state' => $state, 'organization' => $organization, 'company' => $company, 'area' => $area]);
}


//Редактирование персональных данных пациента
 public function edit_patient_info(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
		
	$Id_patient = $req->get('Id_patient');
	$Surname = $req->get('Surname');
	$Name = $req->get('Name');
	$Patronymic = $req->get('Patronymic');
	$Sex_data = $req->get('Sex_data');
	if ($Sex_data == "Мужской") {$Sex = 0 ;}
	else {$Sex = 1 ;}
	
	$Date_born = $req->get('Date_born');
	$Number_history = $req->get('Number_history');
	$Date_with = $req->get('Date_with');
	$Date_before = $req->get('Date_before');
	$Series_number = $req->get('Series_number');
	$Passport = $req->get('Passport');
	$SNILS = $req->get('SNILS');
	$OMS = $req->get('OMS');
	$Id_insurer_data = $req->get("Id_insurer_data");
	$Id_insurer =  $conn->fetchColumn('select Id_insurer from Company where Name = ?', [$Id_insurer_data]);
	
	$Address_propis = $req->get('Address_propis');
	$Address_living = $req->get('Address_living');
	$Id_area_data = $req->get('Id_area_data');
	$Id_area =  $conn->fetchColumn('select Id_area from Area where Number = ?', [$Id_area_data]);
	
	$Id_organization_data = $req->get('Id_organization_data');
	$Id_organization = $conn->fetchColumn('select Id_organization from Organization where Name = ?', [$Id_organization_data]);
	$Reason_data = $req->get('Reason_data');
	if ($Reason_data == "По месту жительства") {$Reason_data = 0 ;}
	else {$Reason_data = 1 ;}
		
	$Phone = $req->get('Phone');
	$Id_blood_data = $req->get('Id_blood_data');
	$Id_blood = $conn->fetchColumn('select Id_blood from Group_of_blood where Number = ?', [$Id_blood_data]);
	
	$Rh_factor_data = $req->get('Rh_factor_data');
	if ($Rh_factor_data == "Отрицательный") {$Rh_factor = 0;}
	else {$Rh_factor = 1 ;}
	
	$Alergic = $req->get('Alergic');
	$Id_disability_data = $req->get('Id_disability_data');
	$Id_disability =$conn->fetchColumn('select Id_disability from Group_disability where Number = ?', [$Id_disability_data]);
	
	$Id_state_data = $req->get('Id_state_data');
	$Id_state = $conn->fetchColumn('select Id_state from State_patient where Name = ?', [$Id_state_data]);
	
	$FIO_relative = $req->get('FIO_relative');
	$Phone_relative = $req->get('Phone_relative');
	
	
	$patient = $conn -> fetchAssoc('select * from Patient where Id_patient = ?',[$Id_patient]);
	
	$sql1 = "UPDATE Patient SET 
	Surname = '$Surname' , 
	Name = '$Name' , 
	Patronymic = '$Patronymic' , 
	Sex = '$Sex' , 
	Date_born = '$Date_born' , 
	Number_history = '$Number_history' , 
	Date_with = '$Date_with' , 
	Date_before = NULL , 
	Series_number = '$Series_number' , 
	Passport = '$Passport' , 
	SNILS = '$SNILS' , 
	OMS = '$OMS' , 
	Id_insurer = '$Id_insurer' , 
	Address_propis = '$Address_propis' , 
	Address_living = '$Address_living' , 
	Id_area = '$Id_area' , 
	Id_organization = '$Id_organization' , 
	Phone = '$Phone' , 
	Id_blood = '$Id_blood' , 
	Rh_factor = '$Rh_factor' , 
	Alergic = '$Alergic' , 
	Id_disability = '$Id_disability' , 
	Id_state = '$Id_state' , 
	FIO_relative = '$FIO_relative' , 
	Phone_relative = '$Phone_relative' 
	WHERE Id_patient = $Id_patient";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute();
	
	return 1;
}


//удаление пустых протокола посещения и талона 

 public function delete_protocol_ticket(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	
	$id_ticket = $req->get('Id_ticket');
	
	$id_visitation = $req->get('Id_visitation');
	
	$conn->DELETE('Protocol', ['Id_visitation' => $id_visitation]);
	$conn->DELETE('Daily_schedule', ['Id_work_day' => $id_ticket]);

	$id_patient = $req->get('id_patient');
		
	$card = $conn->fetchAll('select * from Protocol join Doctor join Daily_schedule join Date_work join MO where Id_patient = ? and Protocol.Id_doctor = Doctor.Id_doctor and Protocol.Id_day = Daily_schedule.Id_work_day and Daily_schedule.Id_date_work = Date_work.Id_date_work and Date_work.Id_MO = MO.Id_MO', [$id_patient]);
	
	$FIO = $conn->fetchAssoc('select Surname, Name, Patronymic, Id_patient from Patient where Id_patient = ?', [$id_patient]);
	
	return $app['twig']->render('patient_card.twig', ['card_list' => $card, 'fio' => $FIO]);
}



}
