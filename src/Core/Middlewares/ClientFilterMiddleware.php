<?php

namespace Digitalis\Core\Middlewares;

use Slim\Container;
use Slim\Http\Body;
use Slim\Http\Request;
use Slim\Http\Response;
use Digitalis\Core\Models\Data;
use Digitalis\Core\Models\EnvironmentManager;

/**
 * ClientFilterMiddleware Middleware qui permet de filtrer les client qui adresse des requêtes à l'api
 *
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM<sylvin@imediatis.net> (Back-end Developper)
 */
class ClientFilterMiddleware
{
    /**
     * Conteneur
     *
     * @var Slim\Container
     */
    private $container;

    /**
     * client
     *
     * @var \Digitalis\core\Models\Reseller
     */
    private $reseller;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->reseller = $container->reseller;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $os = strtolower(addslashes(Data::cgetOS($request->getServerParam("HTTP_USER_AGENT"))));
        $file = realpath(EnvironmentManager::getBaseDir() ) . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, ['repository', 'authorizedclient.json']);
        $authorizedclient = [];
        if (file_exists($file)) {
            $authorizedclient = json_decode(file_get_contents($file));
        }

        if (!in_array($os, $authorizedclient)) {
            $body = new Body(fopen('php://temp', 'r+'));
            $body->write("Unauthorized");
            return $response->withStatus(401, 'Unauthorized')
                ->withHeader('Content-Type', 'applicaiton/json')
                ->withBody($body);
        }

        return $next($request, $response);
    }
}
