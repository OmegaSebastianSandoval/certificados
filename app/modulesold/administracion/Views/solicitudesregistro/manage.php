<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
  <form class="text-left" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>" data-bs-toggle="validator">
    <div class="content-dashboard">
      <input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
      <input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
      <?php if ($this->content->id) { ?>
        <input type="hidden" name="id" id="id" value="<?= $this->content->id; ?>" />
      <?php } ?>
      <div class="row">
        <div class="col-6 form-group">
          <label for="razon_social" class="control-label">Raz&oacute;n social</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" value="<?= $this->content->razon_social; ?>" name="razon_social" id="razon_social" class="form-control" required disabled>
          </label>
          <div class="help-block with-errors"></div>
        </div>
        <div class="col-6 form-group">
          <label for="rut_cc_ce_pasaporte" class="control-label">NIT</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono  fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" value="<?= $this->content->rut_cc_ce_pasaporte; ?>" name="rut_cc_ce_pasaporte" id="rut_cc_ce_pasaporte" class="form-control" required disabled>
          </label>
          <div class="help-block with-errors"></div>
        </div>
        <div class="col-6 form-group">
          <label for="dv" class="control-label">DV</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" value="<?= $this->content->dv; ?>" name="dv" id="dv" class="form-control" required disabled>
          </label>
          <div class="help-block with-errors"></div>
        </div>
        <div class="col-6 form-group">
          <label for="email" class="control-label">Correo</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" value="<?= $this->content->email; ?>" name="email" id="email" class="form-control" required disabled>
          </label>
          <div class="help-block with-errors"></div>
        </div>
        <div class="col-6 form-group">
          <label for="nombre_contacto" class="control-label">Nombre de contacto</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" value="<?= $this->content->nombre_contacto; ?>" name="nombre_contacto" id="nombre_contacto" class="form-control" required disabled>
          </label>
          <div class="help-block with-errors"></div>
        </div>
        <div class="col-6 form-group">
          <label for="cargo" class="control-label">Cargo</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono  fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" value="<?= $this->content->cargo; ?>" name="cargo" id="cargo" class="form-control" disabled>
          </label>
          <div class="help-block with-errors"></div>
        </div>
        <div class="col-6 form-group">
          <label class="control-label">Estado</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono  fondo-rosado "><i class="far fa-list-alt"></i></span>
            </div>
            <select class="form-control" name="estado" disabled>
              <option value="">Seleccione...</option>
              <?php foreach ($this->list_estado as $key => $value) { ?>
                <option <?php if ($this->getObjectVariable($this->content, "estado") == $key) {
                          echo "selected";
                        } ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
              <?php } ?>
            </select>
          </label>
          <div class="help-block with-errors"></div>
        </div>
      </div>
    </div>
    <?php if ($this->content->estado == 0) { ?>
      <div class="col-12">
        <div class="d-flex justify-content-center">
          <a href="/administracion/solicitudesregistro/aprobar?id=<?php echo $this->content->id ?>" class="btn btn-success me-3">Aprobar</a>
          <!-- <a href="/administracion/solicitudesregistro/rechazar?id=<?php echo $this->content->id ?>" class="btn btn-danger">Rechazar</a> -->
        </div>
      </div>
    <?php } ?>
    <!-- <div class="botones-acciones">
        <button class="btn btn-guardar" type="submit">Guardar</button>
        <a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div> -->
  </form>
</div>