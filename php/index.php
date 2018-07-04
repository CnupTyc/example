<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
Request::enableHttpMethodParameterOverride();

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(),
['twig.path' => __DIR__ . '/../view']);
$app->register(new Silex\Provider\SessionServiceProvider());	


$app['debug'] = true;

//страница авторизации
$app->get('/', function () use ($app) {
    /**@var $conn Connection */
	$app['session']->clear();
    return $app['twig']->render('login.twig', ['NotLogin' => null]);
});

$app->post('/', function () use ($app) {
    return $app->redirect('/');
});

$app->register(new Silex\Provider\DoctrineServiceProvider(),
['db.options' => ['driver' => 'pdo_mysql', 'dbname' => 'diploma', 'charset' => 'utf8']]);
$app['debug'] = true;

//рабочая страница врача
$app->post('/work_page', function (Request $req) use ($app) {    
    $conn = $app['db'];
	$user_doctor = $conn->fetchAll('select * from doctor');
	
	$app['session']->clear();
	
    $Login = $req->get('Login');
    $Password = $req->get('Password');
	
	foreach ($user_doctor as $usr) {
				if ($usr['Login'] == $Login && $usr['Password'] == $Password) {
					$app['session']->set('account', array('user' => $usr));
					return $app->redirect('/work_page');
				}	
	}
	return $app['twig']->render('login.twig', ['NotLogin' => "Ошибка ввода данных"]);

});

//Подключаем контроллеры для доктора
$app->mount('/', new doctor_controllers\Doctor_controllers());
//Подключаем СПР
$app->mount('/', new system_accepting_decision\System_accepting_decision());


$app->run();
?>