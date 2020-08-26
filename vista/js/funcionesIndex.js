$(function () {

    var TallerIndex = {};
    var leBombe = true;
    (function (app) {

        app.init = function () {
            $('#user').focus();
            $("#submit").on('click', function (event) {
                event.preventDefault();
                app.encriptar();
            });
            /*$("#user").on('click', function(event) {
             $("#user").css("background-color","white").val('');
             $("#pass").css("background-color","white").val('');
             event.preventDefault();
             });
             $("#pass").on('click', function(event) {
             $("#user").css("background-color","white").val('');
             $("#pass").css("background-color","white").val('');
             event.preventDefault();
             });*/
        };


        app.encriptar = function () {
            var usuario = btoa(btoa($('#user').val()));
            var pass = btoa(btoa($('#pass').val()));
            //var cons = $("#comboConsultorio").val();
            app.enviarAServidor(usuario, pass);
        };

        app.enviarAServidor = function (usuario, pass) {
            var url = "controlador/ruteador/Seguridad.php";
            var datosEnviar = {user: usuario, pass: pass, tabla: usuario};
            $.ajax({
                url: url,
                method: 'POST',
                data: datosEnviar,
                dataType: 'json',
                success: function (datosDevueltos) {
                    app.rellenardiv(datosDevueltos);
                },
                error: function () {
                    alert("error al enviar al servidor");
                },
                beforeSend: function ()//esta función se realiza antes de enviar los datos al servidor cumple solo la función de mostrar un spinner
                {
                    $("#message").html("<p class='text-center'><img src='vista/images/ajax-loader.gif'></p>")//utilizo el mismo div que uso para marcar
                    //el mensaje de error para mostrar el spiner
                }
            });
        };

        app.rellenardiv = function (datosDevueltos) {
            var html = "";
            if (typeof datosDevueltos.cambiarClave != 'undefined') {
                document.location.href = "vista/html/cambiarClave.html";
            } else if (typeof datosDevueltos.usuario_usuario != 'undefined') {
                document.location.href = "admin.html";
            } else {
                html += "<div class='alert alert-danger' role='alert'><p>" + "USUARIO Y/O CONTRASEÑA INVALIDOS" + "</p></div>";
                $("#loginError").html(html);
                $("#user").css("background-color", "red");
                $("#pass").css("background-color", "red");
            }
        };
        app.init();

    })(TallerIndex);


});
       
