<?php

/**
 *
 */

class Page_indexController extends Page_mainController
{

  public function indexAction() {}
  public function registroAction() {}
  public function olvidoAction() {}
  public function certificadosAction()
  {
    if (
      !Session::getInstance()->get('user') ||
      !isset($_SESSION['kt_stw_ows']) ||
      md5($_SERVER['HTTP_HOST'] . "OMEGA") != $_SESSION['kt_stw_ows']
    ) {
      session_destroy();
      header('Location: /');
      exit;
    }
    if (Session::getInstance()->get('user')->temporal_password == '1') {
      header('Location: /page/index/cambiarClave');
    }
    //Generar Token
    Session::getInstance()->set('csrf_token_user', md5(uniqid(rand(), true)));
    $this->_view->csrf_token = Session::getInstance()->get('csrf_token_user');
  }
  public function cambiarClaveAction()
  {
    $omegaToken = md5(Session::getInstance()->get("user")->identificacion . "_OMEGA");

    if (
      !Session::getInstance()->get('user') ||
      Session::getInstance()->get('user_ip') !==  $this->getRealIp() ||
      !Session::getInstance()->get('user_agent') ||
      Session::getInstance()->get('user_agent') !== $_SERVER['HTTP_USER_AGENT'] ||
      !Session::getInstance()->get('OMEGA_TOKEN') ||
      Session::getInstance()->get('OMEGA_TOKEN') !== $omegaToken

    ) {
      header('Location: /');
    }
    if (Session::getInstance()->get('user')->temporal_password == '0') {
      header('Location: /page/index/certificados');
    }

    /* if ($this->_getSanitizedParam('id')) {

      $id = $this->_getSanitizedParam('identificacion');
      $usersModel = new Administracion_Model_DbTable_Usuarios();
      $user = $usersModel->getById($id);
      $this->_view->user = $user;
    } */
  }
  public function registrarAction()
  {
    $identificacion = $this->_getSanitizedParam('identificacion');
    $identificacion = str_replace(".", "", $identificacion);
    $identificacion = str_replace(",", "", $identificacion);
    $identificacion = str_replace("-", "", $identificacion);
    $identificacion = str_replace(" ", "", $identificacion);
    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $usersModel->getList("identificacion = '$identificacion'", "")[0];

    if (!$user) {
      $response = [
        'icon' => 'error',
        'status' => 'no_user',
        'message' => 'Su NIT no se encuentra registrado, si desea registrarse por favor haga clic en el botón de continuar.',
        'redirect' => '/page/index/solicitud'
      ];
      die(json_encode($response));
    }


    $key = $this->generarClave();
    $user->key = $key;
    $usersModel->editField($user->id, 'password', password_hash($key, PASSWORD_DEFAULT));
    $usersModel->editField($user->id, 'temporal_password', '1');

    $mailModel = new Core_Model_Sendingemail($this->_view);
    $email =  $user->email;
    if (!$email) {
      $response = [
        'icon' => 'error',
        'title' => 'Error',
        'status' => 'error_email',
        'message' => 'Ha ocurrido un error, por favor intente nuevamente.'
      ];
      die(json_encode($response));
    }
    $email = explode('@', $email);
    $email[0] = substr($email[0], 0, 5) . '***';
    $email = implode('@', $email);
    if ($mailModel->enviarRegistro($user) == 1) {
      if ($user->temporal_password == '0' || $user->temporal_password == '1') {
        $response = [
          'icon' => 'success',
          'title' => 'Success',
          'status' => 'success_exist',
          'message' => "El usuario ya se encuentra registrado, se enviará un correo con una contraseña temporal para que pueda ingresar al sistema a la dirección: $email por favor verifique su bandeja de entrada.",
          'email' => $email,
          'id' => $user->id,
          'redirect' => '/'
        ];
      } else {

        $response = [
          'icon' => 'success',
          'title' => 'Success',
          'status' => 'success',
          'message' => "El usuario ya se encuentra registrado, se enviará un correo con una contraseña temporal para que pueda ingresar al sistema a la dirección: $email por favor verifique su bandeja de entrada.",
          'email' => $email,
          'redirect' => '/'
        ];
      }
    } else {
      $response = [
        'icon' => 'error',
        'title' => 'Error',
        'status' => 'error',
        'message' => 'No se ha podido enviar el correo, por favor intente de nuevo o avise al administrador.'
      ];
    }
    die(json_encode($response));
  }
  private function generarClave()
  {
    $longitud = 8;
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $caracteresLongitud = strlen($caracteres);
    $contraseña = '';
    for ($i = 0; $i < $longitud; $i++) {
      $contraseña .= $caracteres[random_int(0, $caracteresLongitud - 1)];
    }
    return $contraseña;
  }
  public function loginAction()
  {
    $this->setlayout("blanco");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $recaptchaSecret = '6LfFDZskAAAAAOvo1878Gv4vLz3CjacWqy08WqYP';
      $recaptchaResponse = $_POST['g-recaptcha-response'];
      if (empty($recaptchaResponse)) {
        $response = [
          'status' => 'error',
          'message' => 'Captcha no válido',
          'refresh' => true
        ];
        die(json_encode($response));
      }

      $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
      $responseKeys = json_decode($response, true);

      if (intval($responseKeys["success"]) !== 1) {
        $response = [
          'status' => 'error',
          'message' => 'Captcha no válido',
          'refresh' => true
        ];
        die(json_encode($response));
      }
    } else {

      $response = [
        'status' => 'error',
        'message' => 'Método no permitido',
        'refresh' => true
      ];
      die(json_encode($response));
    }

    $identificacion = $this->_getSanitizedParam('identificacion');
    $password = $this->_getSanitizedParam('password');

    $identificacion = str_replace(".", "", $identificacion);
    $identificacion = str_replace(",", "", $identificacion);
    $identificacion = str_replace("-", "", $identificacion);
    $identificacion = str_replace(" ", "", $identificacion);

    $email = $this->_getSanitizedParam("email");
    if ($email) {
      $response = [
        'status' => 'error',
        'message' => 'Usuario no encontrado'
      ];
      die(json_encode($response));
    }
    $bloqueosModel = new Administracion_Model_DbTable_Bloqueos();
    // Obtiene información de bloqueos anteriores
    $infoBloqueo = $bloqueosModel->getList(
      "bloqueo_usuario = '$identificacion' or bloqueo_ip = '" . $_SERVER['REMOTE_ADDR'] . "' ",
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
      $response = [
        'status' => 'error',
        'message' => 'Usuario  bloqueado por 15 minutos'
      ];
      die(json_encode($response));
    }

    // Registra el intento fallido
    $dataBloque = array();
    $dataBloque['bloqueo_usuario'] = $identificacion;
    $dataBloque['bloqueo_intentosfallidos'] = $this->getIntentos($identificacion);
    $dataBloque['bloqueo_ip'] = $_SERVER['REMOTE_ADDR'];
    $bloqueosModel->insert($dataBloque);

    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $usersModel->getList("identificacion = '$identificacion'", "")[0];
    if ($user) {
      if (password_verify($password, $user->password)) {
        Session::getInstance()->set('user', $user);
        Session::getInstance()->set("user_ip", $this->getRealIp());
        Session::getInstance()->set("user_agent", $_SERVER['HTTP_USER_AGENT']);
        $omegaToken = md5($identificacion . "_OMEGA");
        $_SESSION['kt_stw_ows'] = md5($_SERVER['HTTP_HOST'] . "OMEGA");
        Session::getInstance()->set("OMEGA_TOKEN", $omegaToken);
        //LOG
        $data['log_tipo'] = "LOGIN";
        $data['log_usuario'] = $identificacion;
        $infoInicioSesion = [
          'usuario' => $identificacion,
          'fecha' => date('Y-m-d H:i:s'),
          'ip' => $_SERVER['REMOTE_ADDR'],
          'navegador' => $_SERVER['HTTP_USER_AGENT'],
        ];
        $data['log_log'] = print_r($infoInicioSesion, true);
        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($data);

        $infoBloqueo = $bloqueosModel->getList("bloqueo_usuario = '$identificacion'", "bloqueo_id DESC");
        if (count($infoBloqueo) > 0) {
          foreach ($infoBloqueo as $info) {
            $bloqueosModel->deleteRegister($info->bloqueo_id);
          }
        }
        session_regenerate_id(true);
        $response = [
          'status' => 'success',
          'message' => 'Bienvenido',
          'redirect' => '/page/index/certificados'
        ];
      } else {
        $response = [
          'status' => 'error',
          'message' => 'Contraseña incorrecta'
        ];
      }
    } else {
      $response = [
        'status' => 'error',
        'message' => 'Usuario no encontrado'
      ];
    }
    die(json_encode($response));
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
  public function logoutAction()
  {
	$this->setlayout("blanco");
    Session::getInstance()->set('user', null);
    header('Location: /');
	exit;
  }
  public function changePassAction()
  {
    $password = $this->_getSanitizedParam('password');
    $password2 = $this->_getSanitizedParam('password2');
    $usersModel = new Administracion_Model_DbTable_Usuarios();
    if (
      !Session::getInstance()->get('user') &&
      $this->_getSanitizedParam('id') &&
      $this->_getSanitizedParam('user-exist') == 1
    ) {
      $user = $usersModel->getById($this->_getSanitizedParam('id'));
    } else {
      $user = Session::getInstance()->get('user');
    }

    if ($password == $password2) {
      $usersModel->editField($user->id, 'password', password_hash($password, PASSWORD_DEFAULT));
      $usersModel->editField($user->id, 'temporal_password', '0');
      $response = [
        'status' => 'success',
        'message' => 'Contraseña cambiada correctamente'
      ];
    } else {
      $response = [
        'status' => 'error',
        'message' => 'Las contraseñas no coinciden'
      ];
    }
    $user = $usersModel->getList("id = '$user->id'", "")[0];
    Session::getInstance()->set('user', $user);

    die(json_encode($response));
  }

  public function buscarICAAction()
  {
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode(['html' => '<span> Token de seguridad invalido, por favor recargue la pagina para buscar sus documentos </span>']));
    }
    $year = $this->_getSanitizedParam('year');
    $nit = Session::getInstance()->get('user')->identificacion;
    //2025-05-13 algunos usuarios se loguearon con puntos y no consulta.
    $nit = str_replace(".", "", $nit);
    $nit = str_replace(",", "", $nit);
    $nit = str_replace(" ", "", $nit);

    /* // Crea el patrón de búsqueda con el NIT y el año
    $patternUpper = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Reteica_Reteiva/*" . $nit . "-RTEICA *-GANP-*" . $year . ".pdf";
    $patternLower = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Reteica_Reteiva/*" . $nit . "-RTEICA *-ganp-*" . $year . ".pdf";

    // Busca archivos que coincidan con el patrón
    $filesLower = glob($patternLower);
    $filesUpper = glob($patternUpper);
    $files = array_merge($filesLower, $filesUpper);*/

    // Normaliza la ruta base
    $basePath = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Reteica_Reteiva/*" . $nit;

    // Definir patrones de búsqueda
    $patternsold = [
      $basePath . "-RTEICA *{$year}*.pdf",
      $basePath . "-RTE ICA *{$year}*.pdf",
      $basePath . "_RTEICA *{$year}*.pdf",
      $basePath . "_RTE ICA_ *{$year}*.pdf",
      $basePath . "ICA*{$year}*.pdf",
      $basePath . "RTE*{$year}*.pdf",
      $basePath . "ica*{$year}*.pdf",
      $basePath . "_RTE ICA_*{$year}*.pdf",
      $basePath . "_RTE ICA_BOGOTA*{$year}*.pdf",
      $basePath . "*RTEICA *{$year}*.pdf",
      $basePath . "*RTE ICA *{$year}*.pdf",
      $basePath . "*rte ica *{$year}*.pdf",
      $basePath . "*rteica *{$year}*.pdf",
      $basePath . "*rteica *{$year}*.pdf",
    ];
$patterns = [
  // Casos con guión bajo o espacio, RTEICA
  $basePath . "*RTEICA*{$year}.pdf",
  $basePath . "*RTEICA*{$year}*.pdf",
  $basePath . "*RTE ICA*{$year}*.pdf",
  $basePath . "*_RTEICA_*{$year}*.pdf",
  $basePath . "*-RTEICA*{$year}*.pdf",

  // Casos con RTEIVA
  /*$basePath . "*RTEIVA*{$year}.pdf",
  $basePath . "*RTEIVA*{$year}*.pdf",
  $basePath . "*RTE IVA*{$year}*.pdf",
  $basePath . "*_RTEIVA_*{$year}*.pdf",
  $basePath . "*-RTEIVA*{$year}*.pdf",*/

  // Casos en minúsculas o mezclados
  /*$basePath . "*rteica*{$year}*.pdf",
  $basePath . "*rteiva*{$year}*.pdf",*/

  // Casos más generales
  $basePath . "*ICA*{$year}*.pdf",
  $basePath . "*RETE*{$year}*.pdf",
  $basePath . "*rtei*a*{$year}*.pdf",

  // Casos con texto como IIIBim2025 sin separación
  //$basePath . "*{$year}.pdf",
];

    // Buscar archivos que coincidan con los patrones
    $files = [];
    foreach ($patterns as $pattern) {
      $files = array_merge($files, glob($pattern));
    }

    // Remover duplicados
    $files = array_unique($files);

    // Chequea si se encontraron archivos y los imprime
    $html = "";
    if ($files) {
      foreach ($files as $file) {
        $html .= "<div class='col-12 mb-3'>
                    <a href='/files/Bancolombia/Reteica_Reteiva/" . basename($file) . "' target='_blank'>
                      " . basename($file) . "
                      <i class='fa-solid fa-download'></i>
                    </a>
                  </div>";
      }
    } else {
      $html .= "<div class='col-12'>
                  No se encontraron archivos con el NIT $nit y el año $year.
                </div>";
    }
    die(json_encode(['html' => $html]));
  }
  public function buscarIVAAction()
  {
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode(['html' => '<span> Token de seguridad invalido, por favor recargue la pagina para buscar sus documentos </span>']));
    }
    $year = $this->_getSanitizedParam('year');
    $nit = Session::getInstance()->get('user')->identificacion;
    //2025-05-13 algunos usuarios se loguearon con puntos y no consulta.
    $nit = str_replace(".", "", $nit);
    $nit = str_replace(",", "", $nit);
    $nit = str_replace(" ", "", $nit);

    /* // Crea el patrón de búsqueda con el NIT y el año
    $patternUpper = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Reteica_Reteiva/*" . $nit . "-RTE IVA *-GANP-*" . $year . ".pdf";
    $patternLower = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Reteica_Reteiva/*" . $nit . "-RTE IVA *-ganp-*" . $year . ".pdf";

    // Busca archivos que coincidan con el patrón
    $filesLower = glob($patternLower);
    $filesUpper = glob($patternUpper);
    $files = array_merge($filesLower, $filesUpper);*/
    // Normaliza la ruta base
    $basePath = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Reteica_Reteiva/*" . $nit;

    // Definir patrones de búsqueda
    /* 32002-800231885_RTE IVA_VI Bimestre 2024 */
    $patternsold = [
      $basePath . "-RTE IVA *{$year}*.pdf",
      $basePath . "-RTE IVA *{$year}*.pdf",
      $basePath . "_RTEIVA *{$year}*.pdf",
      $basePath . "_RTE IVA_ *{$year}*.pdf",
      $basePath . "IVA*{$year}*.pdf",
      $basePath . "RTE*{$year}*.pdf",
      $basePath . "IVA*{$year}*.pdf",
      $basePath . "_RTE IVA_*{$year}*.pdf",
      $basePath . "*RTEIVA *{$year}*.pdf",
      $basePath . "*RTE IVA *{$year}*.pdf",
      $basePath . "*rte iva *{$year}*.pdf",
    ];
$patterns = [
  // Variantes comunes con guiones o underscores
  $basePath . "*RTEIVA*{$year}.pdf",
  $basePath . "*RTEIVA*{$year}*.pdf",
  $basePath . "*RTE IVA*{$year}*.pdf",
  $basePath . "*_RTEIVA_*{$year}*.pdf",
  $basePath . "*-RTEIVA*{$year}*.pdf",
  $basePath . "*-RTE IVA*{$year}*.pdf",

  // Variantes en minúsculas o mezcladas
  $basePath . "*rteiva*{$year}*.pdf",
  $basePath . "*rte iva*{$year}*.pdf",

  // Casos que contengan simplemente IVA (más general)
  $basePath . "*IVA*{$year}*.pdf",
  $basePath . "*iva*{$year}*.pdf",

  // Último recurso: cualquier archivo con el NIT y el año
  //$basePath . "*{$year}.pdf",
];

    // Buscar archivos que coincidan con los patrones
    $files = [];
    foreach ($patterns as $pattern) {
      $files = array_merge($files, glob($pattern));
    }

    // Remover duplicados
    $files = array_unique($files);
    // Chequea si se encontraron archivos y los imprime
    $html = "";
    if ($files) {
      foreach ($files as $file) {
        $html .= "<div class='col-12 mb-3'>
                    <a href='/files/Bancolombia/Reteica_Reteiva/" . basename($file) . "' target='_blank'>
                      " . basename($file) . "
                      <i class='fa-solid fa-download'></i>
                    </a>
                  </div>";
      }
    } else {
      $html .= "<div class='col-12'>
                  No se encontraron archivos con el NIT $nit y el año $year.
                </div>";
    }
    die(json_encode(['html' => $html]));
  }
  public function buscarFuenteAction()
  {
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode(['html' => '<span> Token de seguridad invalido, por favor recargue la pagina para buscar sus documentos </span>']));
    }
    $year = $this->_getSanitizedParam('year');
    $nit = Session::getInstance()->get('user')->identificacion;
    //2025-05-13 algunos usuarios se loguearon con puntos y no consulta.
    $nit = str_replace(".", "", $nit);
    $nit = str_replace(",", "", $nit);
    $nit = str_replace(" ", "", $nit);

    /* // Crea el patrón de búsqueda con el NIT y el año
    $patternLower = str_replace('\\', '/', FILE_PATH) . "Retefuente/*" . $nit . "*_renta_*" . $year . ".pdf";
    $patternUpper = str_replace('\\', '/', FILE_PATH) . "Retefuente/*" . $nit . "*_RENTA_*" . $year . ".pdf";

    // Busca archivos que coincidan con el patrón
    $filesLower = glob($patternLower);
    $filesUpper = glob($patternUpper);
    $files = array_merge($filesLower, $filesUpper);*/


    $basePath = str_replace('\\', '/', FILE_PATH) . "Retefuente/*" . $nit;
    $basePath2 = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Certificados_renta_der_fiduciaria/*" . $nit;

    // Definir patrones de búsqueda
    $patterns = [
      $basePath . "*_renta_*{$year}*.pdf",
      $basePath . "*_RENTA_*{$year}*.pdf",
      $basePath . "*_renta_ *{$year}*.pdf",
      $basePath . "*_RENTA_ *{$year}*.pdf",
    ];

    $patterns2 = [
      $basePath2 . "*_renta_*{$year}*.pdf",
      $basePath2 . "*_RENTA_*{$year}*.pdf",
      $basePath2 . "*_renta_ *{$year}*.pdf",
      $basePath2 . "*_RENTA_ *{$year}*.pdf",
    ];

    // Buscar archivos
    $files = [];
    foreach ($patterns as $pattern) {
      $files = array_merge($files, glob($pattern));
    }

    $files2 = [];
    foreach ($patterns2 as $pattern2) {
      $files2 = array_merge($files2, glob($pattern2));
    }

    // Unir archivos y eliminar duplicados
    $allFiles = array_unique(array_merge($files, $files2));

    $html = "";
    if ($allFiles) {
      foreach ($allFiles as $file) {
        // Determinar la URL según la ubicación del archivo
        if (strpos($file, "Retefuente") !== false) {
          $url = "/files/Retefuente/" . basename($file);
        } elseif (strpos($file, "Bancolombia/Certificados_renta_der_fiduciaria") !== false) {
          $url = "/files/Bancolombia/Certificados_renta_der_fiduciaria/" . basename($file);
        } else {
          $url = "/files/" . basename($file); // Fallback genérico
        }

        $html .= "<div class='col-12 mb-3'>
                    <a href='$url' target='_blank'>
                      " . basename($file) . "
                      <i class='fa-solid fa-download'></i>
                    </a>
                  </div>";
      }
    } else {
      $html .= "<div class='col-12'>
                No se encontraron archivos con el NIT $nit y el año $year.
              </div>";
    }
    die(json_encode(['html' => $html]));
  }
  
  

  public function buscarProveedoresAction()
  {
    // 1) CSRF
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode([
        'html' => '<span> Token de seguridad inválido, por favor recargue la página. </span>'
      ]));
    }

    // 2) Parámetros
    $year = $this->_getSanitizedParam('year');
    $nit  = Session::getInstance()->get('user')->identificacion;
    $nit  = str_replace(['.', ',', ' '], '', $nit);

    // 3) Ruta base de Proveedores
    $basePath = rtrim(str_replace('\\', '/', FILE_PATH), '/') . '/Pagos';

    // 4) Patrones de búsqueda
    $patterns = [
      // Con tilde
      "{$basePath}/*-Notificación Pago-*{$year}-*{$nit}.pdf",
      "{$basePath}/*-Notificacion Pago-*{$year}-*{$nit}.pdf",
      // Variante sin tilde / mayúsculas, por si acaso
      "{$basePath}/*-NOTIFICACION PAGO-*{$year}-*{$nit}.pdf",
      "{$basePath}/*-NOTIFICACIÓN PAGO-*{$year}-*{$nit}.pdf",
      "{$basePath}/*-*-{$year}-{$nit}.pdf",
    ];

    // 5) Glob de archivos
    $files = [];
    foreach ($patterns as $pat) {
      $files = array_merge($files, glob($pat));
    }
    $files = array_unique($files);

    // 6) Montar el HTML de respuesta
    $html = '';
    if (count($files) > 0) {
      foreach ($files as $file) {
        $filename = basename($file);
        $url      = "/files/Pagos/{$filename}";
        $html    .= "<div class='col-12 mb-3'>
                            <a href='{$url}' target='_blank'>
                              {$filename}
                              <i class='fa-solid fa-download'></i>
                            </a>
                          </div>";
      }
    } else {
      $html = "<div class='col-12'>
                    No se encontraron archivos para el NIT {$nit} en el año {$year}.
                 </div>";
    }

    die(json_encode(['html' => $html]));
  }
  
  
  public function enviarRecuperacionAction()
  {
    $this->setLayout('blanco');
    $response = array();

    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Llamar a la función para verificar los intentos
    $attemptsCheck = $this->valiteAttempts($ip_address);

    // Verificar el resultado de la función
    if (!$attemptsCheck) {
      $res['status'] = 'error';
      $res['message'] = 'Demasiados intentos, por favor intente de nuevo en 15 minutos';
      die(json_encode($res));
    }

    $identificacion = $this->_getSanitizedParam('identificacion');
    $identificacion = str_replace(".", "", $identificacion);
    $identificacion = str_replace(",", "", $identificacion);
    $identificacion = str_replace("-", "", $identificacion);
    $identificacion = str_replace(" ", "", $identificacion);

    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $usersModel->getList("identificacion = '$identificacion'", "")[0];
    $token = md5(uniqid(rand(), true));
    $token_date = date('Y-m-d H:i:s');
    if ($user) {
      $mailModel = new Core_Model_Sendingemail($this->_view);
      $mail = $mailModel->enviarRecuperacion($user, $token);
      if ($mail == '1') {
        $usersModel->editField($user->id, 'token', $token);
        $usersModel->editField($user->id, 'token_date', $token_date);
        $response = [
          'status' => 'success',
          'email' => $this->enmascararCorreo($user->email)
        ];
      }
    } else {
      $response = [
        'status' => 'error',
        'message' => 'Usuario no encontrado'
      ];
    }
    die(json_encode($response));
  }
  private function enmascararCorreo($correo)
  {
    // Encontrar la posición del símbolo '@'
    $posicionArroba = strpos($correo, '@');

    // Obtener el dominio del correo
    $dominio = substr($correo, $posicionArroba);

    // Enmascarar la parte antes del dominio
    $parteEnmascarada = substr($correo, 0, 3) . str_repeat('*', $posicionArroba - 3);

    // Combinar la parte enmascarada con el dominio
    return $parteEnmascarada . $dominio;
  }
  public function recuperacionAction()
  {
    $token = $this->_getSanitizedParam('t');
    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $usersModel->getList("token = '$token'", "")[0];
    if ($user) {
      $token_date = new DateTime($user->token_date);
      $now = new DateTime();
      $interval = $now->diff($token_date);
      if ($interval->h < 1) {
        $this->_view->error = false;
        $this->_view->user = $user;
      } else {
        $this->_view->error = true;
      }
    } else {
      $this->_view->error = true;
    }
  }
  public function recuperarClaveAction()
  {
    $password = $this->_getSanitizedParam('password');
    $password2 = $this->_getSanitizedParam('password2');
    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user_id = $this->_getSanitizedParam('user_id');
    if ($password == $password2) {
      $usersModel->editField($user_id, 'password', password_hash($password, PASSWORD_DEFAULT));
      $usersModel->editField($user_id, 'temporal_password', '0');
      $usersModel->editField($user_id, 'token', '');
      $response = [
        'status' => 'success',
        'message' => 'Contraseña cambiada correctamente'
      ];
    } else {
      $response = [
        'status' => 'error',
        'message' => 'Las contraseñas no coinciden'
      ];
    }
    die(json_encode($response));
  }
  private function valiteAttempts($ip_address)
  {
    $currentTime = time();

    // Inicializar la sesión si no existe
    if (!isset($_SESSION['password_reset_attempts'])) {
      $_SESSION['password_reset_attempts'] = [];
    }

    // Verificar si la IP tiene intentos registrados
    if (isset($_SESSION['password_reset_attempts'][$ip_address])) {
      $attempts = $_SESSION['password_reset_attempts'][$ip_address];

      // Verificar si la IP está bloqueada
      if ($attempts['blocked_until'] && $attempts['blocked_until'] > $currentTime) {
        return false;
      } elseif ($attempts['count'] >= 5) {
        $_SESSION['password_reset_attempts'][$ip_address] = [
          'count' => 0,
          'blocked_until' => $currentTime + 15 * 60 // Bloquear por 15 minutos
        ];
        return false;
      }
    } else {
      // Inicializar intentos para la IP
      $_SESSION['password_reset_attempts'][$ip_address] = [
        'count' => 0,
        'blocked_until' => null
      ];
    }

    // Actualizar el conteo de intentos
    $_SESSION['password_reset_attempts'][$ip_address]['count']++;
    $_SESSION['password_reset_attempts'][$ip_address]['last_attempt'] = $currentTime;

    return true;
  }
  public function solicitudAction() {}
  public function crearSolicitudRegistroAction()
  {
    $this->setLayout('blanco');
    $razon_social = $this->_getSanitizedParam('razon_social');
    $rut_cc_ce_pasaporte = $this->_getSanitizedParam('rut_cc_ce_pasaporte');
    $dv = $this->_getSanitizedParam('dv');
    $email = $this->_getSanitizedParam('email');
    $nombre_contacto = $this->_getSanitizedParam('nombre_contacto');
    $cargo = $this->_getSanitizedParam('cargo');

    $rut_cc_ce_pasaporte = str_replace(".", "", $rut_cc_ce_pasaporte);
    $rut_cc_ce_pasaporte = str_replace(",", "", $rut_cc_ce_pasaporte);
    $rut_cc_ce_pasaporte = str_replace("-", "", $rut_cc_ce_pasaporte);
    $rut_cc_ce_pasaporte = str_replace(" ", "", $rut_cc_ce_pasaporte);

    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $usersModel->getList("identificacion = '$rut_cc_ce_pasaporte'", "")[0];

    if ($user) {
      $response = [
        'status' => 'user_exist',
        'message' => 'Ya existe un usuario con este NIT, iniciá sesión para continuar o recupera tu contraseña'
      ];
      die(json_encode($response));
    }
    $solicitud = new Administracion_Model_DbTable_SolicitudesRegistro();

    $solicitudExiste = $solicitud->getList("rut_cc_ce_pasaporte = '$rut_cc_ce_pasaporte'", "")[0];
    if ($solicitudExiste && $solicitudExiste->estado == 0) {
      $response = [
        'status' => 'solicitud_exist',
        'message' => 'Ya existe una solicitud con este NIT, por favor espere a que sea aprobada'
      ];
      die(json_encode($response));
    }
    if ($solicitudExiste && $solicitudExiste->estado == 1) {
      $response = [
        'status' => 'solicitud_exist',
        'message' => 'Ya existe una solicitud con este NIT  y se encuentra aprobada, por favor inicie sesión'
      ];
      die(json_encode($response));
    }

    $dataSolicitud = array();
    $dataSolicitud['razon_social'] = $razon_social;
    $dataSolicitud['rut_cc_ce_pasaporte'] = $rut_cc_ce_pasaporte;
    $dataSolicitud['dv'] = $dv;
    $dataSolicitud['email'] = $email;
    $dataSolicitud['nombre_contacto'] = $nombre_contacto;
    $dataSolicitud['cargo'] = $cargo;
    $dataSolicitud['estado'] = 0;
    $dataSolicitud['created_at'] = date('Y-m-d H:i:s');

    $validacion = 1;
    if ($razon_social == "" or $razon_social == 1) {
      $validacion = 0;
    }
    if ($rut_cc_ce_pasaporte == "" or $rut_cc_ce_pasaporte == 1) {
      $validacion = 0;
    }
    /*if ($dv == "" or $dv == 1) {
      $validacion = 0;
    }*/
    if ($email == "" or $email == 1 or substr_count($email, '@') != 1) {
      $validacion = 0;
    }
    if ($nombre_contacto == "" or $nombre_contacto == 1) {
      $validacion = 0;
    }
    if ($cargo == "" or $cargo == 1) {
      $validacion = 0;
    }

    if ($validacion == 0) {
      $response = [
        'status' => 'error',
        'message' => 'Por favor complete todos los campos'
      ];
      die(json_encode($response));
    }

    if ($validacion == 1) {
      $id = $solicitud->insert($dataSolicitud);

      // $infoSolicitud = $solicitud->getById($id);
      $solicitud->editField($id, 'estado', '1');

      $userData = array();
      $userData['razon_social'] = $razon_social;
      $userData['identificacion'] = $rut_cc_ce_pasaporte;
      $userData['dv'] = $dv;
      $userData['email'] = $email;
      $userData['contacto_nombre'] = $nombre_contacto;
      $userData['contacto_cargo'] =  $cargo;


      $usersModel = new Administracion_Model_DbTable_Usuarios();
      $idRegistro = $usersModel->insert($userData);

      $user = $usersModel->getById($idRegistro);

      $key = $this->generarClave();
      $user->key = $key;
      $usersModel->editField($user->id, 'password', password_hash($key, PASSWORD_DEFAULT));
      $usersModel->editField($user->id, 'temporal_password', '1');

      $mailModel = new Core_Model_Sendingemail($this->_view);
      $email =  $user->email;
      $email = explode('@', $email);
      $email[0] = substr($email[0], 0, 5) . '***';
      $email = implode('@', $email);
      $userModel = new Administracion_Model_DbTable_Usuario();
      $users = $userModel->getList("", "");
      $mailModel->enviarRegistroUsuarios($user, $users);
    }

    $response = [
      'status' => 'success',
      'message' => 'Solicitud enviada correctamente'
    ];
    die(json_encode($response));
  }
  public function correoAction() {}

  public function crearSolicitudCorreoAction()
  {
    $this->setLayout('blanco');
    $razon_social = $this->_getSanitizedParam('razon_social');
    $rut_cc_ce_pasaporte = $this->_getSanitizedParam('rut_cc_ce_pasaporte');
    $dv = $this->_getSanitizedParam('dv');
    $email = $this->_getSanitizedParam('email');
    $nombre_contacto = $this->_getSanitizedParam('nombre_contacto');
    $cargo = $this->_getSanitizedParam('cargo');
    //honeypot
    $user_id = $this->_getSanitizedParam('user_id');

    $rut_cc_ce_pasaporte = str_replace(".", "", $rut_cc_ce_pasaporte);
    $rut_cc_ce_pasaporte = str_replace(",", "", $rut_cc_ce_pasaporte);
    $rut_cc_ce_pasaporte = str_replace("-", "", $rut_cc_ce_pasaporte);
    $rut_cc_ce_pasaporte = str_replace(" ", "", $rut_cc_ce_pasaporte);

    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $dv
      ?  $usersModel->getList("identificacion = '$rut_cc_ce_pasaporte' AND dv = '$dv'", "")[0]
      : $usersModel->getList("identificacion = '$rut_cc_ce_pasaporte'", "")[0];

    if (!$user) {
      $response = [
        'status' => 'user_non_exist',
        'message' => 'No existe un usuario con este NIT y DV, por favor registrese'
      ];
      die(json_encode($response));
    }

    $solicitud = new Administracion_Model_DbTable_Solicitudescorreo();
    $dataSolicitud = array();
    $dataSolicitud['razon_social'] = $razon_social;
    $dataSolicitud['rut_cc_ce_pasaporte'] = $rut_cc_ce_pasaporte;
    $dataSolicitud['dv'] = $dv;
    $dataSolicitud['email'] = $email;
    $dataSolicitud['nombre_contacto'] = $nombre_contacto;
    $dataSolicitud['cargo'] = $cargo;
    $dataSolicitud['estado'] = 0;
    $dataSolicitud['created_at'] = date('Y-m-d H:i:s');

    $validacion = 1;
    if ($razon_social == "" or $razon_social == 1) {
      $validacion = 0;
    }
    if ($rut_cc_ce_pasaporte == "" or $rut_cc_ce_pasaporte == 1) {
      $validacion = 0;
    }
    /*if ($dv == "" or $dv == 1) {
      $validacion = 0;
    }*/
    if ($email == "" or $email == 1 or substr_count($email, '@') != 1) {
      $validacion = 0;
    }
    if ($nombre_contacto == "" or $nombre_contacto == 1) {
      $validacion = 0;
    }
    if ($cargo == "" or $cargo == 1) {
      $validacion = 0;
    }

    if ($user_id != "") {
      $validacion = 0;
    }


    if ($validacion == 0) {
      $response = [
        'status' => 'error',
        'message' => 'Ocurrió un error, por favor intente de nuevo'
      ];
      die(json_encode($response));
    }


    if ($validacion == 1) {

      $id = $solicitud->insert($dataSolicitud);

      $infoSolicitud = $solicitud->getById($id);

      $userModel = new Administracion_Model_DbTable_Usuario();
      $users = $userModel->getList("", "");
      $mailModel = new Core_Model_Sendingemail($this->_view);
      $mailModel->enviarSolicitudCorreo($infoSolicitud, $users);
    }



    $response = [
      'status' => 'success',
      'message' => 'Solicitud enviada correctamente'
    ];
    die(json_encode($response));
  }
  public function getIntentos($identificacion)
  {
    $bloqueosModel = new Administracion_Model_DbTable_Bloqueos();

    // Obtiene el último registro de bloqueo del usuario
    $infoBloqueo = $bloqueosModel->getList("bloqueo_usuario = '$identificacion'", "bloqueo_id DESC")[0];

    // Incrementa el contador de intentos fallidos
    $intento = $infoBloqueo->bloqueo_intentosfallidos ?? 0;
    $intento = $intento + 1;

    // Devuelve el número de intentos
    return $intento;
  }
}
