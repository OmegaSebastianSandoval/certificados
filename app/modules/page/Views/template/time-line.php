<div class="container container-time-line"  align="center">
    <?php if($columna->contenido_titulo_ver == 1){ ?>
        <div class="page-header">
            <h3 style="text-align: center !important;"><?php echo $columna->contenido_titulo; ?></h3>
        </div>
    <?php } ?>
    <div id="timeline" style="width: 90%;" class="d-sm-block d-none">
        <div class="row timeline-movement timeline-movement-top">


        </div>
        <?php foreach ($fechas as $key => $fecha) { ?>
            <div class="row timeline-movement">
                <div class="timeline-badge <?php if ($key % 2 == 0) { echo 'left';} ?>">
                    
                </div>
                <div class="col-sm-6  timeline-item <?php if ($key % 2 != 0) { echo 'cel-linea';} else { echo ' margen-linea';} ?>" <?php if ($key % 2 != 0) {echo 'style="visibility:hidden;"';} ?>>
                    <div class="row no-margen">
                        <div class="col-sm-11 timeline-col p-0">
                            <div class="row timeline-panel credits d-flex justify-content-end">
                                <div class="p-0 ex-col" style="width:max-content;">
                                    <li class="itemLine itemLineLeft p-3 py-4" style="background-color: <?php echo $fecha->contenido_fondo_color; ?> ;">
                                    <button style="background: transparent;" type="button" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#modal_<?php echo $fecha->contenido_id ?>">
                                        <img src="/images/<?php echo $fecha->contenido_imagen; ?>" alt="">
                                        <h3 class="timeline-balloon-date-day"><?php echo $fecha->contenido_titulo ?></h3>
                                    </button>
                                    </li>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-sm-6  timeline-item  <?php if ($key % 2 == 0) {echo 'cel-linea2';} else {echo ' cel-linea3';} ?>" <?php if ($key % 2 == 0) {echo 'style="visibility:hidden;"';} ?>>
                    <div class="row no-margen">
                        <div class="col-sm-offset-1 col-sm-11">
                            <div class="row timeline-panel debits">
                                <div class="p-0" style="width:max-content;">
                                    <li class="itemLine itemLineRight p-3 py-4" style="background-color: <?php echo $fecha->contenido_fondo_color; ?> ;">
                                        <button style="max-width: 100%; background: transparent;" type="button" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#modal_<?php echo $fecha->contenido_id ?>">
                                            <img src="/images/<?php echo $fecha->contenido_imagen; ?>" alt="">
                                            <h3 class="timeline-balloon-date-day"><?php echo $fecha->contenido_titulo ?></h3>
                                        </button>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="container d-sm-none d-flex pl-5 justify-content-center flex-column">
    <?php foreach ($fechas as $key => $fecha) { ?>
        <div class="col-sm-6  timeline-item ml-2 my-3">
            <div class="row no-margen">
                <div class="col-sm-10 timeline-col p-0">
                    <div class="row timeline-panel credits d-flex justify-content-end">
                        <div class="col-sm-6 p-0">
                            <li class="itemLine itemLineLeft p-3 py-4" style="background-color: <?php echo $fecha->contenido_fondo_color; ?> ;">
                            <button style="background: transparent;" type="button" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#modal_<?php echo $fecha->contenido_id ?>">
                                <img src="/images/<?php echo $fecha->contenido_imagen; ?>" alt="">
                                <h3 class="timeline-balloon-date-day"><?php echo $fecha->contenido_titulo ?></h3>
                            </button>
                            </li>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php foreach($fechas as $key => $fecha): ?>
    <div class="modal fade" id="modal_<?php echo $fecha->contenido_id; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #1D266Ce7; border-radius: 35px !important;">
                <div class="modal-header" style="border-bottom: none;">
                    <div class="itemLine2 d-flex flex-column ">
                        <h3 class="timeline-balloon-date-day"><?php echo $fecha->contenido_titulo ?></h3>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #fff;">X</span>
                    </button>
                </div>
                <div class="modal-body" style="color: #fff;">
                    <p><?php echo $fecha->contenido_descripcion ?></p>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>