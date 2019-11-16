<?php
namespace Digitalis\Core\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use Digitalis\Core\Models\Reseller;
use Digitalis\Core\Models\SessionManager;
use Digitalis\Core\Models\SysConst;
use Slim\Container;

/**
 * ResellerMiddleware Getionnaier pour le contrôle des différents clients
 *
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM<sylvin@imediatis.net> (Back-end Developper)
 */
class ResellerMiddleware
{
	/**
	 *
	 * @var \Slim\Container
	 */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function __invoke(Request $request, Response $response, $next)
	{
		$reseller = new Reseller();
		if (!$reseller->currentExist()) {
			return $response->withRedirect("http://imediatis.net", 301);
		}
		SessionManager::set(SysConst::S_RESELLER, serialize($reseller));

		return $next($request, $response);
	}

}