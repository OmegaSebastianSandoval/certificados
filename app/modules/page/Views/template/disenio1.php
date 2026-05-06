<div class="caja-contenido-simple design-one" style="background-color: <?php if($contenido->contenido_fondo_color){ echo  $contenido->contenido_fondo_color;  } else if($colorfondo){ echo $colorfondo; }   ?>">
	<?php if($contenido->contenido_titulo_ver == 1){ ?>
		<h2><?php echo $contenido->contenido_titulo; ?></h2>
	<?php } ?>
	<div class="row">
		<div <?php if($contenido->contenido_imagen){ ?>class="col-sm-9"<?php } else { ?>class="col-sm-12"<?php } ?>>
			<div class="descripcion">
				<?php echo $contenido->contenido_descripcion; ?>
			</div>
			<?php if ($contenido->contenido_archivo) { ?>
				<div align="center" class="archivo">
					<a href="/files/<?php echo $contenido->contenido_archivo ?>" target="blank">Descargar Archivo <i class="fas fa-download"></i></a>
				</div>
			<?php } ?>
			<div>
				<?php if ($contenido->contenido_enlace) { ?>
					<a href="" class="btn btn-block btn-vermas" <?php if ($contenido->contenido_enlace_abrir == 1) { ?> target="blank"  <?php } ?> > <?php if( $contenido->contenido_vermas){ ?><?php echo $contenido->contenido_vermas; ?><?php } else { ?>Ver Más<?php } ?></a>
				<?php } ?>
			</div>
		</div>
		<?php if($contenido->contenido_imagen){ ?>
			<div class="col-sm-3">
				<div class="text-center"><img src="/images/<?php echo $contenido->contenido_imagen; ?>"></div>
			</div>
		<?php } ?>
	</div>
</div>