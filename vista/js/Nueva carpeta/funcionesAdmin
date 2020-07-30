$(function () {

    var Taller = {};
    var idUsuario = "";
    var leBombe = false;
    (function (app) {

        app.init = function () {
            app.verificarSesion();
        };
        app.bindings = function () {

            $("#cerrarSesion").on('click', function (event) {
                app.cerrarSesion(event);
            });
            $("#salir").on('click', function (event) {
                app.cerrarSesion(event);
            });
        };

        app.cerrarSesion = function (event) {
            event.preventDefault();
            bootbox.confirm({
                size: 'medium',
                message: 'Esta seguro que desea finalizar sus sesion de trabajo?',
                callback: function (result) {
                    if (result) {
                        var url = "controlador/ruteador/CerrarSesion.php";
                        var datosEnviar = {usuario: idUsuario};
                        $.ajax({
                            url: url,
                            cache: false,
                            method: 'POST',
                            data: datosEnviar,
                            dataType: 'json',
                            success: function (datosDevueltos) {
                                document.location.href = "index.html";

                            },
                            error: function () {
                                alert("error al enviar al servidor");
                            }
                        });

                    }
                }
            });
        };

       
        app.verificarSesion = function () {
            var url = "controlador/ruteador/Sesion.php";
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                success: function (datos) {
                    if (typeof datos['id_usuario'] != 'undefined') {
                        if (leBombe) {
                            var f = new Date();
                            var hoy = (f.getDate() + "/" + (f.getMonth() + 1) + "/" + f.getFullYear());
                            var limite = new Date(2020, 1, 01);
                            f.setHours(0, 0, 0, 0);
                            limite.setHours(0, 0, 0, 0);
                            if (f.getTime() >= limite.getTime()) {
                                alert("Ha concluido el período de prueba. Por favor comuníquese con el desarrrolador.");
                                location.href = "index.html";
                            } else {
                                idUsuario = datos.id_usuario;
                                $("#id_user").val(datos.id_usuario);
                                $("#id_consultorio").html(datos.id_consultorio);
                                $("#logedUser").html(datos.usuario_usuario);
                                $("#logedCons").html(datos.descripcion_consultorio);
                                var acceso = datos.tipoAcceso_usuario;
                                console.log(acceso);
                                app.bindings();
                                app.cargarBotones(acceso);
                            }
                        } else {
                            idUsuario = datos.id_usuario;
                            $("#id_user").val(datos.id_usuario);
                            $("#id_consultorio").html(datos.id_consultorio);
                            $("#logedUser").html(datos.usuario_usuario);
                            $("#logedCons").html(datos.descripcion_consultorio);
                            var acceso = datos.tipoAcceso_usuario;
                            console.log(acceso);
                            app.bindings();
                            app.cargarBotones(acceso);
                        }
                    } else {
                        location.href = "index.html";
                    }
                },
                error: function (data) {
                    location.href = "index.html";
                }
            });
        };
        
        app.cargarBotones = function (acceso) {
            if (acceso === '1') {
                $('#boton').html('<div class="btn-group">' +
                        '<a href="vista/html/usuario.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">CARGA DE USUARIO</button></a>' +
                        '<a href="vista/html/tipoServicio.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">TIPO DE SERVICIO</button></a>' +
                        '<a href="vista/html/servicios.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">CARGAR SERVICIOS</button></a>' +
                        '</div>');

            } else if (acceso === '2') {
                $('#boton').html('<div class="btn-group">' +
                        '<a href="vista/html/tipoServicio.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">TIPO DE SERVICIO</button></a>' +
                        '<a href="vista/html/servicios.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">CARGAR SERVICIOS</button></a>' +
                        '</div>');
            }else{
                $('#boton').html('<div class="btn-group">' +
                        '<a href="vista/html/servicios.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">CARGAR SERVICIOS</button></a>' +
                        '</div>');
                
            }
        };
        
        app.init();

    })(Taller);


});
