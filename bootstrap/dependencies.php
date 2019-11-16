<?php

use Digitalis\Core\Models\Data;
use Digitalis\Core\Models\EnvironmentManager as EnvMngr;
use Digitalis\Core\Models\MailWorker;
use Digitalis\Core\Models\Reseller;
use Digitalis\Core\Models\SessionManager;
use Digitalis\Core\Models\SysConst;
use Digitalis\Core\Models\DbAdapters\JsonClientDbAdapter;

//$app = new Slim\App();

$container = $app->getContainer();

SessionManager::set(SysConst::T_CORE_SHARE_VIEW_F, SysConst::CORE_SHARED_VIEW_F);

$container['debug'] = function () {
    return EnvMngr::isDebug();
};

$container['baseUrl'] = function ($c) {
    return $c->request->getUri()->getScheme() . '://' . $c->request->getUri()->getHost() . (!is_null($c->request->getUri()->getPort()) ? ':' . $c->request->getUri()->getPort() : '') . '/';
};

$container['baseDir'] = function () {
    return realpath(__DIR__ . join(DIRECTORY_SEPARATOR, [DIRECTORY_SEPARATOR, '..'])) . DIRECTORY_SEPARATOR;
};

$container['reseller'] = function ($c) {
    return new Reseller();
};

$container['ipAddress'] = function ($c) {
    return MailWorker::getIpAddress();
};

$container['clientManager'] = function ($c) {
    return new JsonClientDbAdapter();
};

//
//RECUPERATION ET SAUVEGARDE EN SESSION DU SYSTEME D'EXPLOITATION DU CLIENT
//
SessionManager::set(SysConst::CLIENT_OS, Data::cgetOS($container->request->getServerParam("HTTP_USER_AGENT")));
//
//RECUPERATION DE LA ROUTE DEMANDE
//
SessionManager::set(SysConst::R_ROUTE, $container->request->getUri()->getPath());