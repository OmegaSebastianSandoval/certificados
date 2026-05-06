<?php

/**
 *
 */

class Administracion_loginuserController extends Controllers_Abstract
{

	protected $mainModel;
	protected $route;
	protected $_csrf_section = "login_admin";
	public $csrf;

	public function init()
	{
		$this->mainModel = new Core_Model_DbTable_User();
		$this->route = "/administracion/users";
		$this->_view->route = $this->route;
		$this->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		parent::init();
	}
	public function indexAction()
	{
		// error_reporting(E_ALL);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$recaptchaSecret = '6LfFDZskAAAAAOvo1878Gv4vLz3CjacWqy08WqYP';
			$recaptchaResponse = $_POST['g-recaptcha-response'];
			if (empty($recaptchaResponse)) {
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit;
			}

			$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
			$responseKeys = json_decode($response, true);

			if (intval($responseKeys["success"]) !== 1) {
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit;
			}
		} else {
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			exit;
		}
		Session::getInstance()->set("error_login", "");
		$isPost = $this->getRequest()->isPost();
		$user = $this->_getSanitizedParam("user");
		$password = $this->_getSanitizedParam("password");
		$csrf = $this->_getSanitizedParam("csrf");


		$email = $this->_getSanitizedParam("email");
		if ($email) {
			Session::getInstance()->set("error_login", "El Usuario se encuentra inactivo.");
			header('Location: /administracion/');
			return;
		}
		$bloqueosModel = new Administracion_Model_DbTable_Bloqueos();
		// Obtiene información de bloqueos anteriores
		$infoBloqueo = $bloqueosModel->getList(
			"bloqueo_usuario = '$user' or bloqueo_ip = '" . $_SERVER['REMOTE_ADDR'] . "' ",
			"bloqueo_id DESC"
		)[0];
		// Manejo de intentos fallidos
		$intentos = (int)$infoBloqueo->bloqueo_intentosfallidos;
		$fechaUltimoIntento = $infoBloqueo->bloqueo_fechaintento;
		$fechaUltimoIntento = new DateTime($fechaUltimoIntento);
		$fechaActual = new DateTime();
		$diferencia = $fechaActual->getTimestamp() - $fechaUltimoIntento->getTimestamp();

		// Bloquea al usuario si excede los intentos permitidos
		if ($intentos >= 3 && $diferencia <= 900) {
			Session::getInstance()->set("error_login", "El Usuario ha sido bloqueado por 15 minutos por intentos fallidos.");
			header('Location: /administracion/');
			return;
		}

		// Registra el intento fallido
		$dataBloque = array();
		$dataBloque['bloqueo_usuario'] = $user;
		$dataBloque['bloqueo_intentosfallidos'] = $this->getIntentos($user);
		$dataBloque['bloqueo_ip'] = $_SERVER['REMOTE_ADDR'];
		$bloqueosModel->insert($dataBloque);


		$isError = false;
		$busco = "no";
		$error = 0;
		if ($isPost == true && $user && $password && $this->csrf == $csrf) {
			$userModel = new core_Model_DbTable_User();
			$busco = "si";
			if ($userModel->autenticateUser($user, $password) == true) {
				$resUser = $userModel->searchUserByUser($user);
				if ($resUser->user_state == 1) {
					Session::getInstance()->set("kt_login_id", $resUser->user_id);
					Session::getInstance()->set("kt_login_level", $resUser->user_level);
					Session::getInstance()->set("kt_login_user", $resUser->user_user);
					Session::getInstance()->set("kt_login_name", $resUser->user_names . " " . $resUser->user_lastnames);
					Session::getInstance()->set("user_ip", $this->getRealIp());
					Session::getInstance()->set("user_agent", $_SERVER['HTTP_USER_AGENT']);
					$omegaToken = md5($resUser->user_id . "_OMEGA");
					Session::getInstance()->set("OMEGA_TOKEN", $omegaToken);
					Session::getInstance()->set("kt_login_uid", $resUser->user_uid);
					$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
					$_SESSION['kt_stw_ows'] = md5($_SERVER['HTTP_HOST'] . "OMEGA");
					//LOG
					$data['log_tipo'] = "LOGIN";
					$data['log_usuario'] = $resUser->user_user;
					$infoInicioSesion = [
						'usuario' => $resUser->user_user,
						'fecha' => date('Y-m-d H:i:s'),
						'ip' => $_SERVER['REMOTE_ADDR'],
						'navegador' => $_SERVER['HTTP_USER_AGENT'],
					];
					$data['log_log'] = print_r($infoInicioSesion, true);
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
					$infoBloqueo = $bloqueosModel->getList("bloqueo_usuario = '$user'", "bloqueo_id DESC");
					if (count($infoBloqueo) > 0) {
						foreach ($infoBloqueo as $info) {
							$bloqueosModel->deleteRegister($info->bloqueo_id);
						}
					}
				} else {
					$isError = true;
					$error = 3;
					Session::getInstance()->set("error_login", "El Usuario se encuentra inactivo.");
				}
			} else {
				$isError = true;
				Session::getInstance()->set("error_login", "El Usuario o Contraseña son incorrectos.");
			}
		} else {
			$isError = true;
			$error = 1;
			Session::getInstance()->set("error_login", "Lo sentimos ocurrio un error intente de nuevo.");
		}
		if ($isError == false) {
			header("Location: /administracion/panel");
		} else {
			//LOG
			$data['log_tipo'] = "LOGIN FALLIDO";
			$data['log_usuario'] = $user;
			$infoInicioSesion = [
				'usuario' => $user,
				'fecha' => date('Y-m-d H:i:s'),
				'ip' => $_SERVER['REMOTE_ADDR'],
				'navegador' => $_SERVER['HTTP_USER_AGENT'],
			];
			$data['log_log'] = print_r($infoInicioSesion, true);
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
			header('Location: /administracion/');
		}
	}

	function getRealIp()
	{
		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			return $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]; // Proxy
		} else {
			return $_SERVER['REMOTE_ADDR']; // IP normal
		}
	}
	// Método para obtener el número de intentos fallidos de un usuario
	public function getIntentos($user)
	{
		$bloqueosModel = new Administracion_Model_DbTable_Bloqueos();

		// Obtiene el último registro de bloqueo del usuario
		$infoBloqueo = $bloqueosModel->getList("bloqueo_usuario = '$user'", "bloqueo_id DESC")[0];

		// Incrementa el contador de intentos fallidos
		$intento = $infoBloqueo->bloqueo_intentosfallidos ?? 0;
		$intento = $intento + 1;

		// Devuelve el número de intentos
		return $intento;
	}



	public function forgotpasswordAction()
	{
		$this->setLayout('blanco');
		$this->_csrf_section = "login_admin";
		$modelUser = new Core_Model_DbTable_User();
		$email = $this->_getSanitizedParam("email");
		$error = true;
		$message = "Si su correo es válido, recibirá un enlace para recuperar su contraseña.";

		$filter = " user_email = '" . $email . "' ";
		$user = $modelUser->getList($filter, "")[0];
		$id = $user->user_id;
		Session::getInstance()->set("error_olvido", $message);
		if ($user) {
			$sendingemail = new Core_Model_Sendingemail($this->_view);
			$code = Session::getInstance()->get('csrf')['page_csrf'];
			$modelUser->editCode($id, $code);
			$user = $modelUser->getById($user->user_id);
			if ($sendingemail->forgotpassword($user) == true) {
				$error = false;
				$message = "Se ha enviado a su correo un mensaje de recuperación de contraseña.";
				Session::getInstance()->set("mensaje_olvido", $message);
				Session::getInstance()->set("error_olvido", "");
			} else {
				$message = "Lo sentimos ocurrio un error y no se pudo enviar su mensaje";
				Session::getInstance()->set("error_olvido", $message);
			}
		}
		header('Location: /administracion/index/olvido');
	}

	public function logoutAction()
	{
		//LOG
		$data['log_tipo'] = "LOGOUT";
		$logModel = new Administracion_Model_DbTable_Log();
		$logModel->insert($data);

		Session::getInstance()->set("kt_login_id", "");
		Session::getInstance()->set("kt_login_level", "");
		Session::getInstance()->set("kt_login_user", "");
		Session::getInstance()->set("kt_login_name", "");
		header('Location: /administracion/');
	}
}
