<div class="bx-general bx-login">
  <div class="row mx-0">
    <div class="col-md-6 login-bg">
      <img src="/skins/page/images/login-bg.png?v=1.0" alt="">
    </div>
    <div class="col-md-6">
      <div class="row w-100 text-center justify-content-center">
        <div class="col-lg-9 col-md-10 col-11">
          <div class="row">
            <div class="col-12">
              <img src="/skins/page/images/logo-opain.svg" alt="" class="logo">
            </div>
            <div class="col-12">
              <h3 class="login-title">Bienvenidos</h3>
            </div>
            <div class="col-lg-10 col-md-12 mx-auto">
              <p class="login-par">
                Consulte y descargue sus certificados tributarios.
              </p>
            </div>
            <div class="col-12 mb-5 mt-3">
              <div class="d-flex login-terms justify-content-center align-items-center">
                <input type="checkbox" id="terms">
                <label for="terms"><span>✔️</span></label>
                <span>
                  Aceptar <span class="underline" data-bs-toggle="modal" data-bs-target="#termsModal">términos y
                    condiciones.</span>
                </span>
              </div>
            </div>
            <div class="col-xxl-6 col-lg-8 col-9 mx-auto">
              <form action="/page/index/login" class="row" method="post" id="login">
                <input type="hidden" name="email">
                <input type="text" class="form-control mb-3 no-symbols" name="identificacion" placeholder="NIT" required
                  autocomplete="off">
                <input type="password" class="form-control mb-3" name="password" placeholder="Contraseña"
                  autocomplete="new-password" required>
                <a href="/page/index/olvido" class="mb-2">¿Olvidaste tu contraseña?</a>
                <a href="/page/index/registro" class="mb-4">¿No tienes una cuenta? Regístrate</a>
                <div class="form-group my-2 d-flex justify-content-center">
                  <div class="g-recaptcha" data-sitekey="6LfFDZskAAAAAE2HmM7Z16hOOToYIWZC_31E61Sr"></div>
                </div>
                <input type="hidden" name="_csrf"  value="<?= md5("OMEGA".date("Ymd")) ?>">
                <button type="submit">INGRESAR</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitleId">Términos y condiciones</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          Con la creación del usuario y el acceso al módulo de expedición de certificados
          de RETEICA, RETEFUENTE y RETEIVA de la plataforma de OPAIN
          (www.opain.co) está autorizando de manera voluntaria, previa, explícita,
          informada e inequívoca a OPAIN y a OMEGA para tratar la información
          personal que consigna en el mencionado módulo (identificación, correo
          electrónico, teléfono, etc.), con el fin de crear el usuario en la plataforma,
          custodiar información para dar acceso al módulo, enviar correos de
          notificaciones relacionadas con su uso y habilitar la descarga de los certificados.
          El período de tratamiento de la información será desde la autorización hasta la
          finalización del Contrato de Concesión No. 6000169OK de 2006, cuya consulta
          es de público acceso a través del link:
          <a target="_blank" rel="noopener noreferrer" href="https://www.contratos.gov.co/consultas/detalleProceso.do?numConstancia=05-1-
            1033">https://www.contratos.gov.co/consultas/detalleProceso.do?numConstancia=05-1-
            1033</a>
          Desde OPAIN le recordamos que de conformidad con la ley, los derechos del
          titular de los datos son los siguientes: a) Conocer, actualizar y rectificar sus datos
          personales frente a los Responsables o Encargados del Tratamiento. Este derecho
          se podrá ejercer, entre otros frente a datos parciales, inexactos, incompletos,
          fraccionados, que induzcan a error, o aquellos cuyo Tratamiento esté
          expresamente prohibido o no haya sido autorizado; b) Solicitar prueba de la
          autorización otorgada al Responsable del Tratamiento salvo cuando
          expresamente se exceptúe como requisito para el Tratamiento de conformidad
          con lo previsto en el artículo 10 de la Ley 1581 de 2012; c) Ser informado por el
          Responsable del Tratamiento o el Encargado del Tratamiento, previa solicitud,
          respecto del uso que le ha dado a sus datos personales; d) Presentar ante la
          Superintendencia de Industria y Comercio quejas por infracciones a lo dispuesto
          en la citada ley y las demás normas que la modifiquen, adicionen o
          complementen; e) Revocar la autorización y/o solicitar la supresión del dato
          cuando en el Tratamiento no se respeten los principios, derechos y garantías
          constitucionales y legales. La revocatoria y/o supresión procederá cuando la
          Superintendencia de Industria y Comercio haya determinado que en el
          Tratamiento el responsable o Encargado han incurrido en conductas contrarias a
          la ley y/o a la Constitución; f) Acceder en forma gratuita a sus datos personales
          que hayan sido objeto de Tratamiento.
          Para leer nuestra Política de Tratamiento de Datos ingrese a:

          <a target="_blank" rel="noopener noreferrer"
            href="https://www.opain.co/files/membretepoliticastratamientodatos.pdf">https://www.opain.co/files/membretepoliticastratamientodatos.pdf</a>
          y en caso de
          quejas o peticiones relacionados con el tratamiento de sus datos por favor
          contactarse al correo electrónico habeasdataoficial@eldorado.aero
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de consentimiento de cookies -->
<?php if (!isset($_COOKIE['consentimiento']) || $_COOKIE['consentimiento'] !== 'aceptado'): ?>
  <div class="modal fade show" id="cookieModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    style="display: block;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content" style="border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <div class="modal-header bg-light">
          <h5 class="modal-title text-center w-100" style="font-weight: bold; color: #333;">
            <i class="fas fa-cookie-bite me-2" style="color: #ff6b35;"></i>Aviso de Cookies
          </h5>
        </div>
        <div class="modal-body text-center">
          <div class="mb-4">
            <i class="fas fa-cookie-bite fa-4x" style="color: #ff6b35; opacity: 0.8;"></i>
          </div>
          <p style="font-size: 16px; line-height: 1.6; color: #555; max-width: 500px; margin: 0 auto;">
            <strong>Este sitio utiliza cookies de sesión</strong> para mejorar tu experiencia de navegación.
            Al aceptar, permites el uso de cookies necesarias para el funcionamiento del sitio.
          </p>
          <p style="font-size: 14px; color: #777; margin-top: 15px;">
            Puedes leer más sobre nuestra política de privacidad en nuestros términos y condiciones.
          </p>
        </div>
        <div class="modal-footer justify-content-center">
          <button onclick="aceptarCookies()" class="btn btn-primary btn-lg" style="    background-color: var(--yellow);
    color: var(--blue);
    font-size: 1.05rem;
    font-weight: 700;
    padding: 5px 20px;
    border: none;
    border-radius: 15px;
    cursor: pointer;
    transition: 300ms 
ease-in-out;">
            Aceptar Cookies
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function aceptarCookies () {
      // Establecer cookie con fecha de expiración de 1 año
      var fechaExpiracion = new Date();
      fechaExpiracion.setFullYear(fechaExpiracion.getFullYear() + 1);
      document.cookie = "consentimiento=aceptado; expires=" + fechaExpiracion.toUTCString() + "; path=/; SameSite=Lax";

      // Ocultar el modal
      var modal = bootstrap.Modal.getInstance(document.getElementById('cookieModal'));
      if (modal) {
        modal.hide();
      } else {
        document.getElementById('cookieModal').style.display = 'none';
      }

      // Debug: Mostrar en consola si la cookie existe
      console.log('Cookie consentimiento:', document.cookie.indexOf('consentimiento=aceptado') !== -1 ? 'Existe' : 'No existe');
    }

    // Mostrar el modal automáticamente si no hay cookie
    <?php if (!isset($_COOKIE['consentimiento']) || $_COOKIE['consentimiento'] !== 'aceptado'): ?>
      document.addEventListener('DOMContentLoaded', function () {
        var modal = new bootstrap.Modal(document.getElementById('cookieModal'), {
          backdrop: 'static',
          keyboard: false
        });
        modal.show();
      });
    <?php endif; ?>
  </script>
<?php endif; ?>

<style>
  header {
    display: none;
  }
</style>