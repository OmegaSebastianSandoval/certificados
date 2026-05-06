<?php foreach ($this->contenidos as $key => $rescontenido): ?>
	<?php $contenedor = $rescontenido['detalle']; ?>
	<?php if($contenedor->contenido_tipo == 1 ){ ?>
		<?php include(APP_PATH."modules/page/Views/template/bannersimple.php"); ?>
	<?php } else if($contenedor->contenido_tipo == 2 || $contenedor->contenido_tipo == 3){ ?>
		<?php include(APP_PATH."modules/page/Views/template/seccion.php"); ?>
	<?php } ?>
<?php endforeach ?>