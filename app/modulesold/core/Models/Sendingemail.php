<?php

/**
 * Modelo del modulo Core que se encarga de  enviar todos los correos nesesarios del sistema.
 */
class Core_Model_Sendingemail
{
  /**
   * Intancia de la calse emmail
   * @var class
   */
  protected $email;

  protected $_view;

  public function __construct($view)
  {
    $this->email = new Core_Model_Mail();
    $this->_view = $view;
  }


  public function forgotpassword($user)
  {
    if ($user) {
      $code = [];
      $code['user'] = $user->user_id;
      $code['code'] = $user->code;
      $codeEmail = base64_encode(json_encode($code));
      $this->_view->url = "http://" . $_SERVER['HTTP_HOST'] . "/administracion/index/changepassword?code=" . $codeEmail;
      $this->_view->host = "http://" . $_SERVER['HTTP_HOST'] . "/";
      $this->_view->nombre = $user->user_names . " " . $user->user_lastnames;
      $this->_view->usuario = $user->user_user;
      /*fin parametros de la vista */
      //$this->email->getMail()->setFrom("desarrollo4@omegawebsystems.com","Intranet Coopcafam");
      $this->email->getMail()->addAddress($user->user_email, $user->user_names . " " . $user->user_lastnames);
      $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/forgotpassword.php');
      $this->email->getMail()->Subject = "Recuperación de Contraseña Gestor de Contenidos";
      $this->email->getMail()->msgHTML($content);
      $this->email->getMail()->AltBody = $content;
      if ($this->email->sed() == true) {
        return true;
      } else {
        return false;
      }
    }
  }
  public function sendMailContact($data, $mail)
  {
    $this->_view->data = $data;
    if (APPLICATION_ENV == 'production') {
      $this->email->getMail()->addAddress($mail, "");
      //  $this->email->getMail()->addBCC($informacion->info_pagina_correo_oculto);

    }

    $this->email->getMail()->addAddress("desarrollo8@omegawebsystems.com", "desarrollo");

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/');
    $this->email->getMail()->Subject = '';
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    if ($this->email->sed() == true) {
      return 1;
    } else {
      return 2;
    }
  }
  public function enviarRegistro($user)
  {
    $this->_view->user = $user;
    if (APPLICATION_ENV == 'production') {
      $this->email->getMail()->addAddress($user->email, $user->razon_social);
    }

    $this->email->getMail()->addAddress("desarrollo8@omegawebsystems.com", "desarrollo");

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/enviarRegistro.php');
    $this->email->getMail()->Subject = 'Contraseña temporal Opain';
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    // $this->email->getMail()->addBCC($informacion->info_pagina_correo_oculto);
    if ($this->email->sed() == true) {
      return 1;
    } else {
      return 2;
    }
  }
  public function enviarRegistroUsuarios($user, $users)
  {
    $this->_view->user = $user;

    $this->email->getMail()->addAddress("desarrollo8@omegawebsystems.com", "desarrollo");
    if (APPLICATION_ENV == 'production') {
      foreach ($users as $email) {
        $this->email->getMail()->addAddress($email->user_email, $email->user_names);
      }
      $this->email->getMail()->addAddress($user->email, $user->razon_social);
      // $this->email->getMail()->addBCC($informacion->info_pagina_correo_oculto);
    }

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/enviarRegistro.php');
    $this->email->getMail()->Subject = 'Registro - contraseña temporal Opain Certificados';
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;

    if ($this->email->sed() == true) {
      return 1;
    } else {
      return 2;
    }
  }
  public function enviarRecuperacion($user, $token)
  {
    $this->_view->user = $user;
    $this->_view->token = $token;
    if (APPLICATION_ENV == 'production') {
      $this->email->getMail()->addAddress($user->email, $user->razon_social);
      //$this->email->getMail()->addBCC($informacion->info_pagina_correo_oculto);

    }
    $this->email->getMail()->addAddress("desarrollo8@omegawebsystems.com", "desarrollo");
    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/enviarRecuperacion.php');
    $this->email->getMail()->Subject = 'Recuperación de contraseña Opain';
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    if ($this->email->sed() == true) {
      return 1;
    } else {
      return 2;
    }
  }
  public function enviarSolicitud($info, $emails)
  {
    $this->_view->info = $info;
    if (APPLICATION_ENV == 'production') {
      foreach ($emails as $email) {
        $this->email->getMail()->addAddress($email->user_email, $email->user_names);
      }
      // $this->email->getMail()->addBCC($informacion->info_pagina_correo_oculto);
    }
    $this->email->getMail()->addAddress("soporteomega@omegawebsystems.com", "desarrollo");
    $this->email->getMail()->addAddress("desarrollo8@omegawebsystems.com", "desarrollo");
    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/enviarSolicitud.php');
    $this->email->getMail()->Subject = 'Solicitud de registro de empresa Opain';
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    if ($this->email->sed() == true) {
      return 1;
    } else {
      return 2;
    }
  }
  public function enviarSolicitudCorreo($info, $emails)
  {
    $this->_view->info = $info;
    if (APPLICATION_ENV == 'production') {
      foreach ($emails as $email) {
        $this->email->getMail()->addAddress($email->user_email, $email->user_names);
      }
      // $this->email->getMail()->addBCC($informacion->info_pagina_correo_oculto);
    }
    $this->email->getMail()->addAddress("soporteomega@omegawebsystems.com", "desarrollo");
    $this->email->getMail()->addAddress("desarrollo8@omegawebsystems.com", "desarrollo");

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/enviarSolicitudCorreo.php');
    $this->email->getMail()->Subject = 'Solicitud de cambio de correo Opain';
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    if ($this->email->sed() == true) {
      return 1;
    } else {
      return 2;
    }
  }
  public function enviarAprobacion($user)
  {
    $this->_view->user = $user;
    if (APPLICATION_ENV == 'production') {
      $this->email->getMail()->addAddress($user->email, $user->razon_social);
      //  $this->email->getMail()->addBCC($informacion->info_pagina_correo_oculto);
    }
    $this->email->getMail()->addAddress("soporteomega@omegawebsystems.com", "desarrollo");
    $this->email->getMail()->addAddress("desarrollo8@omegawebsystems.com", "desarrollo");

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/enviarAprobacion.php');
    $this->email->getMail()->Subject = 'Cambio de correo Opain';
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    if ($this->email->sed() == true) {
      return 1;
    } else {
      return 2;
    }
  }
}
