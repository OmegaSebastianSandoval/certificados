<div class="slider-principal">
  <div id="carouselprincipal<?php echo $this->seccionbanner;  ?>" class="carousel slide" data-bs-ride="carousel">
    <!-- <ol class="carousel-indicators">
      <?php foreach ($this->banners as $key => $banner){ ?>
      <li data-bs-target="#carouselprincipal<?php echo $this->seccionbanner;  ?>" data-slide-to="0" <?php if($key==0
                ){ ?>class="active" <?php }  ?>></li>
      <?php } ?>
    </ol> -->
    <div class="carousel-inner">
      <?php foreach ($this->banners as $key => $banner){ ?>
      <div class="carousel-item <?php if($key == 0){ ?>active <?php } ?>">
        <a href="<?php echo $banner->publicidad_enlace; ?>" <?php if ($banner->publicidad_tipo_enlace == 1) { ?>
          target="blank" <?php } ?>>
          <?php if($this->id_youtube($banner->publicidad_video) != false){ ?>
          <div class="fondo-video-youtube">
            <div class="banner-video-youtube" id="videobanner<?php echo $banner->publicidad_id;?> "
              data-video="<?php echo $this->id_youtube($banner->publicidad_video); ?>"></div>
          </div>
          <?php } else { ?>
          <!--<div class="fondo-imagen" style="background-image: url(/images/<?php echo $banner->publicidad_imagen; ?>);"></div>-->
          <div class="fondo-imagen d-none d-sm-flex justify-content-center align-items-center">
            <img src="/images/<?php echo $banner->publicidad_imagen;?>" alt="">
            <!-- <h2><?php echo $banner->publicidad_nombre; ?></h2> -->
          </div>
          <!-- <img src="/images/<?php echo $banner->publicidad_imagen; ?>" style="width:100%;" class="fondo-imagen"> -->
          <div class="fondo-imagen-responsive d-sm-none d-flex justify-content-center align-items-center">
            <img src="/images/<?php echo $banner->publicidad_imagenresponsive;?>" alt="">
            <!-- <h2><?php echo $banner->publicidad_nombre; ?></h2> -->
          </div>
          <!-- <img src="/images/<?php echo $banner->publicidad_imagenresponsive; ?>" style="width:100%;" class="fondo-imagen-responsive"> -->
          <?php } ?>
        </a>
      </div>
      <?php } ?>
    </div>
    <button type="button" class="carousel-control-prev"
      data-bs-target="#carouselprincipal<?php echo $this->seccionbanner;  ?>" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button type="button" class="carousel-control-next"
      data-bs-target="#carouselprincipal<?php echo $this->seccionbanner;  ?>" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</div>