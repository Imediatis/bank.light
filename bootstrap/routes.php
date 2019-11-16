<?php
use Digitalis\Core\Controllers\OperationsApiController;
use Digitalis\Core\Controllers\OperatorApiController;
use Digitalis\Core\Middlewares\ApiTokenValidationMiddleware;
use Digitalis\Core\Middlewares\BranchBoxLogMiddleware;
use Digitalis\Core\Middlewares\ClientFilterMiddleware;
use Slim\App;

//$app = new \Slim\App();

$c = $app->getContainer();

$app->group('', function (App $app) {
	$app->post('/Login', OperatorApiController::class . ':login')->setName('api.login');
	$app->get('/CheckLogin/{login}', OperatorApiController::class . ':userLoggedOut')->setName('api.checkLogin');
	$app->get('/SetLastLogout/{login}', OperatorApiController::class . ':setLastLogout')->setName('api.setLastLogout');
	$app->get('/SetLastLogin/{login}', OperatorApiController::class . ':setLastLogin')->setName('api.setLastLogin');
	$app->get('/SetLastAction/{login}', OperatorApiController::class . ':setLastAction')->setName('api.setLastAction');
	$app->get('/GetUser/{login}', OperatorApiController::class . ':getUser')->setName('api.GetUser');
	$app->post('/ChangePwd/{login}', OperatorApiController::class . ':changePwd')->setName('api.ChangePwd');
	$app->post('/boxOpenClose/{boxCode}', OperationsApiController::class . ':openCloseBox')->setName('api.boxOpenClose')->add(new BranchBoxLogMiddleware($app->getContainer()));
	$app->post('/branchOpenClose/{branchCode}', OperationsApiController::class . ':openCloseBranch')->setName('api.branchOpenClose')->add(new BranchBoxLogMiddleware($app->getContainer()));
	$app->get('/Partner/{branchCode}', OperationsApiController::class . ':getPartners')->setName('api.getPartners');
	$app->get('/Typepiece', OperationsApiController::class . ':getTypePieces')->setName('api.getTpiece');
	$app->get('/Client/{numCpt}/{partner}', OperationsApiController::class . ':getClient')->setName('api.getClient');
})->add(new ApiTokenValidationMiddleware($c))->add(new ClientFilterMiddleware($c));