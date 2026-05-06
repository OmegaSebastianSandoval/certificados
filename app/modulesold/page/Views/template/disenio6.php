<div class="caja-contenido-redonda p-3 d-flex justify-content-center" style="background-color: <?php if($contenido->contenido_fondo_color){ echo  $contenido->contenido_fondo_color;  } else if($colorfondo){ echo $colorfondo; }   ?>; <?php if($contenido->contenido_borde == '1'){echo 'border: 2px solid #13436B; border-radius:20px;';} ?>">
	
    <button type="button" class=""  data-bs-toggle="modal" data-bs-target="#myModalphp<?php echo $contenido->contenido_id; ?>">
        <?php if($contenido->contenido_titulo_ver == 1){ ?>
            <h2><?php echo $contenido->contenido_titulo; ?></h2>
        <?php } ?>
    </button>

	
</div>
<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="myModalphp<?php echo $contenido->contenido_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content" style="background-color: #2f5b95e7;">
            <div class="modal-header" style="border: none !important;">
                <h3 class="modal-title text-white" id="exampleModalCenterTitle ">
                <?php if($contenido->contenido_titulo_ver == 1){ ?>
                    <?php echo $contenido->contenido_titulo; ?>
                <?php } ?>
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #fff;">X</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="descripcion text-white">
                        <?php echo $contenido->contenido_descripcion; ?>
                    </div>
                    <?php if($contenido->contenido_imagen){ ?>
                        <div class="imagen-contenido">
                            <div>
                                <img src="/images/<?php echo $contenido->contenido_imagen; ?>">
                            </div>
                        </div>
                        </br>
	                <?php } ?>
                    <?php if($contenido->contenido_enlace){ ?>
                        <div>
                            <a href="<?php $contenido->contenido_enlace; ?>" <?php if($contenido->contenido_enlace_abrir == 1){ ?> target="_blank" <?php } ?>   class="btn btn-block btn-vermas"> <?php if( $contenido->contenido_vermas){ ?><?php echo $contenido->contenido_vermas; ?><?php } else { ?>Ver Más<?php } ?></a>
                        </div>
		            <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>