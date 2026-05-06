<ul>
  <?php if (Session::getInstance()->get('kt_login_level') == '1') { ?>
   <!-- <li <?php if ($this->botonpanel == 1) { ?>class="activo" <?php } ?>>
      <a href="/administracion/panel">
        <i class="fas fa-info-circle"></i> 
        Información Página
      </a>
    </li>-->
  <?php } ?>
  <li <?php if ($this->botonpanel == 2) { ?>class="activo" <?php } ?>>
    <a href="/administracion/publicidad">
      <i class="far fa-images"></i> 
      Administrar Publicidad
    </a>
  </li>
  <li <?php if ($this->botonpanel == 3) { ?>class="activo" <?php } ?>>
    <!--<a href="/administracion/contenido">
      <i class="fas fa-file-invoice"></i> 
      Administrar Contenidos
    </a>-->
  </li>
  <?php if (Session::getInstance()->get('kt_login_level') == '1') { ?>
    <li <?php if ($this->botonpanel == 4) { ?>class="activo" <?php } ?>>
      <a href="/administracion/usuario">
        <i class="fas fa-users"></i> 
        Administrar Usuarios
      </a>
    </li>
  <?php } ?>
  <?php if (Session::getInstance()->get('kt_login_level') == '1') { ?>
    <li <?php if ($this->botonpanel == 4) { ?>class="activo" <?php } ?>>
      <a href="/administracion/usuarios">
        <i class="fa-regular fa-building"></i>
        Administrar Empresas
      </a>
    </li>
  <?php } ?>
  <?php if (Session::getInstance()->get('kt_login_level') == '1') { ?>
    <li <?php if ($this->botonpanel == 5) { ?>class="activo" <?php } ?>>
      <a href="/administracion/solicitudesregistro">
        <i class="fa-solid fa-list"></i>
        Solicitudes de Registro
      </a>
    </li>
  <?php } ?>
  <?php if (Session::getInstance()->get('kt_login_level') == '1') { ?>
    <li <?php if ($this->botonpanel == 6) { ?>class="activo" <?php } ?>>
      <a href="/administracion/solicitudescorreo">
      <i class="fa-solid fa-envelope"></i>
        Solicitudes de Cambio de correo
      </a>
    </li>
  <?php } ?>
</ul>