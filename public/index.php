<?php
declare(strict_types=1);
require_once __DIR__ . "/../app/Db.php";
require_once __DIR__ . "/../app/Auth.php";
require_once __DIR__ . "/../app/Controllers/DashboardController.php";
require_once __DIR__ . "/../app/Controllers/AuthController.php";
require_once __DIR__ . "/../app/Controllers/TicketController.php";
require_once __DIR__ . "/../app/Controllers/ReportController.php";
require_once __DIR__ . "/../app/Services/LighthouseService.php";
require_once __DIR__ . "/../app/Services/SeoAnalyzer.php";
use App\Db; use App\Auth;
use App\Controllers\DashboardController;
use App\Controllers\AuthController;
use App\Controllers\TicketController;
use App\Controllers\ReportController;
$config=['db'=>['dsn'=>'mysql:host=localhost;dbname=mds;charset=utf8mb4','user'=>'root','pass'=>'']];
$db=new Db($config['db']['dsn'],$config['db']['user'],$config['db']['pass']); $auth=new Auth($db);
$uri=parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)?:'/'; $method=$_SERVER['REQUEST_METHOD'];
if($uri==='/login'||$uri==='/register'){ $ctrl=new AuthController($db,$auth);
  if($method==='POST'&&$uri==='/login'){ $ctrl->postLogin(); exit; }
  if($method==='POST'&&$uri==='/register'){ $ctrl->postRegister(); exit; }
  echo $ctrl->showLogin(); exit; }
if($uri==='/logout'&&$method==='POST'){ (new AuthController($db,$auth))->logout(); exit; }
if($uri==='/'){ $auth->requireLogin(); echo (new DashboardController($db,$auth))->index(); exit; }
if($uri==='/tickets'){ echo (new TicketController($db,$auth))->index(); exit; }
if($uri==='/tickets/store'&&$method==='POST'){ (new TicketController($db,$auth))->store(); exit; }
if($uri==='/tickets/update'&&$method==='POST'){ (new TicketController($db,$auth))->update(); exit; }
if($uri==='/reports'){ echo (new ReportController($db,$auth))->index(); exit; }
if($uri==='/reports/upload'&&$method==='POST'){ (new ReportController($db,$auth))->upload(); exit; }
if($uri==='/reports/analyze-url'&&$method==='POST'){ (new ReportController($db,$auth))->analyzeUrl(); exit; }
if($uri==='/reports/analyze-bulk'&&$method==='POST'){ (new ReportController($db,$auth))->analyzeBulk(); exit; }
if($uri==='/reports/demo'){ (new ReportController($db,$auth))->generateDemo(); exit; }
if($uri==='/reports/api/detail'){ (new ReportController($db,$auth))->apiReportDetail(); exit; }
if($uri==='/api/reports/last10'){ (new ReportController($db,$auth))->apiLast10(); exit; }
http_response_code(404); echo "404";