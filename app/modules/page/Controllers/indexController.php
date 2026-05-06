<?php

/**
 *
 */

class Page_indexController extends Page_mainController
{

  public function indexAction()
  {
  }
  public function registroAction()
  {
  }
  public function olvidoAction()
  {
  }
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

    $modalModel = new Page_Model_DbTable_Publicidad();

    $this->_view->popup = $modalModel->getList("publicidad_seccion=101 AND publicidad_estado=1", "")[0];
  }
  public function cambiarClaveAction()
  {
    $omegaToken = md5(Session::getInstance()->get("user")->identificacion . "_OMEGA");

    if (
      !Session::getInstance()->get('user') ||
      Session::getInstance()->get('user_ip') !== $this->getRealIp() ||
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


      $datosProveedor = $this->consultarProveedorPorNit($identificacion);
      if ($datosProveedor['status'] === 'error') {
        $response = [
          'icon' => 'error',
          'status' => 'query_error',
          'message' => $datosProveedor['message'],
          'redirect' => '/'
        ];
        die(json_encode($response));
      } elseif ($datosProveedor['status'] === 'not_found') {
        $response = [
          'icon' => 'error',
          'status' => 'no_user',
          'message' => 'El NIT proporcionado no figura en nuestros registros. Por favor, póngase en contacto con el administrador para asistencia.',
          'redirect' => '/'
        ];
        die(json_encode($response));
      }

      $datosProveedor = $datosProveedor['data'];

      // Validar cada campo de datosProveedor uno por uno
      if (!($datosProveedor['proveedor']) || empty(trim($datosProveedor['proveedor']))) {
        $response = [
          'icon' => 'error',
          'status' => 'error',
          'message' => 'El nombre del proveedor registrado en nuestro sistema es inválido o está vacío.',
          'redirect' => '/'
        ];
        die(json_encode($response));
      }

      if (!($datosProveedor['emailProveedor']) || empty(trim($datosProveedor['emailProveedor'])) || !filter_var($datosProveedor['emailProveedor'], FILTER_VALIDATE_EMAIL)) {
        $response = [
          'icon' => 'error',
          'status' => 'error',
          'message' => 'La dirección de correo electrónico registrada en nuestro sistema es inválida o está vacía.',
          'redirect' => '/'
        ];
        die(json_encode($response));
      }

      if (!($datosProveedor['comprador']) || empty(trim($datosProveedor['comprador']))) {
        $response = [
          'icon' => 'error',
          'status' => 'error',
          'message' => 'El nombre del comprador registrado en nuestro sistema es inválido o está vacío.',
          'redirect' => '/'
        ];
        die(json_encode($response));
      }


      $emailExist = $usersModel->getList("email = '{$datosProveedor['emailProveedor']}'", "")[0];
      if ($emailExist) {
        $response = [
          'icon' => 'error',
          'title' => 'Error',
          'status' => 'error_email_exist',
          'message' => 'El correo electrónico ya está registrado en el sistema. Por favor, utilice otro correo o recupere su contraseña si ya tiene una cuenta.',
          'redirect' => '/page/index/solicitud'
        ];
        die(json_encode($response));
      }


      $solicitud = new Administracion_Model_DbTable_SolicitudesRegistro();
      $count = count($solicitud->getList("", ""));
      $dataSolicitud = array();
      $dataSolicitud['razon_social'] = $datosProveedor['proveedor'];
      $dataSolicitud['rut_cc_ce_pasaporte'] = $identificacion;
      $dataSolicitud['dv'] = '';
      $dataSolicitud['email'] = APPLICATION_ENV != "production" ? "desarrollo8{$count}@omagewebsystems.com" : $datosProveedor['emailProveedor'];
      $dataSolicitud['nombre_contacto'] = $datosProveedor['comprador'];
      $dataSolicitud['cargo'] = "";
      $dataSolicitud['estado'] = 0;
      $dataSolicitud['created_at'] = date('Y-m-d H:i:s');
      $id = $solicitud->insert($dataSolicitud);
      $solicitud->editField($id, 'estado', '1');
      $userData = array();
      $userData['razon_social'] = $datosProveedor['proveedor'];
      $userData['identificacion'] = $identificacion;
      $userData['dv'] = '';
      $userData['email'] = APPLICATION_ENV != "production" ? "desarrollo8{$count}@omagewebsystems.com" : $datosProveedor['emailProveedor'];
      $userData['contacto_nombre'] = $datosProveedor['comprador'];
      $userData['contacto_cargo'] = '';


      $usersModel = new Administracion_Model_DbTable_Usuarios();
      $idRegistro = $usersModel->insert($userData);
      $user = $usersModel->getById($idRegistro);
      $key = $this->generarClave();
      $user->key = $key;
      $usersModel->editField($user->id, 'password', password_hash($key, PASSWORD_DEFAULT));
      $usersModel->editField($user->id, 'temporal_password', '1');

      $mailModel = new Core_Model_Sendingemail($this->_view);
      $email = $user->email;
      $email = explode('@', $email);
      $email[0] = substr($email[0], 0, 5) . '***';
      $email = implode('@', $email);
      $userModel = new Administracion_Model_DbTable_Usuario();
      $users = $userModel->getList("", "");
      $res = $mailModel->enviarRegistroUsuarios($user, $users);
      if ($res == 1) {
        $response = [
          'icon' => 'success',
          'title' => 'Listo',
          'status' => 'success',
          'message' => "Registro exitoso. Se enviará un correo con una contraseña temporal para que pueda ingresar al sistema a la dirección: $email por favor verifique su bandeja de entrada.",
          'email' => $email,
          'id' => $user->id,
          'redirect' => '/'
        ];
        die(json_encode($response));
      } else {
        $response = [
          'icon' => 'error',
          'title' => 'Error',
          'status' => 'error',
          'message' => 'No se ha podido enviar el correo, por favor intente de nuevo o contacte al administrador.'
        ];
        die(json_encode($response));
      }
    }


    $key = $this->generarClave();
    $user->key = $key;
    $usersModel->editField($user->id, 'password', password_hash($key, PASSWORD_DEFAULT));
    $usersModel->editField($user->id, 'temporal_password', '1');

    $mailModel = new Core_Model_Sendingemail($this->_view);
    $email = $user->email;
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
      $response = [
        'icon' => 'success',
        'title' => 'Listo',
        'status' => 'success_exist',
        'message' => "El usuario ya se encuentra registrado, se enviará un correo con una contraseña temporal para que pueda ingresar al sistema a la dirección: $email por favor verifique su bandeja de entrada.",
        'email' => $email,
        'id' => $user->id,
        'redirect' => '/'
      ];
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
    $intentos = (int) $infoBloqueo->bloqueo_intentosfallidos;
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
    $this->setLayout('blanco');
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
    $patterns = [
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
    $patterns = [
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
    $nit = Session::getInstance()->get('user')->identificacion;
    $nit = str_replace(['.', ',', ' '], '', $nit);

    // 3) Ruta base de Proveedores

    if (APPLICATION_ENV === 'development') {
      $basePath = rtrim(str_replace('\\', '/', ROOT), '/') . '/../CertificadosPagos/' . $year;
    } else {
      $basePath = rtrim('E:/CertificadosPagos/' . $year);
    }
    // Validar que el directorio existe
    if (!is_dir($basePath)) {
      $html = "<div class='col-12'>No se encontraron archivos para el año {$year}.</div>";
      die(json_encode(['html' => $html]));
    }

    // Resolver la ruta base a su forma canónica
    $basePathResolved = realpath($basePath);
    if (!$basePathResolved) {
      $html = "<div class='col-12'>Error al resolver la ruta base.</div>";
      die(json_encode(['html' => $html]));
    }

    // Normalizar NIT (ya se hizo arriba, pero aseguramos)
    $nit = str_replace(['.', ',', ' '], '', $nit);

    // Recorrer recursivamente la carpeta del año y buscar PDFs que contengan el NIT en el nombre
    $filesFound = [];
    $iter = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($basePathResolved, \FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iter as $fileinfo) {
      if (!$fileinfo->isFile())
        continue;
      $ext = strtolower($fileinfo->getExtension());
      if ($ext !== 'pdf')
        continue;
      $filename = $fileinfo->getFilename();
      if ($nit !== '') {
        // Buscar el NIT rodeado de caracteres no numéricos o al inicio/fin del nombre
        if (!preg_match('/(?<!\d)' . preg_quote($nit, '/') . '(?!\d)/', $filename)) {
          continue;
        }
      }

      // Resolver la ruta completa del archivo
      $fullPathResolved = realpath($fileinfo->getPathname());
      if (!$fullPathResolved || strpos($fullPathResolved, $basePathResolved) !== 0)
        continue;

      // Calcular la ruta relativa normalizada
      $relativePath = substr($fullPathResolved, strlen($basePathResolved) + 1);
      $relativePath = str_replace('\\', '/', $relativePath); // Normalizar a barras forward

      // Guardar ruta relativa desde basePath
      $filesFound[] = [
        'fullPath' => $fullPathResolved,
        'relativePath' => $relativePath,
        'filename' => $filename
      ];
    }

    // Montar HTML
    $html = '';
    if (count($filesFound) > 0) {
      foreach ($filesFound as $file) {
        // Usar el nuevo endpoint para descargar
        $downloadUrl = '/page/index/descargarProveedor?year=' . urlencode($year) . '&file=' . urlencode($file['relativePath']);
        $html .= "<div class='col-12 mb-3'>
                  <a href='{$downloadUrl}' target='_blank'>
                    {$file['filename']} 
                    <i class='fa-solid fa-download'></i>
                  </a>
                </div>";
      }
    } else {
      $html = "<div class='col-12'>No se encontraron archivos para el NIT {$nit} en el año {$year}.</div>";
    }
    die(json_encode(['html' => $html]));
  }

  // Nuevo método para servir los archivos
  public function descargarProveedorAction()
  {
    $this->setLayout('blanco');

    // Verificar que el usuario esté logueado
    if (!Session::getInstance()->get('user')) {
      header('HTTP/1.0 403 Forbidden');
      die('Acceso denegado');
    }

    $year = $this->_getSanitizedParam('year');
    $file = $this->_getSanitizedParam('file');

    // Validación básica
    if (!$year || !$file) {
      header('HTTP/1.0 400 Bad Request');
      die('Parámetros inválidos');
    }

    // Construir ruta completa
    if (APPLICATION_ENV === 'development') {
      $basePath = rtrim(str_replace('\\', '/', ROOT), '/') . '/../CertificadosPagos/' . $year;
    } else {
      $basePath = rtrim('E:/CertificadosPagos/' . $year);
    }
    $basePathResolved = realpath($basePath);
    if (!$basePathResolved) {
      header('HTTP/1.0 404 Not Found');
      die('Ruta base no encontrada');
    }

    $filePath = $basePathResolved . DIRECTORY_SEPARATOR . $file;
    $realFilePath = realpath($filePath);



    // Verificar que el archivo está dentro del directorio permitido
    if ($realFilePath === false || strpos($realFilePath, $basePathResolved) !== 0) {
      header('HTTP/1.0 403 Forbidden');
      die('Acceso denegado');
    }

    // Verificar que el archivo existe
    if (!file_exists($realFilePath) || !is_file($realFilePath)) {
      header('HTTP/1.0 404 Not Found');
      die('Archivo no encontrado');
    }

    // Verificar que el NIT del usuario está en el nombre del archivo (seguridad adicional)
    $nit = Session::getInstance()->get('user')->identificacion;
    $nit = str_replace(['.', ',', ' '], '', $nit);

    if (stripos(basename($realFilePath), $nit) === false) {
      header('HTTP/1.0 403 Forbidden');
      die('No tiene permisos para acceder a este archivo');
    }

    // Servir el archivo
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($realFilePath) . '"');
    header('Content-Length: ' . filesize($realFilePath));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    readfile($realFilePath);
    exit;
  }
  public function buscarProveedoresOLDAction()
  {
    // 1) CSRF
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode([
        'html' => '<span> Token de seguridad inválido, por favor recargue la página. </span>'
      ]));
    }

    // 2) Parámetros
    $year = $this->_getSanitizedParam('year');
    $nit = Session::getInstance()->get('user')->identificacion;
    $nit = str_replace(['.', ',', ' '], '', $nit);

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
        $url = "/files/Pagos/{$filename}";
        $html .= "<div class='col-12 mb-3'>
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
    error_reporting(E_ALL);

    $this->setLayout('blanco');
    $response = array();

    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Llamar a la función para verificar los intentos
    $attemptsCheck = $this->valiteAttempts($ip_address);

    // Verificar el resultado de la función
    // if (!$attemptsCheck) {
    //   $res['status'] = 'error';
    //   $res['message'] = 'Demasiados intentos, por favor intente de nuevo en 15 minutos';
    //   die(json_encode($res));
    // }

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
		if (!$user-> email) {
		$response = [
			'status' => 'error',
			'message' => 'No hay un correo asociado a este NIT, contacte con un administrador para obtener ayuda'
		];
		die(json_encode($response));
    }

      $mailModel = new Core_Model_Sendingemail($this->_view);
      $mail = $mailModel->enviarRecuperacion($user, $token);
      if ($mail == '1') {
        $usersModel->editField($user->id, 'token', $token);
        $usersModel->editField($user->id, 'token_date', $token_date);
        $response = [
          'status' => 'success',
          'email' => $this->enmascararCorreo($user->email)
        ];
      } else {
        $response = [
          'status' => 'error',
          'message' => 'Error al enviar el correo de recuperación',
          'mail_error' => $mail
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
  public function solicitudAction()
  {
    header('location: /page/index/');
  }
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
        'message' => 'Ya existe un usuario con este NIT, inicia sesión para continuar o recupera tu contraseña'
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

    //validar empresa con el correo y nit
    // $isValidCompany = $this->validarEmpresa($dataSolicitud);
    // if (!$isValidCompany) {
    //   $response = [
    //     'status' => 'error',
    //     'message' => 'Los datos proporcionados (razón social, NIT o email) no coinciden con nuestros registros de proveedores.'
    //   ];
    //   die(json_encode($response));
    // }
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
      $userData['contacto_cargo'] = $cargo;


      $usersModel = new Administracion_Model_DbTable_Usuarios();
      $idRegistro = $usersModel->insert($userData);

      $user = $usersModel->getById($idRegistro);

      $key = $this->generarClave();
      $user->key = $key;
      $usersModel->editField($user->id, 'password', password_hash($key, PASSWORD_DEFAULT));
      $usersModel->editField($user->id, 'temporal_password', '1');

      $mailModel = new Core_Model_Sendingemail($this->_view);
      $email = $user->email;
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
  public function correoAction()
  {
  }

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
      ? $usersModel->getList("identificacion = '$rut_cc_ce_pasaporte' AND dv = '$dv'", "")[0]
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


  /**
   * Consulta un proveedor por NIT - OPTIMIZADO con caché
   */
  public function consultarProveedorPorNit($nit)
  {
    $nit = trim($nit);

    $proveedorModel = new Administracion_Model_DbTable_Proveedores();
    $proveedor = $proveedorModel->getByNit($nit);

    if ($proveedor) {
      return [
        'status' => 'found',
        'data' => [
          'comprador' => $proveedor->proveedor_comprador,
          'numeroOr' => $proveedor->proveedor_numeroOr,
          'numeroLinea' => $proveedor->proveedor_numeroLinea,
          'codigoProducto' => $proveedor->proveedor_codigoProducto,
          'fechaAprobacionOR' => $proveedor->proveedor_fechaAprobacionOR,
          'fechaAprobacionSolicitud' => $proveedor->proveedor_fechaAprobacionSolicitud,
          'proveedor' => $proveedor->proveedor_proveedor,
          'nit' => $proveedor->proveedor_nit,
          'emailProveedor' => $proveedor->proveedor_emailProveedor,
          'creacionProveedor' => $proveedor->proveedor_creacionProveedor,
          'solicitudOferta' => $proveedor->proveedor_solicitudOferta,
          'visitaTecnica' => $proveedor->proveedor_visitaTecnica,
          'recepcionPreguntas' => $proveedor->proveedor_recepcionPreguntas,
          'envioRespuestas' => $proveedor->proveedor_envioRespuestas,
          'recepcionOferta' => $proveedor->proveedor_recepcionOferta,
          'evaluacionTecnica' => $proveedor->proveedor_evaluacionTecnica,
          'reevaluacion' => $proveedor->proveedor_reevaluacion,
          'adjudicacion' => $proveedor->proveedor_adjudicacion,
          'fechaContrato' => $proveedor->proveedor_fechaContrato,
        ]
      ];
    }

    return ['status' => 'not_found'];
  }

  /**
   * Trae todos los proveedores del servicio SOAP
   * NOTA: Esta función se ejecuta solo cuando el caché está expirado
   */
  public function traerTodosProveedores()
  {
    $curl = curl_init();
    $hoy = date('d-m-Y');

    // Consultar solo el último año para reducir datos (ajustar según necesidad)
    $fechaInicio = '01-01-2023';

    $soapRequest = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope 
    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
    xmlns:get="' . URL_GETINFO . '">
    <soapenv:Header/>
    <soapenv:Body>
        <get:getInfoOpainBi>
            <fechaInicio>' . $fechaInicio . '</fechaInicio>
            <fechaFinal>' . $hoy . '</fechaFinal>
        </get:getInfoOpainBi>
    </soapenv:Body>
</soapenv:Envelope>';

    curl_setopt_array($curl, array(
      CURLOPT_URL => URL_GETINFO,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $soapRequest,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: "getInfoOpainBi"',
        'Content-Length: ' . strlen($soapRequest),
        'Cookie: AWSALBAPP-0=AAAAAAAAAAAkd5ULrudLd73VrVj2sf+zRIUga4hS1PdF+e8m06sv0Z2e8kFdN/B+YvKKpDCLa7wfkySzIbVRATVVhJBtCy/M9TwRVJFexqYEf39TC9qjDDItTbYrwGWtNjBwOhegcuBV7W4=; PHPSESSID=b8scsmpt3hgthnplvcmopisn2g',
        'Accept: text/xml, application/xml, application/soap+xml'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      // echo 'Error:' . curl_error($curl);
      error_log('Error en getInfo: ' . curl_error($curl));
      curl_close($curl);
      return false;
    }

    curl_close($curl);

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($response);

    if ($xml === false) {
      // echo "No es XML válido. Errores:\n";
      foreach (libxml_get_errors() as $error) {
        // echo $error->message . "\n";
      }
      libxml_clear_errors();
      return false;
    }

    return $this->parseXmlResponse($xml);
  }
  private function parseXmlResponse($xml)
  {
    // Registrar el namespace SOAP-ENV
    $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');

    // Usar XPath para navegar más fácilmente
    $items = $xml->xpath('//item');

    $resultado = [];

    foreach ($items as $item) {
      // Verificar si tiene items anidados
      if (isset($item->ProductLine->item)) {
        // Items anidados
        foreach ($item->ProductLine->item as $subItem) {
          $resultado[] = [
            'comprador' => trim((string) $subItem->comprador),
            'numeroOr' => trim((string) $subItem->numeroOr),
            'numeroLinea' => trim((string) $subItem->numeroLinea),
            'codigoProducto' => trim((string) $subItem->codigoProducto),
            'fechaAprobacionOR' => trim((string) $subItem->fechaAprobacionOR),
            'fechaAprobacionSolicitud' => trim((string) $subItem->fechaAprobacionSolicitud),
            'proveedor' => trim((string) $subItem->Proveedor),
            'nit' => trim((string) $subItem->Nit),
            'emailProveedor' => trim((string) $subItem->emailProveedor),
            'creacionProveedor' => trim((string) $subItem->creacionProveedor),
            'solicitudOferta' => trim((string) $subItem->solicitudOferta),
            'visitaTecnica' => trim((string) $subItem->visitaTecnica),
            'recepcionPreguntas' => trim((string) $subItem->recepcionPreguntas),
            'envioRespuestas' => trim((string) $subItem->envioRespuestas),
            'recepcionOferta' => trim((string) $subItem->recepcionOferta),
            'evaluacionTecnica' => trim((string) $subItem->evaluacionTecnica),
            'reevaluacion' => trim((string) $subItem->reevaluacion),
            'adjudicacion' => trim((string) $subItem->adjudicacion),
            'fechaContrato' => trim((string) $subItem->fechaContrato),
          ];
        }
      } elseif (isset($item->ProductLine)) {
        // Item directo
        $productLine = $item->ProductLine;
        $resultado[] = [
          'comprador' => trim((string) $productLine->comprador),
          'numeroOr' => trim((string) $productLine->numeroOr),
          'numeroLinea' => trim((string) $productLine->numeroLinea),
          'codigoProducto' => trim((string) $productLine->codigoProducto),
          'fechaAprobacionOR' => trim((string) $productLine->fechaAprobacionOR),
          'fechaAprobacionSolicitud' => trim((string) $productLine->fechaAprobacionSolicitud),
          'proveedor' => trim((string) $productLine->Proveedor),
          'nit' => trim((string) $productLine->Nit),
          'emailProveedor' => trim((string) $productLine->emailProveedor),
          'creacionProveedor' => trim((string) $productLine->creacionProveedor),
          'solicitudOferta' => trim((string) $productLine->solicitudOferta),
          'visitaTecnica' => trim((string) $productLine->visitaTecnica),
          'recepcionPreguntas' => trim((string) $productLine->recepcionPreguntas),
          'envioRespuestas' => trim((string) $productLine->envioRespuestas),
          'recepcionOferta' => trim((string) $productLine->recepcionOferta),
          'evaluacionTecnica' => trim((string) $productLine->evaluacionTecnica),
          'reevaluacion' => trim((string) $productLine->reevaluacion),
          'adjudicacion' => trim((string) $productLine->adjudicacion),
          'fechaContrato' => trim((string) $productLine->fechaContrato),
        ];
      }
    }


    return $resultado;
  }

  public function sincronizarProveedoresActionOLD()
  {
    error_reporting(E_ALL);
    $this->setLayout('blanco');

    // Token de seguridad
    $tokenValido = CACHE_REFRESH_TOKEN;
    $token = $this->_getSanitizedParam('token');

    if ($token !== $tokenValido) {
      die(json_encode(['status' => 'error', 'message' => 'No autorizado']));
    }

    // set_time_limit(300); // 5 minutos
    // ini_set('memory_limit', '-1'); //infinito

    $startTime = microtime(true);

    // Obtener proveedores del servicio SOAP
    $proveedores = $this->traerTodosProveedores();

    if ($proveedores === false) {
      die(json_encode([
        'status' => 'error',
        'message' => 'Error al consultar el servicio SOAP 1'
      ]));
    }

    $proveedorModel = new Administracion_Model_DbTable_Proveedores();
    $insertados = 0;
    $actualizados = 0;
    $errores = 0;

    foreach ($proveedores as $prov) {
      try {
        // Limpiar datos
        $data = [
          'proveedor_comprador' => trim(preg_replace('/\s+/', ' ', str_replace(['[DESACTIVADO]', '*'], '', $prov['comprador'] ?? ''))),
          'proveedor_numeroOr' => trim($prov['numeroOr'] ?? ''),
          'proveedor_numeroLinea' => trim($prov['numeroLinea'] ?? ''),
          'proveedor_codigoProducto' => trim($prov['codigoProducto'] ?? ''),
          'proveedor_fechaAprobacionOR' => trim($prov['fechaAprobacionOR'] ?? ''),
          'proveedor_fechaAprobacionSolicitud' => trim($prov['fechaAprobacionSolicitud'] ?? ''),
          'proveedor_proveedor' => trim(preg_replace('/\s+/', ' ', str_replace(['[DESACTIVADO]', '*'], '', $prov['proveedor'] ?? ''))),
          'proveedor_nit' => trim($prov['nit'] ?? ''),
          'proveedor_emailProveedor' => trim(str_replace('[DESACTIVADO]', '', $prov['emailProveedor'] ?? '')),
          'proveedor_creacionProveedor' => trim($prov['creacionProveedor'] ?? ''),
          'proveedor_solicitudOferta' => trim($prov['solicitudOferta'] ?? ''),
          'proveedor_visitaTecnica' => trim($prov['visitaTecnica'] ?? ''),
          'proveedor_recepcionPreguntas' => trim($prov['recepcionPreguntas'] ?? ''),
          'proveedor_envioRespuestas' => trim($prov['envioRespuestas'] ?? ''),
          'proveedor_recepcionOferta' => trim($prov['recepcionOferta'] ?? ''),
          'proveedor_evaluacionTecnica' => trim($prov['evaluacionTecnica'] ?? ''),
          'proveedor_reevaluacion' => trim($prov['reevaluacion'] ?? ''),
          'proveedor_adjudicacion' => trim($prov['adjudicacion'] ?? ''),
          'proveedor_fechaContrato' => trim($prov['fechaContrato'] ?? ''),
        ];

        // Saltar si no tiene NIT
        if (
          empty($data['proveedor_nit']) ||
          empty($data['proveedor_emailProveedor']) ||
          empty($data['proveedor_comprador']) ||
          empty($data['proveedor_proveedor'])
        ) {
          continue;
        }

        $proveedorModel->insert($data);
        $insertados++;

      } catch (Exception $e) {
        $errores++;
        error_log("Error sincronizando proveedor: " . $e->getMessage());
      }
    }

    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);

    die(json_encode([
      'status' => 'success',
      'message' => 'Sincronización completada',
      'total_procesados' => count($proveedores),
      'insertados_actualizados' => $insertados,
      'errores' => $errores,
      'tiempo_segundos' => $duration,
      'proxima_sincronizacion' => date('Y-m-d H:i:s', strtotime('+1 day'))
    ]));
  }


  public function pruebaenvioAction()
  {
    $this->setLayout('blanco');
    $emailModel = new Core_Model_Mail();
    $asunto = "PRUEBA DE ENVIO";
    $tabla = "<table>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Edad</th>
          <th>Relación</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Juan Pérez</td>
          <td>30</td>
          <td>Amigo</td>
        </tr>
        <tr>
          <td>María López</td>
          <td>25</td>
          <td>Hermana</td>
        </tr>
      </tbody>
    </table>";

    $content = $tabla;

    $bccs = [
      "desarrollo8@omegawebsystems.com",
    ];

    $emailModel->getMail()->Subject = $asunto;
    $emailModel->getMail()->msgHTML($content);
    $emailModel->getMail()->AltBody = $content;
    $emailModel->getMail()->SMTPDebug = 1;

    foreach ($bccs as $bcc) {
      $emailModel->getMail()->addBCC($bcc);
    }
    //$emailModel->getMail()->addAddress($email);

    // Intentar enviar
    $enviado = $emailModel->sed();
    if ($enviado) {
      echo "Correo enviado correctamente.";
    } else {
      echo $emailModel->getMail()->ErrorInfo;
    }

  }

  public function sincronizarProveedoresAction()
  {
    error_reporting(E_ALL);
    $this->setLayout('blanco');

    // Token de seguridad
    $tokenValido = CACHE_REFRESH_TOKEN;
    $token = $this->_getSanitizedParam('token');

    if ($token !== $tokenValido) {
      die(json_encode(['status' => 'error', 'message' => 'No autorizado']));
    }

    // set_time_limit(300);
    // ini_set('memory_limit', '512M');

    $startTime = microtime(true);

    // SIMPLIFICADO: Solo traer último día por defecto
    $modo = $this->_getSanitizedParam('modo') ?? 'diario';

    if ($modo === '48h') {
      $fechaInicio = date('d-m-Y', strtotime('-2 days'));
      $diasConsultados = 2;
    } else {
      // Por defecto: solo ayer y hoy (más seguro que solo hoy)
      $fechaInicio = date('d-m-Y', strtotime('-1 day'));
      $diasConsultados = 1;
    }

    $fechaFinal = date('d-m-Y');

    // Obtener proveedores del rango de fechas
    $proveedores = $this->traerProveedoresPorFechas($fechaInicio, $fechaFinal);

    if ($proveedores === false) {
      die(json_encode([
        'status' => 'error',
        'message' => 'Error al consultar el servicio SOAP',
        'rango_consultado' => "{$fechaInicio} a {$fechaFinal}"
      ]));
    }

    $proveedorModel = new Administracion_Model_DbTable_Proveedores();
    $insertados = 0;
    $actualizados = 0;
    $omitidos = 0;
    $errores = [];

    foreach ($proveedores as $prov) {
      try {
        // Limpiar datos
        $data = [
          'proveedor_comprador' => $this->limpiarTexto($prov['comprador'] ?? ''),
          'proveedor_numeroOr' => trim($prov['numeroOr'] ?? ''),
          'proveedor_numeroLinea' => trim($prov['numeroLinea'] ?? ''),
          'proveedor_codigoProducto' => trim($prov['codigoProducto'] ?? ''),
          'proveedor_fechaAprobacionOR' => trim($prov['fechaAprobacionOR'] ?? ''),
          'proveedor_fechaAprobacionSolicitud' => trim($prov['fechaAprobacionSolicitud'] ?? ''),
          'proveedor_proveedor' => $this->limpiarTexto($prov['proveedor'] ?? ''),
          'proveedor_nit' => trim($prov['nit'] ?? ''),
          'proveedor_emailProveedor' => $this->limpiarTexto($prov['emailProveedor'] ?? ''),
          'proveedor_creacionProveedor' => trim($prov['creacionProveedor'] ?? ''),
          'proveedor_solicitudOferta' => trim($prov['solicitudOferta'] ?? ''),
          'proveedor_visitaTecnica' => trim($prov['visitaTecnica'] ?? ''),
          'proveedor_recepcionPreguntas' => trim($prov['recepcionPreguntas'] ?? ''),
          'proveedor_envioRespuestas' => trim($prov['envioRespuestas'] ?? ''),
          'proveedor_recepcionOferta' => trim($prov['recepcionOferta'] ?? ''),
          'proveedor_evaluacionTecnica' => trim($prov['evaluacionTecnica'] ?? ''),
          'proveedor_reevaluacion' => trim($prov['reevaluacion'] ?? ''),
          'proveedor_adjudicacion' => trim($prov['adjudicacion'] ?? ''),
          'proveedor_fechaContrato' => trim($prov['fechaContrato'] ?? ''),
        ];

        // Validar campos obligatorios
        if (!$this->validarProveedor($data)) {
          $omitidos++;
          continue;
        }

        // Insertar o actualizar
        $proveedorModel->insert($data);
        $insertados++;

      } catch (Exception $e) {
        $errores[] = [
          'nit' => $data['proveedor_nit'] ?? 'N/A',
          'error' => $e->getMessage()
        ];
        error_log("Error sync NIT {$data['proveedor_nit']}: " . $e->getMessage());
      }
    }

    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);

    // Registrar log
    $this->registrarLogSincronizacion([
      'fecha_inicio' => $fechaInicio,
      'fecha_final' => $fechaFinal,
      'total_procesados' => count($proveedores),
      'insertados' => $insertados,
      'omitidos' => $omitidos,
      'errores_count' => count($errores),
      'errores_detalle' => $errores,
      'duracion' => $duration
    ]);

    die(json_encode([
      'status' => 'success',
      'message' => 'Sincronización completada',
      'modo' => $modo,
      'dias_consultados' => $diasConsultados,
      'rango_fechas' => "{$fechaInicio} a {$fechaFinal}",
      'total_procesados' => count($proveedores),
      'insertados_actualizados' => $insertados,
      'omitidos' => $omitidos,
      'errores' => count($errores),
      'tiempo_segundos' => $duration
    ]));
  }

  // Método helper para limpiar texto
  private function limpiarTexto($texto)
  {
    return trim(preg_replace('/\s+/', ' ', str_replace(['[DESACTIVADO]', '*'], '', $texto)));
  }

  // Método helper para validar proveedor
  private function validarProveedor($data)
  {
    return !empty($data['proveedor_nit']) &&
      !empty($data['proveedor_emailProveedor']) &&
      !empty($data['proveedor_comprador']) &&
      !empty($data['proveedor_proveedor']);
  }

  /**
   * Trae proveedores filtrados por rango de fechas
   */
  private function traerProveedoresPorFechas($fechaInicio, $fechaFinal)
  {
    $curl = curl_init();

    $soapRequest = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope 
    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
    xmlns:get="' . URL_GETINFO . '">
    <soapenv:Header/>
    <soapenv:Body>
        <get:getInfoOpainBi>
            <fechaInicio>' . $fechaInicio . '</fechaInicio>
            <fechaFinal>' . $fechaFinal . '</fechaFinal>
        </get:getInfoOpainBi>
    </soapenv:Body>
</soapenv:Envelope>';

    curl_setopt_array($curl, array(
      CURLOPT_URL => URL_GETINFO,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 120,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $soapRequest,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: "getInfoOpainBi"',
        'Content-Length: ' . strlen($soapRequest),
        'Accept: text/xml, application/xml, application/soap+xml'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      error_log('Error SOAP (fechas): ' . curl_error($curl));
      curl_close($curl);
      return false;
    }

    curl_close($curl);

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($response);

    if ($xml === false) {
      foreach (libxml_get_errors() as $error) {
        error_log('XML Error: ' . $error->message);
      }
      libxml_clear_errors();
      return false;
    }

    return $this->parseXmlResponse($xml);
  }

  /**
   * Registra un log de cada sincronización
   */
  private function registrarLogSincronizacion($datos)
  {
    $logData = [
      'log_tipo' => 'SYNC_PROVEEDORES',
      'log_usuario' => 'SYSTEM_CRON',
      'log_log' => json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ];

    $logModel = new Administracion_Model_DbTable_Log();
    $logModel->insert($logData);
  }
}
