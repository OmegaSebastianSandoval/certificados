<div class="bx-general bx-login">
  <div class="row mx-0">
    <div class="col-md-6 login-bg">
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
              <h3 class="login-title">Regístrate</h3>
            </div>
            <div class="col-lg-10 col-md-12 mx-auto">
              <p class="login-par">
                <br>
                <br>
                Consulte y descargue sus certificados tributarios.
                <br><br>
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
            <div class="col-lg-8 col-md-9 col-10 mx-auto">
              <form action="/page/index/registrar" method="post" class="row" id="registro">
                <div class="alert alert-info py-2 mb-2 text-start" role="alert" style="font-size: 0.85rem;">
                  <i class="fas fa-info-circle me-1"></i> Ingrese el NIT <strong>sin</strong> dígito de verificación.
                </div>
                <input type="text" class="form-control mb-3 no-symbols" name="identificacion" placeholder="NIT" required
                  autocomplete="off">
                <!-- <input type="email" class="form-control mb-3 no-symbols" name="email" placeholder="Email" required autocomplete="off" > -->
                <a href="/" class="mb-4">¿Ya tienes cuenta? Inicia sesión.</a>
                <a href="/page/index/correo" class="mb-4">¿No recibes las notificaciones? Cambia tu correo.</a>
                <button type="submit">REGISTRAR</button>
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
          <a target="_blank" href="https://www.contratos.gov.co/consultas/detalleProceso.do?numConstancia=05-1-
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

<style>
  header {
    display: none;
  }
</style>