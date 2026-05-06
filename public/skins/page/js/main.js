// ==============================================================================================
$(document).ajaxStart(function () {
  $(".loader-bx").addClass("show");
});
$(document).ajaxStop(function () {
  $(".loader-bx").removeClass("show");
});
$(document).ready(function () {
  //Recuperación de contraseña
  $("#olvido").on("submit", function (e) {
    e.preventDefault();
    if ($("#terms").is(":checked") == false) {
      alert("Debe aceptar los terminos y condiciones para poder continuar.");
      return false;
    }
    let data = $(this).serialize();
    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: data,
      dataType: "json",
      success: function (res) {
        if (res.status == "success") {
          Swal.fire({
            icon: "success",
            text:
              "Se enviara un correo para su recuperación de contraseña a la dirección: " +
              res.email +
              " verifique su bandeja de entrada.",
          }).then((result) => {
            window.location.href = "/";
          });
        } else if (res.status == "error") {
          Swal.fire({
            icon: "error",
            text: res.message,
          }).then((result) => {
            window.location.href = "/";
          });
        }
      },
    });
  });

  // Inicio cambio de contraseña
  $("#change").on("submit", function (e) {
    e.preventDefault();
    if ($("#terms").is(":checked") == false) {
      alert("Debe aceptar los terminos y condiciones para poder continuar.");
      return false;
    }
    let data = $(this).serialize();
    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.status == "success") {
          Swal.fire({
            icon: "success",
            title: "Cambio de contraseña exitoso",
            confirmButtonText: "Continuar",
            confirmButtonColor: "#1c3455",
            text: response.message,
          }).then((result) => {
            window.location.href = "/page/index/certificados";
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
          });
        }
      },
    });
  });
  // Fin cambio de contraseña

  // Inicio formulario de login
  $("#login").on("submit", function (e) {
    e.preventDefault();
    if ($("#terms").is(":checked") == false) {
      alert("Debe aceptar los terminos y condiciones para poder continuar.");
      return false;
    }
    var recaptchaResponse = grecaptcha.getResponse();

    if (recaptchaResponse.length == 0) {
      alert("Por favor, completa el reCAPTCHA");
      return false;
    }
    let data = $(this).serialize();

    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: data,
      dataType: "json",
      success: function (response) {
        console.log(response);
        if (response.status == "success") {
          window.location.href = "/page/index/certificados";
        } else if (response.refresh == true) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "El usuario no se encuentra registrado, por favor intente nuevamente.",
            confirmButtonText: "Continuar",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            window.location.href = "/";
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
            confirmButtonText: "Continuar",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            window.location.href = "/";
          });
        }
      },
    });
  });
  // Fin formulario de login
  $("#correo").on("submit", function (e) {
    e.preventDefault();
    let data = $(this).serialize();
    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.status == "error") {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ha ocurrido un error, por favor intente nuevamente.",
          });
        } else {
          Swal.fire({
            icon: "success",
            title: "Solicitud exitosa",
            text: "Se solicitud de cambio de correo ha sido enviada correctamente, se revisara la información para su aprobación, recibirá una notificación al nuevo correo si es aprobada.",
            confirmButtonText: "Continuar",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            window.location.href = "/";
          });
        }
      },
    });
  });
  $("#solicitud").on("submit", function (e) {
    e.preventDefault();

    let data = $(this).serialize();
    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.status == "error") {
          Swal.fire({
            icon: "error",
            title: "Error",
            text:
              response.message ??
              "Ha ocurrido un error, por favor intente nuevamente.",
          });
        } else if (response.status == "user_exist") {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
            confirmButtonText: "Continuar",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "/";
            }
          });
        } else if (response.status == "solicitud_exist") {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
            confirmButtonText: "Continuar",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "/";
            }
          });
        } else {
          Swal.fire({
            icon: "success",
            title: "Solicitud exitosa",
            text: "Se solicitud de registro ha sido recibida correctamente, recibirá un correo con una contraseña temporal.",
            confirmButtonText: "Continuar",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            window.location.href = "/";
          });
        }
      },
    });
  });

  // Inicio formulario de registro
  $("#registro").on("submit", function (e) {
    e.preventDefault();
    if ($("#terms").is(":checked") == false) {
      alert("Debe aceptar los terminos y condiciones para poder continuar.");
      return false;
    }
    let data = $(this).serialize();
    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: data,
      dataType: "json",
      success: function (response) {
        console.log(response);
        // return;
        if (response.status == "error") {
          Swal.fire({
            icon: response.icon,
            title: response.title,
            text: response.message,
            showCancelButton: true,
            confirmButtonText: "Continuar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#1c3455",
          });
        } else {
          Swal.fire({
            icon: response.icon,
            title: response.title,
            text: response.message,
            showCancelButton: true,
            confirmButtonText: "Continuar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            if (result.isConfirmed) {
              if (response.redirect) {
                window.location.href = response.redirect;
              }
            }
          });
        }
      },
    });
  });
  // Fin formulario de registro

  $(".btn-ica").on("click", function (e) {
    e.preventDefault();
    let year = $(`#select-ica`).val();
    let token = $(`input[name="csrf_token"]`).val();
    $.ajax({
      url: "/page/index/buscarICA",
      type: "POST",
      data: {
        year: year,
        csrf_token: token,
      },
      dataType: "json",
      success: function (response) {
        $(`#docs_ica`).html(response.html);
        // console.log(response.html);
      },
    });
  });
  $(".btn-iva").on("click", function (e) {
    e.preventDefault();
    let year = $(`#select-iva`).val();
    let token = $(`input[name="csrf_token"]`).val();
    $.ajax({
      url: "/page/index/buscarIVA",
      type: "POST",
      data: {
        year: year,
        csrf_token: token,
      },
      dataType: "json",
      success: function (response) {
        $(`#docs_iva`).html(response.html);
        // console.log(response.html);
      },
    });
  });
  $(".btn-fuente").on("click", function (e) {
    e.preventDefault();
    let year = $(`#select-fuente`).val();
    let token = $(`input[name="csrf_token"]`).val();
    $.ajax({
      url: "/page/index/buscarFuente",
      type: "POST",
      data: {
        year: year,
        csrf_token: token,
      },
      dataType: "json",
      success: function (response) {
        $(`#docs_fuente`).html(response.html);
        // console.log(response.html);
      },
    });
  });

  $(".btn-proveedores").on("click", function (e) {
    e.preventDefault();
    let year = $(`#select-proveedores`).val();
    let token = $(`input[name="csrf_token"]`).val();
    $.ajax({
      url: "/page/index/buscarProveedores",
      type: "POST",
      data: {
        year: year,
        csrf_token: token,
      },
      dataType: "json",
      success: function (response) {
        $(`#docs_proveedores`).html(response.html);
        // console.log(response.html);
      },
    });
  });

  $("#correo-recuperacion").on("submit", function (e) {
    e.preventDefault();
    if ($("#terms").is(":checked") == false) {
      alert("Debe aceptar los terminos y condiciones para poder continuar.");
      return false;
    }

    let data = $(this).serialize();
    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.status == "error") {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
          });
        } else if (response.status == "user_non_exist") {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
            confirmButtonText: "Registrese aquí",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "/page/index/registro";
            }
          });
        } else {
          Swal.fire({
            icon: "success",
            title: "Solicitud exitosa",
            text: "Se solicitud de registro ha sido enviada correctamente, se revisara la información para su aprobación, recibirá un correo si es aprobada.",
            confirmButtonText: "Continuar",
            confirmButtonColor: "#1c3455",
          }).then((result) => {
            window.location.href = "/";
          });
        }
      },
    });
  });
});

// ==============================================================================================
var videos = [];
$(document).ready(function () {
  $(".dropdown-toggle").dropdown();
  $(".carouselsection").carousel({
    quantity: 4,
    sizes: {
      900: 3,
      500: 1,
    },
  });

  $(".banner-video-youtube").each(function () {
    // console.log($(this).attr('data-video'));
    const datavideo = $(this).attr("data-video");
    const idvideo = $(this).attr("id");
    const playerDefaults = {
      autoplay: 0,
      autohide: 1,
      modestbranding: 0,
      rel: 0,
      showinfo: 0,
      controls: 0,
      disablekb: 1,
      enablejsapi: 0,
      iv_load_policy: 3,
    };
    const video = {
      videoId: datavideo,
      suggestedQuality: "hd1080",
    };
    videos[videos.length] = new YT.Player(idvideo, {
      videoId: datavideo,
      playerVars: playerDefaults,
      events: {
        onReady: onAutoPlay,
        onStateChange: onFinish,
      },
    });
  });

  function onAutoPlay(event) {
    event.target.playVideo();
    event.target.mute();
  }

  function onFinish(event) {
    if (event.data === 0) {
      event.target.playVideo();
    }
  }
  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
  );
  const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
  );
});

document.addEventListener("DOMContentLoaded", () => {
  // Expresión regular para eliminar caracteres peligrosos
  const sanitizeInput = (input) => {
    return input.replace(/[<>'`{}()[\]\\\/]/g, ""); // Quita los caracteres prohibidos
  };

  document.body.addEventListener("input", (event) => {
    const field = event.target;
    if (field.tagName !== "INPUT" && field.tagName !== "TEXTAREA") return;

    const cleanValue = sanitizeInput(field.value);
    if (field.value !== cleanValue) {
      field.value = cleanValue; // Reemplaza el texto con la versión limpia
    } else {
      field.title = "";
    }
  });

  document.body.addEventListener("submit", (event) => {
    if (event.target.classList.contains("secureForm")) {
      event.preventDefault();
      let isValid = true;

      const inputs = event.target.querySelectorAll("input, textarea");
      inputs.forEach((field) => {
        const cleanValue = sanitizeInput(field.value);
        if (field.value !== cleanValue) {
          field.value = cleanValue; // Limpia el input antes de enviar

          isValid = false;
        }
      });

      if (isValid) {
        console.log("Formulario enviado correctamente.");
        event.target.submit();
      }
    }
  });
});
document.addEventListener("DOMContentLoaded", () => {
  // Expresión regular para eliminar caracteres peligrosos
  const sanitizeInput = (input) => {
    return input.replace(/[<>'`{}()[\]\\\/]/g, ""); // Quita los caracteres prohibidos
  };

  document.body.addEventListener("input", (event) => {
    const field = event.target;
    if (field.tagName !== "INPUT" && field.tagName !== "TEXTAREA") return;

    const cleanValue = sanitizeInput(field.value);
    if (field.value !== cleanValue) {
      field.value = cleanValue; // Reemplaza el texto con la versión limpia
    } else {
      field.title = "";
    }
  });

  document.body.addEventListener("submit", (event) => {
    if (event.target.classList.contains("secureForm")) {
      event.preventDefault();
      let isValid = true;

      const inputs = event.target.querySelectorAll("input, textarea");
      inputs.forEach((field) => {
        const cleanValue = sanitizeInput(field.value);
        if (field.value !== cleanValue) {
          field.value = cleanValue; // Limpia el input antes de enviar

          isValid = false;
        }
      });

      if (isValid) {
        console.log("Formulario enviado correctamente.");
        event.target.submit();
      }
    }
  });
});
document.addEventListener("DOMContentLoaded", () => {
  const inputs = document.querySelectorAll("input.no-symbols");

  inputs.forEach((input) => {
    input.addEventListener("input", (event) => {
      event.target.value = event.target.value.replace(/[.,-]/g, "");
      event.target.value = event.target.value.replace(/[^0-9]/g, "");
    });
  });
});
/* retert */
