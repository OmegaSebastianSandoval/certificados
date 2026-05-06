<?php 

class Page_contactoController extends Page_mainController
{
  public function indexAction()
  {
    $this->_view->home = $this->template->getContentseccion("1");
    $this->_view->contenido2 = $this->template->getContentseccion("2");
  }
}