<?php
// web/system_accepting_decision.php
namespace system_accepting_decision;
require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
Request::enableHttpMethodParameterOverride();
use Silex\Application;
use Silex\Api\ControllerProviderInterface;


class System_accepting_decision implements ControllerProviderInterface {
 
  public function connect(Application $application) {
    $factory=$application['controllers_factory'];
	$factory->get('/help','System_accepting_decision\system_accepting_decision::help');
	$factory->get('/show_about_diagnosis','System_accepting_decision\system_accepting_decision::show_about_diagnosis');
	$factory->get('/print','System_accepting_decision\system_accepting_decision::print');

    return $factory;
  }
 

//система принятия решений
 public function help(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);

	$complaints_text = $req->get('comp');
	$anamnesis_text = $req->get('anam');

	$diagnosis = array ();
	$symptom = array ();
	$i = 0;
	$ii = 0;
	$word_comp = explode(",",$complaints_text);
	$word_anam = explode(",",$anamnesis_text);
	
	while (empty($word_comp[$i]) == false)
	{
			$id_complaint = $conn->fetchColumn('Select Id_complaints from Complaints where Name = ?', [$word_comp[$i]]);
			$symptom[$i]['Symptom'] = $word_comp[$i];
			$ii = 0;		
					 if (empty($word_anam[0]) == false)
					{
						
						while(empty($word_anam[$ii]) == false)
						{	
							$b=0;
							$id_anamnes = $conn->fetchAll('Select Id_anamnesis from Anamnesis where Name = ?', [$word_anam[$ii]]);
							while(empty($id_anamnes[$b]['Id_anamnesis']) == false)
							{
									$id_diagnosis = $conn->fetchAll('Select Id_diagnosis, Id_anamnesis from Symptoms where Id_complaints = ? and Id_anamnesis = ?', [$id_complaint, $id_anamnes[$b]['Id_anamnesis']]);

									if (empty($id_diagnosis[$b]['Id_anamnesis']) == true)
									{
										$symptom[$i]['Anamnes'] = null; 
										$id_diagnosis = $conn->fetchAll('Select Id_diagnosis, Id_anamnesis from Symptoms where Id_complaints = ?', [$id_complaint]);
																
										$diagnosis = null;
										$j = 0;
										$k = 0;			
										while (empty($id_diagnosis[$j]['Id_diagnosis']) == false)
										{
											$diagnosis[$k] = $conn->fetchAssoc('Select Name, Id_diagnosis from Diagnosis where Id_diagnosis = ?', [$id_diagnosis[$j]['Id_diagnosis']]);
											$symptom[$i]['Id_diagnosis'] = $id_diagnosis[$j]['Id_diagnosis'];

											if (empty($diagnosis[$k]) == False)
											{	
												$k++;
											}
											$j++;
										}
										$symptom[$i]['Diagnosis'] = $diagnosis;
									}
									else
									{
										$symptom[$i]['Anamnes'] = $word_anam[$ii];
										$diagnosis = null;
										$j = 0;
										$k = 0;	
										if(empty($id_diagnosis[$j]['Id_diagnosis']) == false)
										{							
											while (empty($id_diagnosis[$j]['Id_diagnosis']) == false)
											{

												$diagnosis[$k] = $conn->fetchAssoc('Select Name, Id_diagnosis from Diagnosis where Id_diagnosis = ?', [$id_diagnosis[$j]['Id_diagnosis']]);

												$symptom[$i]['Id_diagnosis'] = $id_diagnosis[$j]['Id_diagnosis'];
												if (empty($diagnosis[$k]) == False)
												{	
													$k++;
												}
											$j++;
											}
										$symptom[$i]['Diagnosis'] = $diagnosis;
										}
									}
									$b++;
							}
							$ii++;
						}
					}
					else
					{
						$symptom[$i]['Anamnes'] = null; 
						$id_diagnosis = $conn->fetchAll('Select Id_diagnosis, Id_anamnesis from Symptoms where Id_complaints = ?', [$id_complaint]);
						$flag = $conn->fetchAssoc('Select Id_anamnesis from Symptoms where Id_complaints = ?', [$id_complaint]);
												
						if ($flag['Id_anamnesis'] == NULL)
						{
							$diagnosis = null;
							$j = 0;
							$k = 0;			
							while (empty($id_diagnosis[$j]['Id_diagnosis']) == false)
							{
								if ($id_diagnosis[$j]['Id_anamnesis'] == null)
								{
									$diagnosis[$k] = $conn->fetchAssoc('Select Name, Id_diagnosis from Diagnosis where Id_diagnosis = ?', [$id_diagnosis[$j]['Id_diagnosis']]);

									$symptom[$i]['Id_diagnosis'] = $id_diagnosis[$j]['Id_diagnosis'];

									if (empty($diagnosis[$k]) == False)
									{	
										$k++;
									}
								}
								$j++;
							}
							$symptom[$i]['Diagnosis'] = $diagnosis;
						}
						else
						{
							$symptom[$i]['Symptom'] = null;
							$symptom[$i]['Anamnes'] = null;
							$symptom[$i]['Diagnosis']['Name'] = null;
							$symptom[$i]['Diagnosis'] = null;
							$symptom[$i]['Id_diagnosis'] = null;

						}
					}
		$i++;
	}	
	return $app['twig']->render('help.twig', ['diagnosis' => $symptom]);
}

public function show_about_diagnosis(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	$Id_diagnosis = $req->get('Id_diagnosis');
	$therapy = $conn->fetchAll('Select Name, Dosage from Therapy join Medicament where Therapy.Id_diagnosis = ? and Therapy.Id_medicament = Medicament.Id_medicament', [$Id_diagnosis]);
	
	$observ = $conn->fetchAll('Select Name from Survey_for_diagnosis join Method_of_examination where Survey_for_diagnosis.Id_diagnosis = ? and Survey_for_diagnosis.Id_method = Method_of_examination.Id_method', [$Id_diagnosis]);

	return $app['twig']->render('show_about_diagnosis.twig', ['therapy' => $therapy, 'observ' => $observ]);
}
//Подготовка печати для справочников
 public function print(Request $req, Application $app) {
	$conn = $app['db'];
	if (empty($app['session']->get('account'))) return $app['twig']->render('login.twig', ['NotLogin' => "Требуется авторизация"]);
	
	$a = $req->get('input_surv');
	
	return $app['twig']->render('print.twig');
}

}
