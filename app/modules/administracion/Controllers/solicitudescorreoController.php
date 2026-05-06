<?php

/**
 * Controlador de Solicitudesregistro que permite la  creacion, edicion  y eliminacion de los Solicitudes de Registro del Sistema
 */
class Administracion_solicitudescorreoController extends Administracion_mainController
{
  public $botonpanel = 6;
  /**
   * $mainModel  instancia del modelo de  base de datos Solicitudes de Registro
   * @var modeloContenidos
   */
  public $mainModel;

  /**
   * $route  url del controlador base
   * @var string
   */
  protected $route;

  /**
   * $pages cantidad de registros a mostrar por pagina]
   * @var integer
   */
  protected $pages;

  /**
   * $namefilter nombre de la variable a la fual se le van a guardar los filtros
   * @var string
   */
  protected $namefilter;

  /**
   * $_csrf_section  nombre de la variable general csrf  que se va a almacenar en la session
   * @var string
   */
  protected $_csrf_section = "administracion_solicitudescorreo";

  /**
   * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
   * @var string
   */
  protected $namepages;



  /**
   * Inicializa las variables principales del controlador solicitudesregistro .
   *
   * @return void.
   */
  public function init()
  {
    $this->mainModel = new Administracion_Model_DbTable_Solicitudescorreo();
    $this->namefilter = "parametersfiltersolicitudescorreo";
    $this->route = "/administracion/solicitudescorreo";
    $this->namepages = "pages_solicitudescorreo";
    $this->namepageactual = "page_actual_solicitudescorreo";
    $this->_view->route = $this->route;
    if (Session::getInstance()->get($this->namepages)) {
      $this->pages = Session::getInstance()->get($this->namepages);
    } else {
      $this->pages = 20;
    }
    parent::init();
  }


  /**
   * Recibe la informacion y  muestra un listado de  Solicitudes de Registro con sus respectivos filtros.
   *
   * @return void.
   */
  public function indexAction()
  {
    $title = "Aministración de Solicitudes de Cambio de Correo";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;
    $this->filters();
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $filters = (object)Session::getInstance()->get($this->namefilter);
    $this->_view->filters = $filters;
    $filters = $this->getFilter();
    $order = "";
    $list = $this->mainModel->getList($filters, $order);
    $amount = $this->pages;
    $page = $this->_getSanitizedParam("page");
    if (!$page && Session::getInstance()->get($this->namepageactual)) {
      $page = Session::getInstance()->get($this->namepageactual);
      $start = ($page - 1) * $amount;
    } else if (!$page) {
      $start = 0;
      $page = 1;
      Session::getInstance()->set($this->namepageactual, $page);
    } else {
      Session::getInstance()->set($this->namepageactual, $page);
      $start = ($page - 1) * $amount;
    }
    $this->_view->register_number = count($list);
    $this->_view->pages = $this->pages;
    $this->_view->totalpages = ceil(count($list) / $amount);
    $this->_view->page = $page;
    $this->_view->lists = $this->mainModel->getListPages($filters, $order, $start, $amount);
    $this->_view->csrf_section = $this->_csrf_section;
    $this->_view->list_estado = $this->getEstado();
  }

  /**
   * Genera la Informacion necesaria para editar o crear un  Solicitudes de Registro  y muestra su formulario
   *
   * @return void.
   */
  public function manageAction()
  {
    $this->_view->route = $this->route;
    $this->_csrf_section = "manage_solicitudesregistro_" . date("YmdHis");
    $this->_csrf->generateCode($this->_csrf_section);
    $this->_view->csrf_section = $this->_csrf_section;
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $this->_view->list_estado = $this->getEstado();
    $id = $this->_getSanitizedParam("id");
    if ($id > 0) {
      $content = $this->mainModel->getById($id);
      if ($content->id) {
        $this->_view->content = $content;
        $this->_view->routeform = $this->route . "/update";
        $title = "Actualizar Solicitudes de Registro";
        $this->getLayout()->setTitle($title);
        $this->_view->titlesection = $title;
      } else {
        $this->_view->routeform = $this->route . "/insert";
        $title = "Crear Solicitudes de Registro";
        $this->getLayout()->setTitle($title);
        $this->_view->titlesection = $title;
      }
    } else {
      $this->_view->routeform = $this->route . "/insert";
      $title = "Crear Solicitudes de Registro";
      $this->getLayout()->setTitle($title);
      $this->_view->titlesection = $title;
    }
  }

  /**
   * Inserta la informacion de un Solicitudes de Registro  y redirecciona al listado de Solicitudes de Registro.
   *
   * @return void.
   */
  public function insertAction()
  {
    $this->setLayout('blanco');
    $csrf = $this->_getSanitizedParam("csrf");
    if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
      $data = $this->getData();
      $id = $this->mainModel->insert($data);

      $data['id'] = $id;
      $data['log_log'] = print_r($data, true);
      $data['log_tipo'] = 'CREAR SOLICITUDES DE CAMBIO DE CORREO';
      $logModel = new Administracion_Model_DbTable_Log();
      $logModel->insert($data);
    }
    header('Location: ' . $this->route . '' . '');
  }

  /**
   * Recibe un identificador  y Actualiza la informacion de un Solicitudes de Registro  y redirecciona al listado de Solicitudes de Registro.
   *
   * @return void.
   */
  public function updateAction()
  {
    $this->setLayout('blanco');
    $csrf = $this->_getSanitizedParam("csrf");
    if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
      $id = $this->_getSanitizedParam("id");
      $content = $this->mainModel->getById($id);
      if ($content->id) {
        $data = $this->getData();
        $this->mainModel->update($data, $id);
      }
      $data['id'] = $id;
      $data['log_log'] = print_r($data, true);
      $data['log_tipo'] = 'REVISAR SOLICITUDES DE CAMBIO DE CORREO';
      $logModel = new Administracion_Model_DbTable_Log();
      $logModel->insert($data);
    }
    header('Location: ' . $this->route . '' . '');
  }

  /**
   * Recibe un identificador  y elimina un Solicitudes de Registro  y redirecciona al listado de Solicitudes de Registro.
   *
   * @return void.
   */
  public function deleteAction()
  {
    $this->setLayout('blanco');
    $csrf = $this->_getSanitizedParam("csrf");
    if (Session::getInstance()->get('csrf')[$this->_csrf_section] == $csrf) {
      $id =  $this->_getSanitizedParam("id");
      if (isset($id) && $id > 0) {
        $content = $this->mainModel->getById($id);
        if (isset($content)) {
          $this->mainModel->deleteRegister($id);
          $data = (array)$content;
          $data['log_log'] = print_r($data, true);
          $data['log_tipo'] = 'BORRAR SOLICITUDES DE REGISTRO';
          $logModel = new Administracion_Model_DbTable_Log();
          $logModel->insert($data);
        }
      }
    }
    header('Location: ' . $this->route . '' . '');
  }

  /**
   * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Solicitudesregistro.
   *
   * @return array con toda la informacion recibida del formulario.
   */
  private function getData()
  {
    $data = array();
    $data['razon_social'] = $this->_getSanitizedParam("razon_social");
    $data['rut_cc_ce_pasaporte'] = $this->_getSanitizedParam("rut_cc_ce_pasaporte");
    $data['dv'] = $this->_getSanitizedParam("dv");
    $data['email'] = $this->_getSanitizedParam("email");
    $data['nombre_contacto'] = $this->_getSanitizedParam("nombre_contacto");
    $data['cargo'] = $this->_getSanitizedParam("cargo");
    $data['estado'] = $this->_getSanitizedParam("estado");
    $data['created_at'] = $this->_getSanitizedParam("created_at");
    $data['updated_at'] = $this->_getSanitizedParam("updated_at");
    return $data;
  }

  /**
   * Genera los valores del campo Estado.
   *
   * @return array cadena con los valores del campo Estado.
   */
  private function getEstado()
  {
    $array = array();
    $array['Data'] = 'Data';
    return $array;
  }

  /**
   * Genera la consulta con los filtros de este controlador.
   *
   * @return array cadena con los filtros que se van a asignar a la base de datos
   */
  protected function getFilter()
  {
    $filtros = " 1 = 1 ";
    if (Session::getInstance()->get($this->namefilter) != "") {
      $filters = (object)Session::getInstance()->get($this->namefilter);
      if ($filters->razon_social != '') {
        $filtros = $filtros . " AND razon_social LIKE '%" . $filters->razon_social . "%'";
      }
      if ($filters->rut_cc_ce_pasaporte != '') {
        $filtros = $filtros . " AND rut_cc_ce_pasaporte LIKE '%" . $filters->rut_cc_ce_pasaporte . "%'";
      }
      if ($filters->email != '') {
        $filtros = $filtros . " AND email LIKE '%" . $filters->email . "%'";
      }
      if ($filters->nombre_contacto != '') {
        $filtros = $filtros . " AND nombre_contacto LIKE '%" . $filters->nombre_contacto . "%'";
      }
      if ($filters->estado != '') {
        $filtros = $filtros . " AND estado ='" . $filters->estado . "'";
      }
    }
    return $filtros;
  }

  /**
   * Recibe y asigna los filtros de este controlador
   *
   * @return void
   */
  protected function filters()
  {
    if ($this->getRequest()->isPost() == true) {
      Session::getInstance()->set($this->namepageactual, 1);
      $parramsfilter = array();
      $parramsfilter['razon_social'] =  $this->_getSanitizedParam("razon_social");
      $parramsfilter['rut_cc_ce_pasaporte'] =  $this->_getSanitizedParam("rut_cc_ce_pasaporte");
      $parramsfilter['email'] =  $this->_getSanitizedParam("email");
      $parramsfilter['nombre_contacto'] =  $this->_getSanitizedParam("nombre_contacto");
      $parramsfilter['estado'] =  $this->_getSanitizedParam("estado");
      Session::getInstance()->set($this->namefilter, $parramsfilter);
    }
    if ($this->_getSanitizedParam("cleanfilter") == 1) {
      Session::getInstance()->set($this->namefilter, '');
      Session::getInstance()->set($this->namepageactual, 1);
    }
  }
  public function aprobarAction()
  {
    $id = $this->_getSanitizedParam("id");
    $solicitudesModel = new Administracion_Model_DbTable_Solicitudescorreo();
    $solicitud = $solicitudesModel->getById($id);
    
    $nit = $solicitud->rut_cc_ce_pasaporte;

    $usersModel = new Administracion_Model_DbTable_Usuarios();
    $user = $usersModel->getList("identificacion = '$nit'")[0];

    $usersModel->editField($user->id, 'email', $solicitud->email);
    $solicitudesModel->editField($id, 'estado', '1');

    $user = $usersModel->getById($user->id);
    
    $mailModel = new Core_Model_Sendingemail($this->_view);
    $mailModel->enviarAprobacion($user);
    header('Location: ' . $this->route . '' . '');
  }
  private function generarClave()
  {
    $longitud = 8;
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $caracteresLongitud = strlen($caracteres);
    $contraseña = '';
    for ($i = 0; $i < $longitud; $i++) {
      $contraseña .= $caracteres[random_int(0, $caracteresLongitud - 1)];
    }
    return $contraseña;
  }
}
