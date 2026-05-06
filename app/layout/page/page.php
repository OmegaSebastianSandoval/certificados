<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title><?= $this->_titlepage ?></title>
  <?php $infopageModel = new Page_Model_DbTable_Informacion();
  $infopage = $infopageModel->getById(1);
  ?>
  <!-- Skins Carousel -->
  <!-- <link rel="stylesheet" type="text/css" href="/scripts/carousel/carousel.css"> -->
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="/components/bootstrap/css/bootstrap.min.css">
  <!-- Slick CSS -->
  <!-- <link rel="stylesheet" href="/components/slick/slick.css">
  <link rel="stylesheet" href="/components/slick/slick-theme.css"> -->
  <!-- Global CSS -->
  <link rel="stylesheet" href="/skins/page/css/global.css?v=5.00">
  <link rel="stylesheet" href="/skins/page/css/responsive.css?v=5.00">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="/components/Font-Awesome/css/all.css">

  <link rel="shortcut icon" href="/images/<?= $infopage->info_pagina_favicon; ?>">


  <script type="text/javascript" id="www-widgetapi-script"
    src="https://s.ytimg.com/yts/jsbin/www-widgetapi-vflS50iB-/www-widgetapi.js" async=""></script>

  <!-- Jquery -->
  <script src="/components/jquery/jquery-3.6.0.min.js"></script>
  <!-- Popper -->
  <!-- <script src="https://unpkg.com/@popperjs/core@2"></script> -->
  <script src="/components/umd/popper.min.js"></script>
  <!-- Bootstrap Js -->
  <script src="/components/bootstrap/js/bootstrap.min.js"></script>
  <!-- Carousel -->
  <!-- <script type="text/javascript" src="/scripts/carousel/carousel.js"></script> -->
  <!-- Slick 
  <script type="text/javascript" src="//cdn.jsdelivr.net/npm/[email protected]/slick/slick.min.js"></script>
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/[email protected]/slick/slick.css" />
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/[email protected]/slick/slick-theme.css" />
  <script src="/components/fullpage/slick/slick.min.js"></script>
-->
  <!--<script src="/components/jquery-knob/js/jquery.knob.js"></script>-->

  <!-- SweetAlert -->
  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
  <script src="/components/sweetalert/sweetalert2@11.js"></script>

  <!-- Main Js -->
  <script src="/skins/page/js/main.js?v=10.01"></script>

  <!-- Recaptcha -->
  <script src='https://www.google.com/recaptcha/api.js'></script>
  <meta name="description" content="<?= $this->_data['meta_description']; ?>" />
  <meta name=" keywords" content="<?= $this->_data['meta_keywords']; ?>" />
  <?php echo $this->_data['scripts']; ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>
  <header>
    <?= $this->_data['header']; ?>
  </header>
  <div class="contenedor-general"><?= $this->_content ?></div>
  <footer>
    <?= $this->_data['footer']; ?>
  </footer>
  <?= $this->_data['adicionales']; ?>

</body>

</html>