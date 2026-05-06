<div class="certificados-bx">
  <div class="container">
    <div class="row">
      <div class="col-12 mb-5">
        <div class="row align-items-center">
          <div class="col-md-5 text-right pe-3">
            <h3>Certificados</h3>
          </div>
          <div class="col-md-7">
            <p>Descarga aquí los certificados de retención en la fuente por IVA, ICA, Renta y Pagos. </p>
			<div class="alert alert-warning" role="alert">
			  En caso que el documento le solicite una clave, por favor ingrese su NIT sin puntos ni dígito de verificación.
			</div>
          </div>
        </div>
      </div>
      <input type="hidden" name="csrf_token" value="<?php echo Session::getInstance()->get('csrf_token_user') ?>">
      <div class="col-12 text-center">
        <div class="row">
          <div class="col-12 col-md-6 col-lg-3 px-4">
            <div class="row">
              <div class="col-12">
                <div class="image-bx">
                  <img src="/skins/page/images/retefuente-bg.png?v=1.0" alt="" class="bg-image rounded-4">
                  <div class="text">
                    <img src="/skins/page/images/icono-busqueda.png" alt="" class="icon">
                    <span>Retefuente</span>
                  </div>
                </div>
              </div>
              <div class="col-12 py-3">
                <span>
                  Seleccionar por año
                </span>
              </div>
              <div class="col-10 mx-auto">
                <select name="" id="select-fuente" class="form-control">
                  <option value="" selected disabled>
                    Año
                  </option>
                  <?php for($i = 2022; $i <= date('Y'); $i++){ ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-12 mt-3">
                <button class="btn-fuente">
                  Buscar
                </button>
              </div>
              <div class="col-12">
                <div class="row mt-3 docs docsRetefuente" id="docs_fuente">
                  
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-lg-3 px-4">
            <div class="row">
              <div class="col-12">
                <div class="image-bx">
                  <img src="/skins/page/images/reteica-bg.png?v=1.0" alt="" class="bg-image rounded-4">
                  <div class="text">
                    <img src="/skins/page/images/icono-busqueda.png" alt="" class="icon">
                    <span>Reteica</span>
                  </div>
                </div>
              </div>
              <div class="col-12 py-3">
                <span>
                  Seleccionar por año
                </span>
              </div>
              <div class="col-10 mx-auto">
                <select name="" id="select-ica" class="form-control">
                  <option value="" selected disabled>
                    Año
                  </option>
                  <?php for($i = 2022; $i <= date('Y'); $i++){ ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-12 mt-3">
                <button class="btn-ica">
                  Buscar
                </button>
              </div>
              <div class="col-12">
                <div class="row mt-3 docs docsRetefuente" id="docs_ica">
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-lg-3 px-4">
            <div class="row">
              <div class="col-12">
                <div class="image-bx">
                  <img src="/skins/page/images/reteiva-bg.png?v=1.0" alt="" class="bg-image rounded-4">
                  <div class="text">
                    <img src="/skins/page/images/icono-busqueda.png" alt="" class="icon">
                    <span>Reteiva</span>
                  </div>
                </div>
              </div>
              <div class="col-12 py-3">
                <span>
                  Seleccionar por año
                </span>
              </div>
              <div class="col-10 mx-auto">
                <select name="" id="select-iva" class="form-control">
                  <option value="" selected disabled>
                    Año
                  </option>
                  <?php for($i = 2022; $i <= date('Y'); $i++){ ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-12 mt-3">
                <button class="btn-iva">
                  Buscar
                </button>
              </div>
              <div class="col-12">
                <div class="row mt-3 docs docsRetefuente" id="docs_iva">
                  
                </div>
              </div>
            </div>
          </div>


           <div class="col-12 col-md-6 col-lg-3 px-4">
            <div class="row">
              <div class="col-12">
                <div class="image-bx" style="background-color: #b1d5ff;">
                  <img src="/skins/page/images/nota.jpg" alt="" class="bg-image rounded-4" style="mix-blend-mode: multiply;">
                  <div class="text">
                    <img src="/skins/page/images/icono-busqueda.png" alt="" class="icon">
                    <span>Pagos</span>
                  </div>
                </div>
              </div>
              <div class="col-12 py-3">
                <span>
                  Seleccionar por año
                </span>
              </div>
              <div class="col-10 mx-auto">
                <select name="" id="select-proveedores" class="form-control">
                  <option value="" selected disabled>
                    Año
                  </option>
                  <?php for($i = 2022; $i <= date('Y'); $i++){ ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-12 mt-3">
                <button class="btn-proveedores">
                  Buscar
                </button>
              </div>
              <div class="col-12">
                <div class="row mt-3 docs docsProveedores" id="docs_proveedores">
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .bg-image {
    width: 300px;
    height: 300px;
    object-fit: cover;
  }
  
  .button-modal {
  cursor: pointer;
  width: 205px;
  margin-left: 30px;
}

.modal-content {
  border: none;
  background-color: transparent;
}

</style>
<script>
$(document).ready(function() {
  <?php if(  $this->popup->publicidad_estado==1){ ?>
  $("#popup").modal("show");
  <?php }?>
  //   setTimeout(function() {$('#popup').modal('hide');}, 18000);

});
</script>
<?php if(  $this->popup->publicidad_estado==1){ ?>
<div class="modal fade" id="popup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
      <div class="modal-body">
        <?php if($this->popup->publicidad_video!="") {?>
        <div class="fondo-video-youtube">
          <div class="banner-video-youtube" id="videobanner<?php echo $this->popup->publicidad_id;?> "
            data-video="<?php echo $this->id_youtube($this->popup->publicidad_video); ?>"></div>
        </div>
        <?php } ?>
        <?php if($this->popup->publicidad_imagen!="") {?>
        <?php if($this->popup->publicidad_enlace!="") {?> <a href="<?php echo $this->popup->publicidad_enlace ?>"
          <?php if($this->popup->publicidad_enlace==1) { echo "target='_blank'"; } ?>> <?php }?><img
            src="/images/<?php echo $this->popup->publicidad_imagen ?>"
            alt=""><?php if($this->popup->publicidad_enlace!="") {?> </a><?php }?>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<?php } ?>