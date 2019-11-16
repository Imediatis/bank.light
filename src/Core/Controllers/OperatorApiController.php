<?php
namespace Digitalis\Core\Controllers;

use Digitalis\Core\Models\ViewModels\LoginViewModel;
use Digitalis\Core\Controllers\ApiController;
use Digitalis\Core\Handlers\ErrorHandler;
use Digitalis\Core\Models\ApiResponse;
use Digitalis\Core\Models\Data;
use Digitalis\Core\Models\DataBase\DBase;
use Digitalis\Core\Models\DbAdapters\OperatorDbAdapter;
use Digitalis\Core\Models\Lexique;
use Digitalis\Core\Models\MailWorker;
use Imediatis\EntityAnnotation\ModelState;
use Imediatis\EntityAnnotation\Security\InputValidator;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * AccountApiController Description of AccountApiController here
 * @copyright  2018 IMEDIATIS SARL http://www.imediatis.net
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @author     Sylvin KAMDEM <sylvin@imediatis.net> (Back-end Developper)
 */
class OperatorApiController extends ApiController
{
	public function __construct(Container $container)
	{
		parent::__construct($container);
		parent::setCurrentController(__class__);
	}

	public function login(Request $request, Response $response)
	{
		$model = new LoginViewModel();
		$model = InputValidator::BuildModelFromRequest($model, $request);
		$output = new ApiResponse();
		try {
			if (ModelState::isValid()) {
				$operator = OperatorDbAdapter::getByLogin($model->login);
				if ($operator) {
					if (password_verify($model->pwd, $operator->getPassword())) {
						if ($operator->getIsLogged()) {
							$laction = $operator->getLastAction();
							if ($laction) {
								$now = new \DateTime();
								$diff = $now->diff($laction);
								if ($diff->i < 15) {
									OperatorDbAdapter::setLastLogout($operator->getLogin());
								} else {
									OperatorDbAdapter::setLastLogout($operator->getLogin());
									goto LOGGUSER;
								}
							} else {
								OperatorDbAdapter::setLastLogout($operator->getLogin());
							}
						} else {
							LOGGUSER: OperatorDbAdapter::setLastLogin($operator->getLogin());
							if ($operator->getCaisse()) {
								if (strcmp($operator->getCaisse()->getKey(), $model->boxKey) == 0) {
									$output->found = true;
									$output->data = $operator->toLoggedUser();
								}
							}
						}
					}
				}
			} else {
				$output->message = ApiResponse::ERROR_MODELSTATE;
				$output->modelstateerror = ModelState::getErrors();
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
			$output->message = Lexique::GetString(CUR_LANG, an_error_occured);
			$output->status = false;
			$output->data = null;
		}
		return $this->render($response, $output);
	}

	public function userLoggedOut(Request $request, Response $response)
	{
		$output = new ApiResponse();
		$output->data = false;
		$output->found = true;
		$login = $request->getAttribute('login');
		if ($login) {
			$login = base64_decode($login);
			if (Data::isDecodeString($login)) {
				$operator = OperatorDbAdapter::getByLogin($login);
				if ($operator) {
					$output->data = ($operator->getLastLogout() > $operator->getLastLogin()) || in_array($operator->getStatus(), [0, 2]) || ($operator->getLastIpLogout() != MailWorker::getIpAddress() || !$operator->getIsLogged());
					$output->message = "Operateur trouve";
				} else {
					$output->found = false;
					$output->message = "Opérateur non trouvée-" . $login;
				}
			} else {
				$output->found = false;
				$output->message = "Login non valide";
			}
		} else {
			$output->message = "Impossible de traiter votre requete";
		}

		return $this->render($response, $output);
	}

	public function setLastLogout(Request $request, Response $response)
	{
		$output = new ApiResponse();
		$output->data = false;
		$login = $request->getAttribute('login');
		$login = base64_decode($login);
		if (Data::isDecodeString($login)) {
			$output->data = OperatorDbAdapter::setLastLogout($login);
		}

		return $this->render($response, $output);
	}

	public function setLastLogin(Request $request, Response $response)
	{
		$output = new ApiResponse();
		$output->data = false;
		$login = $request->getAttribute('login');
		if ($login) {
			$login = base64_decode($login);
			if (preg_match('/^[\w.]+$/', $login)) {
				$output->data = OperatorDbAdapter::setLastLogin($login);
			}
		} else {
			$output->message = "Impossible de traiter votre requete";
		}

		return $this->render($response, $output);
	}

	public function setLastAction(Request $request, Response $response)
	{
		$output = new ApiResponse();
		$output->data = false;
		$login = $request->getAttribute('login');
		if ($login) {
			$login = base64_decode($login);
			if (preg_match('/^[\w.]+$/', $login)) {
				$operator = OperatorDbAdapter::getByLogin($login);
				if ($operator) {
					$operator->setLastAction(new \DateTime());
					DBase::getEntityManager()->flush();
					$output->data = true;
				}
			}
		} else {
			$output->message = "Impossible de traiter votre requete";
		}

		return $this->render($response, $output);
	}

	public function getUser(Request $request, Response $response)
	{
		$output = new ApiResponse();
		$output->data = false;
		$login = $request->getAttribute('login');
		if ($login) {
			$login = base64_decode($login);
			if (Data::isDecodeString($login)) {
				$operator = OperatorDbAdapter::getByLogin($login);
				if ($operator) {
					$output->data = $operator->toLoggedUser();
					$output->found = true;
				} else {
					$output->message = Data::getErrorMessage();
				}
			}
		} else {
			$output->message = "Impossible de traiter votre requete";
		}

		return $this->render($response, $output);
	}

	public function changePwd(Request $request, Response $response)
	{
		$output = new ApiResponse();
		InputValidator::InitSlimRequest($request);
		try {
			$login = base64_decode($request->getAttribute('login'));
			$plogin = base64_decode(InputValidator::getString('login'));
			$pwd = base64_decode(InputValidator::getString('currentPwd'));
			$newPwd = base64_decode(InputValidator::getString('newPwd'));
			if (strcmp($login, $plogin) == 0 && Data::isDecodeString($plogin) && Data::isDecodeString($login)) {
				$operator = OperatorDbAdapter::getByLogin($login);
				if ($operator) {
					if (password_verify($pwd, $operator->getPassword())) {
						$operator->setPassword(Data::cryptPwd($newPwd));
						$operator->setStatus(1);
						$operator->setLastLogin(new \DateTime());
						$date = new \DateTime();
						$operator->setLastLogout($date->add(new \DateInterval('PT30S')));
						$ip = MailWorker::getIpAddress();
						$operator->setLastIpLogin($ip);
						$operator->setLastIpLogout($ip);
						DBase::getEntityManager()->flush();
						$output->saved = true;
						$output->data = true;
					} else {
						$output->data = false;
						$output->message = "Invalide username or password: operateur non trouve";
					}
				} else {
					$output->data = false;
					$output->message = "Invalide username or password: operateur non trouve";
				}
			} else {
				$output->message = "Invalide username or password: login non correspondant" . $login . " " . $plogin;
				$output->data = false;
			}
		} catch (\Exception $exc) {
			Data::setErrorMessage(Lexique::GetString(CUR_LANG, an_error_occured));
			ErrorHandler::writeLog($exc);
			$output->message = Lexique::GetString(CUR_LANG, an_error_occured);
		}
		return $this->render($response, $output);
	}
}