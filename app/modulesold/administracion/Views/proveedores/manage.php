<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform;?>"  data-bs-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->proveedor_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->proveedor_id; ?>" />
			<?php }?>
			<div class="row">
				<div class="col-12 form-group">
					<label for="proveedor_comprador"  class="control-label">proveedor_comprador</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado " ><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->proveedor_comprador; ?>" name="proveedor_comprador" id="proveedor_comprador" class="form-control"   >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_numeroOr"  class="control-label">proveedor_numeroOr</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro " ><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->proveedor_numeroOr; ?>" name="proveedor_numeroOr" id="proveedor_numeroOr" class="form-control"   >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_numeroLinea"  class="control-label">proveedor_numeroLinea</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado " ><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->proveedor_numeroLinea; ?>" name="proveedor_numeroLinea" id="proveedor_numeroLinea" class="form-control"   >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_codigoProducto"  class="control-label">proveedor_codigoProducto</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe " ><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->proveedor_codigoProducto; ?>" name="proveedor_codigoProducto" id="proveedor_codigoProducto" class="form-control"   >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_fechaAprobacionOR"  class="control-label">proveedor_fechaAprobacionOR</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_fechaAprobacionOR){ echo $this->content->proveedor_fechaAprobacionOR; } else { echo date('Y-m-d'); } ?>" name="proveedor_fechaAprobacionOR" id="proveedor_fechaAprobacionOR" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_fechaAprobacionSolicitud"  class="control-label">proveedor_fechaAprobacionSolicitud</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_fechaAprobacionSolicitud){ echo $this->content->proveedor_fechaAprobacionSolicitud; } else { echo date('Y-m-d'); } ?>" name="proveedor_fechaAprobacionSolicitud" id="proveedor_fechaAprobacionSolicitud" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_proveedor"  class="control-label">proveedor_proveedor</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde " ><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->proveedor_proveedor; ?>" name="proveedor_proveedor" id="proveedor_proveedor" class="form-control"   >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_nit"  class="control-label">proveedor_nit</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro " ><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->proveedor_nit; ?>" name="proveedor_nit" id="proveedor_nit" class="form-control"   >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_emailProveedor"  class="control-label">proveedor_emailProveedor</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe " ><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->proveedor_emailProveedor; ?>" name="proveedor_emailProveedor" id="proveedor_emailProveedor" class="form-control"   >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_creacionProveedor"  class="control-label">proveedor_creacionProveedor</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_creacionProveedor){ echo $this->content->proveedor_creacionProveedor; } else { echo date('Y-m-d'); } ?>" name="proveedor_creacionProveedor" id="proveedor_creacionProveedor" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_solicitudOferta"  class="control-label">proveedor_solicitudOferta</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_solicitudOferta){ echo $this->content->proveedor_solicitudOferta; } else { echo date('Y-m-d'); } ?>" name="proveedor_solicitudOferta" id="proveedor_solicitudOferta" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_visitaTecnica"  class="control-label">proveedor_visitaTecnica</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_visitaTecnica){ echo $this->content->proveedor_visitaTecnica; } else { echo date('Y-m-d'); } ?>" name="proveedor_visitaTecnica" id="proveedor_visitaTecnica" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_recepcionPreguntas"  class="control-label">proveedor_recepcionPreguntas</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_recepcionPreguntas){ echo $this->content->proveedor_recepcionPreguntas; } else { echo date('Y-m-d'); } ?>" name="proveedor_recepcionPreguntas" id="proveedor_recepcionPreguntas" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_envioRespuestas"  class="control-label">proveedor_envioRespuestas</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_envioRespuestas){ echo $this->content->proveedor_envioRespuestas; } else { echo date('Y-m-d'); } ?>" name="proveedor_envioRespuestas" id="proveedor_envioRespuestas" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_recepcionOferta"  class="control-label">proveedor_recepcionOferta</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_recepcionOferta){ echo $this->content->proveedor_recepcionOferta; } else { echo date('Y-m-d'); } ?>" name="proveedor_recepcionOferta" id="proveedor_recepcionOferta" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_evaluacionTecnica"  class="control-label">proveedor_evaluacionTecnica</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_evaluacionTecnica){ echo $this->content->proveedor_evaluacionTecnica; } else { echo date('Y-m-d'); } ?>" name="proveedor_evaluacionTecnica" id="proveedor_evaluacionTecnica" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_reevaluacion"  class="control-label">proveedor_reevaluacion</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_reevaluacion){ echo $this->content->proveedor_reevaluacion; } else { echo date('Y-m-d'); } ?>" name="proveedor_reevaluacion" id="proveedor_reevaluacion" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_adjudicacion"  class="control-label">proveedor_adjudicacion</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_adjudicacion){ echo $this->content->proveedor_adjudicacion; } else { echo date('Y-m-d'); } ?>" name="proveedor_adjudicacion" id="proveedor_adjudicacion" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="proveedor_fechaContrato"  class="control-label">proveedor_fechaContrato</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde " ><i class="fas fa-calendar-alt"></i></span>
						</div>
					<input type="text" value="<?php if($this->content->proveedor_fechaContrato){ echo $this->content->proveedor_fechaContrato; } else { echo date('Y-m-d'); } ?>" name="proveedor_fechaContrato" id="proveedor_fechaContrato" class="form-control"   data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es"  >
					</label>
					<div class="help-block with-errors"></div>
				</div>
			</div>
		</div>
		<div class="botones-acciones">
			<button class="btn btn-guardar" type="submit">Guardar</button>
			<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div>
	</form>
</div>