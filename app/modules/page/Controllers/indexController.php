<?php

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

  // Página principal de certificados, requiere sesión válida
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

    Session::getInstance()->set('csrf_token_user', md5(uniqid(rand(), true)));
    $this->_view->csrf_token = Session::getInstance()->get('csrf_token_user');

    $modalModel = new Page_Model_DbTable_Publicidad();
    $this->_view->popup = $modalModel->getList("publicidad_seccion=101 AND publicidad_estado=1", "")[0];
  }

  // Vista para cambio de contraseña temporal obligatorio
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
  }

  // Registra un nuevo usuario o regenera clave si ya existe
  public function registrarAction()
  {
    $identificacion = $this->_getSanitizedParam('identificacion');
    $identificacion = str_replace(['.', ',', '-', ' '], '', $identificacion);

    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $usersModel->getList("identificacion = '$identificacion'", "")[0];

    if (!$user) {
      $apiProveedorData = $this->consultarProveedor($identificacion);

      if (
        !$apiProveedorData ||
        empty($apiProveedorData->providerName) ||
        empty($apiProveedorData->providerEmail) ||
        empty($apiProveedorData->nit)
      ) {
        $response = [
          'icon' => 'error',
          'title' => 'Error',
          'status' => 'error',
          'message' => 'No se encontró información del proveedor en el sistema. Verifique el NIT ingresado.'
        ];
        die(json_encode($response));
      }

      $solicitud = new Administracion_Model_DbTable_SolicitudesRegistro();

      $dataSolicitud = array();
      $dataSolicitud['razon_social'] = $apiProveedorData->providerName;
      $dataSolicitud['rut_cc_ce_pasaporte'] = $apiProveedorData->nit;
      $dataSolicitud['dv'] = '';
      $dataSolicitud['email'] = APPLICATION_ENV == "development" ? "desarrollo8+{$apiProveedorData->nit}@omagewebsystems.com" : $apiProveedorData->providerEmail;
      $dataSolicitud['nombre_contacto'] = '';
      $dataSolicitud['cargo'] = "";
      $dataSolicitud['estado'] = 0;
      $dataSolicitud['created_at'] = date('Y-m-d H:i:s');
      $id = $solicitud->insert($dataSolicitud);
      $solicitud->editField($id, 'estado', '1');

      $userData = array();
      $userData['razon_social'] = $apiProveedorData->providerName;
      $userData['identificacion'] = $apiProveedorData->nit;
      $userData['dv'] = '';
      $userData['email'] = APPLICATION_ENV == "development" ? "desarrollo8+{$apiProveedorData->nit}@omagewebsystems.com" : $apiProveedorData->providerEmail;
      $userData['contacto_nombre'] = '';
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

  // Consulta el proveedor en la API interna por NIT. Retorna el array 'data' o null si no existe/falla.
  private function consultarProveedor($nit)
  {
    $apiKey = Config_Config::getInstance()->getValue('keys/keyProviders');
    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => "http://10.30.240.8/api/Providers/$nit",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => ["Accept: application/json", "X-API-Key: $apiKey"],
      CURLOPT_TIMEOUT => 10
    ]);
    $respuesta = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$respuesta || $httpCode !== 200) {
      return null;
    }

    $json = json_decode($respuesta);
    if (!empty($json->success) && isset($json->data)) {
      return $json->data;
    }

    return null;
  }

  // Genera una clave aleatoria de 8 caracteres
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

  // Autentica al usuario con captcha, bloqueo por intentos y registro de log
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

    $identificacion = str_replace(['.', ',', '-', ' '], '', $identificacion);

    // Honeypot anti-bot
    $email = $this->_getSanitizedParam("email");
    if ($email) {
      $response = [
        'status' => 'error',
        'message' => 'Usuario no encontrado'
      ];
      die(json_encode($response));
    }

    $bloqueosModel = new Administracion_Model_DbTable_Bloqueos();
    $infoBloqueo = $bloqueosModel->getList(
      "bloqueo_usuario = '$identificacion' or bloqueo_ip = '" . $_SERVER['REMOTE_ADDR'] . "' ",
      "bloqueo_id DESC"
    )[0];

    $intentos = (int) $infoBloqueo->bloqueo_intentosfallidos;
    $fechaUltimoIntento = new DateTime($infoBloqueo->bloqueo_fechaintento);
    $fechaActual = new DateTime();
    $diferencia = $fechaActual->getTimestamp() - $fechaUltimoIntento->getTimestamp();

    if ($intentos >= 3 && $diferencia <= 900) {
      $response = [
        'status' => 'error',
        'message' => 'Usuario  bloqueado por 15 minutos'
      ];
      die(json_encode($response));
    }

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

  // Retorna la IP real del cliente considerando Cloudflare y proxies
  function getRealIp()
  {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
      return $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
      return $_SERVER['REMOTE_ADDR'];
    }
  }

  // Cierra la sesión y redirige al inicio
  public function logoutAction()
  {
    $this->setLayout('blanco');
    Session::getInstance()->set('user', null);
    header('Location: /');
    exit;
  }

  // Cambia la contraseña del usuario autenticado
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

  // Busca certificados de Reteica por NIT y año
  public function buscarICAAction()
  {
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode(['html' => '<span> Token de seguridad invalido, por favor recargue la pagina para buscar sus documentos </span>']));
    }
    $year = $this->_getSanitizedParam('year');
    $nit = Session::getInstance()->get('user')->identificacion;
    // Algunos usuarios se loguearon con puntos en el NIT y no consulta correctamente
    $nit = str_replace(['.', ',', ' '], '', $nit);

    $basePath = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Reteica_Reteiva/*" . $nit;

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
    ];

    $files = [];
    foreach ($patterns as $pattern) {
      $files = array_merge($files, glob($pattern));
    }
    $files = array_unique($files);

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

  // Busca certificados de Reteiva por NIT y año
  public function buscarIVAAction()
  {
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode(['html' => '<span> Token de seguridad invalido, por favor recargue la pagina para buscar sus documentos </span>']));
    }
    $year = $this->_getSanitizedParam('year');
    $nit = Session::getInstance()->get('user')->identificacion;
    // Algunos usuarios se loguearon con puntos en el NIT y no consulta correctamente
    $nit = str_replace(['.', ',', ' '], '', $nit);

    $basePath = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Reteica_Reteiva/*" . $nit;

    $patterns = [
      $basePath . "-RTE IVA *{$year}*.pdf",
      $basePath . "_RTEIVA *{$year}*.pdf",
      $basePath . "_RTE IVA_ *{$year}*.pdf",
      $basePath . "IVA*{$year}*.pdf",
      $basePath . "RTE*{$year}*.pdf",
      $basePath . "_RTE IVA_*{$year}*.pdf",
      $basePath . "*RTEIVA *{$year}*.pdf",
      $basePath . "*RTE IVA *{$year}*.pdf",
      $basePath . "*rte iva *{$year}*.pdf",
    ];

    $files = [];
    foreach ($patterns as $pattern) {
      $files = array_merge($files, glob($pattern));
    }
    $files = array_unique($files);

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

  // Busca certificados de Retefuente por NIT y año en dos rutas distintas
  public function buscarFuenteAction()
  {
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode(['html' => '<span> Token de seguridad invalido, por favor recargue la pagina para buscar sus documentos </span>']));
    }
    $year = $this->_getSanitizedParam('year');
    $nit = Session::getInstance()->get('user')->identificacion;
    // Algunos usuarios se loguearon con puntos en el NIT y no consulta correctamente
    $nit = str_replace(['.', ',', ' '], '', $nit);

    $basePath = str_replace('\\', '/', FILE_PATH) . "Retefuente/*" . $nit;
    $basePath2 = str_replace('\\', '/', FILE_PATH) . "Bancolombia/Certificados_renta_der_fiduciaria/*" . $nit;

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

    $files = [];
    foreach ($patterns as $pattern) {
      $files = array_merge($files, glob($pattern));
    }

    $files2 = [];
    foreach ($patterns2 as $pattern2) {
      $files2 = array_merge($files2, glob($pattern2));
    }

    $allFiles = array_unique(array_merge($files, $files2));

    $html = "";
    if ($allFiles) {
      foreach ($allFiles as $file) {
        if (strpos($file, "Retefuente") !== false) {
          $url = "/files/Retefuente/" . basename($file);
        } elseif (strpos($file, "Bancolombia/Certificados_renta_der_fiduciaria") !== false) {
          $url = "/files/Bancolombia/Certificados_renta_der_fiduciaria/" . basename($file);
        } else {
          $url = "/files/" . basename($file);
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

  // Busca certificados de pagos a proveedores recorriendo recursivamente la carpeta del año
  public function buscarProveedoresAction()
  {
    if (Session::getInstance()->get('csrf_token_user') != $this->_getSanitizedParam('csrf_token')) {
      die(json_encode([
        'html' => '<span> Token de seguridad inválido, por favor recargue la página. </span>'
      ]));
    }

    $year = $this->_getSanitizedParam('year');
    $nit = Session::getInstance()->get('user')->identificacion;
    $nit = str_replace(['.', ',', ' '], '', $nit);

    if (APPLICATION_ENV === 'development') {
      $basePath = rtrim(str_replace('\\', '/', ROOT), '/') . '/../CertificadosPagos/' . $year;
    } else {
      $basePath = rtrim('E:/CertificadosPagos/' . $year);
    }

    if (!is_dir($basePath)) {
      $html = "<div class='col-12'>No se encontraron archivos para el año {$year}.</div>";
      die(json_encode(['html' => $html]));
    }

    $basePathResolved = realpath($basePath);
    if (!$basePathResolved) {
      $html = "<div class='col-12'>Error al resolver la ruta base.</div>";
      die(json_encode(['html' => $html]));
    }

    $filesFound = [];
    $iter = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($basePathResolved, \FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iter as $fileinfo) {
      if (!$fileinfo->isFile())
        continue;
      if (strtolower($fileinfo->getExtension()) !== 'pdf')
        continue;
      $filename = $fileinfo->getFilename();
      if ($nit !== '') {
        if (!preg_match('/(?<!\d)' . preg_quote($nit, '/') . '(?!\d)/', $filename)) {
          continue;
        }
      }

      $fullPathResolved = realpath($fileinfo->getPathname());
      if (!$fullPathResolved || strpos($fullPathResolved, $basePathResolved) !== 0)
        continue;

      $relativePath = substr($fullPathResolved, strlen($basePathResolved) + 1);
      $relativePath = str_replace('\\', '/', $relativePath);

      $filesFound[] = [
        'fullPath' => $fullPathResolved,
        'relativePath' => $relativePath,
        'filename' => $filename
      ];
    }

    $html = '';
    if (count($filesFound) > 0) {
      foreach ($filesFound as $file) {
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

  // Sirve el PDF de proveedor validando que el NIT del usuario coincida con el archivo
  public function descargarProveedorAction()
  {
    $this->setLayout('blanco');

    if (!Session::getInstance()->get('user')) {
      header('HTTP/1.0 403 Forbidden');
      die('Acceso denegado');
    }

    $year = $this->_getSanitizedParam('year');
    $file = $this->_getSanitizedParam('file');

    if (!$year || !$file) {
      header('HTTP/1.0 400 Bad Request');
      die('Parámetros inválidos');
    }

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

    if ($realFilePath === false || strpos($realFilePath, $basePathResolved) !== 0) {
      header('HTTP/1.0 403 Forbidden');
      die('Acceso denegado');
    }

    if (!file_exists($realFilePath) || !is_file($realFilePath)) {
      header('HTTP/1.0 404 Not Found');
      die('Archivo no encontrado');
    }

    $nit = Session::getInstance()->get('user')->identificacion;
    $nit = str_replace(['.', ',', ' '], '', $nit);

    if (stripos(basename($realFilePath), $nit) === false) {
      header('HTTP/1.0 403 Forbidden');
      die('No tiene permisos para acceder a este archivo');
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($realFilePath) . '"');
    header('Content-Length: ' . filesize($realFilePath));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    readfile($realFilePath);
    exit;
  }

  // Envía correo de recuperación de contraseña con token temporal
  public function enviarRecuperacionAction()
  {
    $this->setLayout('blanco');
    $response = array();

    $ip_address = $_SERVER['REMOTE_ADDR'];

    $attemptsCheck = $this->valiteAttempts($ip_address);
    if (!$attemptsCheck) {
      $res['status'] = 'error';
      $res['message'] = 'Demasiados intentos, por favor intente de nuevo en 15 minutos';
      die(json_encode($res));
    }

    $identificacion = $this->_getSanitizedParam('identificacion');
    $identificacion = str_replace(['.', ',', '-', ' '], '', $identificacion);

    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $usersModel->getList("identificacion = '$identificacion'", "")[0];
    $token = md5(uniqid(rand(), true));
    $token_date = date('Y-m-d H:i:s');

    if ($user) {
      if (!$user->email) {
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

  // Enmascara el correo dejando visibles solo los primeros 3 caracteres y el dominio
  private function enmascararCorreo($correo)
  {
    $posicionArroba = strpos($correo, '@');
    $dominio = substr($correo, $posicionArroba);
    $parteEnmascarada = substr($correo, 0, 3) . str_repeat('*', $posicionArroba - 3);
    return $parteEnmascarada . $dominio;
  }

  // Vista de recuperación de contraseña, valida que el token no haya expirado (1 hora)
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

  // Actualiza la contraseña usando el flujo de recuperación por token
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

  // Controla el rate limiting de recuperación de contraseña por IP (5 intentos, bloqueo 15 min)
  private function valiteAttempts($ip_address)
  {
    $currentTime = time();

    if (!isset($_SESSION['password_reset_attempts'])) {
      $_SESSION['password_reset_attempts'] = [];
    }

    if (isset($_SESSION['password_reset_attempts'][$ip_address])) {
      $attempts = $_SESSION['password_reset_attempts'][$ip_address];

      if ($attempts['blocked_until'] && $attempts['blocked_until'] > $currentTime) {
        return false;
      } elseif ($attempts['count'] >= 5) {
        $_SESSION['password_reset_attempts'][$ip_address] = [
          'count' => 0,
          'blocked_until' => $currentTime + 15 * 60
        ];
        return false;
      }
    } else {
      $_SESSION['password_reset_attempts'][$ip_address] = [
        'count' => 0,
        'blocked_until' => null
      ];
    }

    $_SESSION['password_reset_attempts'][$ip_address]['count']++;
    $_SESSION['password_reset_attempts'][$ip_address]['last_attempt'] = $currentTime;

    return true;
  }

  // Redirige al inicio
  public function solicitudAction()
  {
    header('location: /page/index/');
  }

  // Crea una solicitud de registro con datos completos del proveedor y envía clave temporal
  public function crearSolicitudRegistroAction()
  {
    $this->setLayout('blanco');
    $razon_social = $this->_getSanitizedParam('razon_social');
    $rut_cc_ce_pasaporte = $this->_getSanitizedParam('rut_cc_ce_pasaporte');
    $dv = $this->_getSanitizedParam('dv');
    $email = $this->_getSanitizedParam('email');
    $nombre_contacto = $this->_getSanitizedParam('nombre_contacto');
    $cargo = $this->_getSanitizedParam('cargo');

    $rut_cc_ce_pasaporte = str_replace(['.', ',', '-', ' '], '', $rut_cc_ce_pasaporte);

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

    $validacion = 1;
    if ($razon_social == "" or $razon_social == 1) {
      $validacion = 0;
    }
    if ($rut_cc_ce_pasaporte == "" or $rut_cc_ce_pasaporte == 1) {
      $validacion = 0;
    }
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

  // Crea una solicitud de actualización de correo para un proveedor existente
  public function crearSolicitudCorreoAction()
  {
    $this->setLayout('blanco');
    $razon_social = $this->_getSanitizedParam('razon_social');
    $rut_cc_ce_pasaporte = $this->_getSanitizedParam('rut_cc_ce_pasaporte');
    $dv = $this->_getSanitizedParam('dv');
    $email = $this->_getSanitizedParam('email');
    $nombre_contacto = $this->_getSanitizedParam('nombre_contacto');
    $cargo = $this->_getSanitizedParam('cargo');
    // Honeypot anti-bot
    $user_id = $this->_getSanitizedParam('user_id');

    $rut_cc_ce_pasaporte = str_replace(['.', ',', '-', ' '], '', $rut_cc_ce_pasaporte);

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

  // Obtiene el número acumulado de intentos fallidos de login para un usuario
  public function getIntentos($identificacion)
  {
    $bloqueosModel = new Administracion_Model_DbTable_Bloqueos();
    $infoBloqueo = $bloqueosModel->getList("bloqueo_usuario = '$identificacion'", "bloqueo_id DESC")[0];
    $intento = ($infoBloqueo->bloqueo_intentosfallidos ?? 0) + 1;
    return $intento;
  }

  // Acción de prueba para verificar el envío de correos
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

    $enviado = $emailModel->sed();
    if ($enviado) {
      echo "Correo enviado correctamente.";
    } else {
      echo $emailModel->getMail()->ErrorInfo;
    }
  }
  public function testAction()
  {

    $this->setLayout('blanco');
    $nit = '900375398';
    $url = "http://10.30.240.8/api/Providers/$nit";
    $key = Config_Config::getInstance()->getValue('keys/keyProviders');


    $ch = curl_init();

    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "X-API-Key: $key"
      ],
      CURLOPT_HEADER => true,
      CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      echo "cURL Error:\n";
      echo curl_error($ch);
      curl_close($ch);
      return;
    }

    $info = curl_getinfo($ch);
    $httpCode = $info['http_code'];
    $headerSize = $info['header_size'];

    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    curl_close($ch);

    echo "URL: $url<br>";
    echo "HTTP Code: $httpCode<br>";
    echo "Tiempo total: {$info['total_time']}s<br>";
    echo "Tamaño respuesta: {$info['size_download']} bytes<br><br>";

    echo "HEADERS:<br>" . nl2br($headers) . "<br><br>";
    echo "BODY:<br>" . nl2br($body) . "<br><br>";

    if ($httpCode < 200 || $httpCode >= 300) {
      echo "Error HTTP detectado<br>";

      $json = json_decode($body, true);
      if ($json) {
        echo "Mensaje API: " . ($json['message'] ?? 'Sin mensaje') . "<br>";
      }
    }
  }
}
