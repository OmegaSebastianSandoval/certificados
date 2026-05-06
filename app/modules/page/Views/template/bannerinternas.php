<?php foreach($this->coops as $key => $seccion){ ?>
    <?php foreach($seccion as $key1 => $banner){ ?>
        <?php foreach($banner as $key2 => $itemBanner){ ?>
            <?php foreach($itemBanner as $key3 => $item){ ?>
                <div class="banner-internas" style="margin-top: 50px;">
                    <div id="carouselprincipal<?php echo $item->cooperativa_id;  ?>" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php foreach($item as $key4 => $it){ ?>
                            <li data-bs-target="#carouselprincipal<?php echo $item->cooperativa_id;  ?>" data-slide-to="0" <?php if($key4==0){ ?>class="active"<?php }?> ></li>
                        <?php } ?>
                    </ol>
                        <div class="carousel-inner">
                            <?php foreach($item as $key4 => $it){ ?>
                                <?php if($it->cooperativa_id){ ?>
                                    <div class="carousel-item <?php if($key4 == 0){ ?>active <?php } ?>">
                                        <a href="<?php echo $it->cooperativa_enlace; ?>" <?php if ($it->cooperativa_enlace_abrir == 1) { ?> target="_blank"  <?php } ?>>
                                                <div class="fondo-imagen" style="background-image: url(/images/<?php echo $it->cooperativa_fondo_imagen; ?>);"></div>
                                        </a>
                                    </div>
                                <?php } ?>
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
            <?php } ?>
        <?php } ?>
    <?php } ?>
<?php } ?>
