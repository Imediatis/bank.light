<?php
require_once '../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Digitalis\Core\Models\Reseller;
use Digitalis\Core\Models\EnvironmentManager as EnvMngr;


defined('ENV_FILE') || define('ENV_FILE', '../src/environment.php');

new EnvMngr('../src/environment.php');
$reseller = new Reseller();

$entitiesPath = [
    join(DIRECTORY_SEPARATOR, ['..', "src", 'Core', "Models", "Entities"]),
    join(DIRECTORY_SEPARATOR, ['..', 'src', $reseller->getFolder(), 'Models', 'Entities'])
];

$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;

//Connexion à la base de données
$dbParams = [
    'driver' => 'pdo_mysql',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_general_ci',
    'host' => $reseller->getDbHost(),
    'port'=>$reseller->getDbPort(),
    'user' => $reseller->getDbUser(),
    'password' => $reseller->getDbPwd(),
    'dbname' => $reseller->getDbName()
];

$config = Setup::createAnnotationMetadataConfiguration(
    $entitiesPath,
    $isDevMode,
    $proxyDir,
    $cache,
    $useSimpleAnnotationReader
);

$entityManager = EntityManager::create($dbParams, $config);

return $entityManager;
