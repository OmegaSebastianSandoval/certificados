	<div class='carouselsection'>
		<div class='left_scroll caja-control carrr'> <i class="fas fa-chevron-circle-left fa-2x"></i> </div>

		<div class="carousel_inner carr">

			<ul>
				<?php $colorfondo = $columna->contenido_fondo_color; ?>
				<?php foreach ($carrousel as $key => $contenido) : ?>
					<li>

						<?php include($disenio); ?>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
		<div class='right_scroll carrr caja-control'> <i class="fas fa-chevron-circle-right fa-2x"></i> </div>
	</div>