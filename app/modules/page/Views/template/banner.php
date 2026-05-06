<div class="slider-coasmedas">
    <div id="carouselprincipal<?php echo $this->seccionbanner;  ?>" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php foreach ($this->banners as $key => $banner){ ?>
            <li data-bs-target="#carouselprincipal<?php echo $this->seccionbanner;  ?>" data-slide-to="0" <?php if($key==0){ ?>class="active"<?php }?> ></li>
            <?php } ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach ($this->banners as $key => $banner){ ?>
            <div class="carousel-item <?php if($key == 0){ ?>active <?php } ?>">
                <a href="<?php echo $banner->cooperativa_enlace; ?>" <?php if ($banner->cooperativa_enlace_abrir == 1) { ?> target="blank"  <?php } ?>>
                        <div class="fondo-imagen" style="background-image: url(/images/<?php echo $banner->cooperativa_fondo_imagen; ?>);"></div>
                        <div class="fondo-imagen-responsive" style="background-image: url(/images/<?php echo $banner->publicidad_imagenresponsive; ?>);"></div>
                </a>
            </div>
            <?php } ?>
        </div>
        <a class="carousel-control-prev" href="#carouselprincipal<?php echo $this->seccionbanner;  ?>" role="button"
            data-slide="prev">
            <span class="carrusel-derecha" aria-hidden="true">
                <i class="fas fa-chevron-left"></i>      
            </span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselprincipal<?php echo $this->seccionbanner;  ?>" role="button"
            data-slide="next">
            <span class="carrusel-izquierda" aria-hidden="true">
                <i class="fas fa-chevron-right"></i> 
            </span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>