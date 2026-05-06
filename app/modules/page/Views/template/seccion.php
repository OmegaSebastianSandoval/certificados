<section 
  id="<?php echo $contenedor->contenido_id ?>" 
  class="
    id_<?php echo $contenedor->contenido_id ?> 
    <?php echo $contenedor->contenido_columna; ?> 
    contenedor-seccion 
    <?php if ($contenedor->contenido_fondo_imagen_tipo == 2) { ?>dinamica<?php } ?>" style="background-image:url(/images/<?php echo $contenedor->contenido_fondo_imagen; ?>); background-color:<?php echo $contenedor->contenido_fondo_color; ?>;">
  <div class="content-box container">
		<?php if ($contenedor->contenido_titulo_ver == 1) { ?>
			<h2><?php echo $contenedor->contenido_titulo; ?></h2>
		<?php } ?>
		<?php if ($contenedor->contenido_introduccion != "") { ?>
			<div class="descripcion-seccion"><?php echo $contenedor->contenido_introduccion; ?></div>
		<?php } ?>
		<?php if ($contenedor->contenido_descripcion && $contenedor->contenido_id == '198') { ?>
			<a target="<?php if ($contenedor->contenido_enlace_abrir == '1') { ?>_BLANK<?php } ?>" href='<?php echo $contenedor->contenido_enlace; ?>'>
				<div class="descripcion-seccion ddd"><?php echo $contenedor->contenido_descripcion; ?></div>
			</a>
		<?php } ?>
		<?php if ($contenedor->contenido_descripcion && $contenedor->contenido_id != '198') { ?>
			<div class="descripcion-seccion ddd"><?php echo $contenedor->contenido_descripcion; ?></div>
		<?php } ?>
		<?php if ($contenedor->contenido_enlace && $contenedor->contenido_id != '198') { ?>
			<div class="boton">
				<a href="<?php echo $contenedor->contenido_enlace; ?>" <?php if ($contenedor->contenido_enlace_abrir == 1) { ?>target="_blank" <?php } ?> <?php if ($contenedor->contenedor_enlace_abrir == 1) { ?> target="_blank" <?php } ?> class="btn btn-vermas"> <?php if ($contenedor->contenedor_vermas) { ?><?php echo $contenedor->contenido_vermas; ?><?php } else { ?>VER MÁS<?php } ?></a>
			</div>
		<?php } ?>
		<?php if (is_countable($rescontenido['hijos']) && count($rescontenido['hijos']) > 0) { ?>
			<div class="row <?php if ($contenedor->contenido_columna_alineacion == 2) { ?>justify-content-center text-center<?php } else if ($contenedor->contenido_columna_alineacion == 3) { ?> justify-content-end text-right<?php } else { ?> justify-content-start text-left<?php } ?> <?php if ($contenedor->contenido_columna_espacios == 2 || $contenedor->contenido_columna_espacios == 4) { ?> no-gutters <?php } ?>">

				<?php foreach ($rescontenido['hijos'] as $key => $rescolumna) : ?>
					<?php $columna = $rescolumna['detalle']; ?>
					<div class="<?php echo $columna->contenido_columna; ?>">
						<?php if ($columna->contenido_tipo == 5) { ?>
							<?php $contenido = $columna; ?>
							<?php if ($columna->contenido_disenio == 1) { ?>
								<?php include(APP_PATH . "modules/page/Views/template/disenio1.php"); ?>
							<?php } else if ($columna->contenido_disenio == 2) { ?>
								<?php include(APP_PATH . "modules/page/Views/template/disenio2.php"); ?>
							<?php } else if ($columna->contenido_disenio == 3) { ?>
								<?php include(APP_PATH . "modules/page/Views/template/disenio3.php"); ?>
							<?php } else if ($columna->contenido_disenio == 4) { ?>
								<?php include(APP_PATH . "modules/page/Views/template/disenio4.php"); ?>
							<?php } else if ($columna->contenido_disenio == 5) { ?>
								<?php include(APP_PATH . "modules/page/Views/template/disenio5.php"); ?>
							<?php } else if ($columna->contenido_disenio == 6) {?>
								<?php include(APP_PATH . "modules/page/Views/template/disenio6.php"); ?>
							<?php } ?>
						<?php } else if ($columna->contenido_tipo == 6) { ?>
							<?php $carrousel = $rescolumna['hijos']; ?>
							<?php if ($columna->contenido_disenio == 1) { ?>
								<?php $disenio = APP_PATH . "modules/page/Views/template/disenio1.php"; ?>
							<?php } else if ($columna->contenido_disenio == 2) { ?>
								<?php $disenio = APP_PATH . "modules/page/Views/template/disenio2.php"; ?>
							<?php } else if ($columna->contenido_disenio == 3) { ?>
								<?php $disenio = APP_PATH . "modules/page/Views/template/disenio3.php"; ?>
							<?php } else if ($columna->contenido_disenio == 4) { ?>
								<?php $disenio = APP_PATH . "modules/page/Views/template/disenio4.php"; ?>
							<?php } else if ($columna->contenido_disenio == 5) { ?>
								<?php $disenio = APP_PATH . "modules/page/Views/template/disenio5.php"; ?>
							<?php } ?>
							<div class="<?php if ($columna->contenido_columna_espacios == 1 || $columna->contenido_columna_espacios == 3) { ?>con-espacios<?php } ?>">
								<?php include(APP_PATH . "modules/page/Views/template/carrousel.php"); ?>
							</div>
						<?php } else if ($columna->contenido_tipo == 7) { ?>
							<?php $acordioncontent = $rescolumna['hijos']; ?>
							<?php include(APP_PATH . "modules/page/Views/template/acordion.php"); ?>
						<?php } else if ($columna->contenido_tipo == 8) { ?>
							<?php $slidercontent = $rescolumna['hijos']; ?>
							<?php include(APP_PATH . "modules/page/Views/template/slider.php"); ?>
						<?php } ?>
					</div>
				<?php endforeach ?>
			</div>
		<?php } ?>
	</div>
</section>