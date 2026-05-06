<div class="bx-general bx-login">
  <div class="row mx-0">
    <div class="col-md-6 login-bg pt-5">
      <img src="/skins/page/images/login-bg.png" alt="">
    </div>
    <div class="col-md-6">
      <div class="row w-100 text-center justify-content-center">
        <div class="col-lg-9 col-md-10 col-11">
          <div class="row">
            <div class="col-12">
              <img src="/skins/page/images/logo-opain.svg" alt="" class="logo">
            </div>
            <div class="col-12">
              <h3 class="login-title">Cambia tu contraseña</h3>
            </div>
            <div class="col-lg-10 col-md-12 mx-auto">
              <p class="login-par">
                Cambia tu contraseña temporal por una nueva, esto ocurrirá solo una vez.
              </p>
            </div>
            <div class="col-12 mb-5 mt-5">
              <div class="d-flex login-terms justify-content-center align-items-center">
                <input type="checkbox" id="terms">
                <label for="terms"><span>✔️</span></label>
                <span>
                  Aceptar <span class="underline">términos y condiciones.</span>
                </span>
              </div>
            </div>
            <div class="col-lg-5 col-md-7 col-9 mx-auto">
              <form action="/page/index/changePass" method="post" class="row" id="change">
                <input type="password" class="form-control mb-3" name="password" placeholder="Contraseña" required>
                <input type="password" class="form-control mb-3" name="password2" placeholder="Repite tu contraseña" required>
                <?php if ($this->user && $this->user->id) { ?>
                  <input type="hidden" name="id" value="<?php echo $this->user->id; ?>">
                  <input type="hidden" name="user-exist" value="1">
                <?php } ?>

                <button type="submit">Cambiar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
  header {
    display: none;
  }
</style>