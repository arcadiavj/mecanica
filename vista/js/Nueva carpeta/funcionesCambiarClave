$(function() {

    var cambiarClave = {};
    var idUsuario = "";
    (function(app) {
        
        app.init = function() {
            app.verificarSesion();
        };
        app.bindings = function(){
            $("#guardar").on('click', function(event){
               event.preventDefault();
               app.validarCampos(); 
            });
            $("#cerrarSesion").on('click', function (event) {
                var msge = "Esta seguro que desea finalizar sus sesion de trabajo?";
                app.cerrarSesion(event, msge);
            });
            $("#salir").on('click', function (event) {
                var msge = "Esta seguro que desea finalizar sus sesion de trabajo?";
                app.cerrarSesion(event, msge);
            });
        };
        
        app.cerrarSesion = function(msge){
                bootbox.confirm({
                    size: 'medium',
                    message: msge,
                    callback: function (result) {
                        if (result) {
                            var url = "../../controlador/ruteador/CerrarSesion.php";
                            var datosEnviar = {usuario: idUsuario};
                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: datosEnviar,
                                dataType: 'json',
                                success: function (datosDevueltos) {
                                    document.location.href = "../../index.html";
                                    
                                },
                                error: function () {
                                    alert("error al enviar al servidor");
                                }
                            });

                        }
                    }
                });  
        };
        
        app.validarCampos = function(){
            //user, passAnteior, nuevoPass1, nuevoPass2
            var user = $("#user").val();
            var passAnt = $("#passAnterior").val();
            var nuevoPass1 = $("#nuevoPass1").val();
            var nuevoPass2 = $("#nuevoPass2").val();
            if (user == null || user == "") {
                bootbox.alert("Debes completar el campo Usuario");
            }else if(passAnt == null || passAnt == ""){
                bootbox.alert("Debes completar el campo Clave Anterior");
            }else if(nuevoPass1 == null || nuevoPass1 == ""){
                bootbox.alert("Debes completar el campo Nueva Clave");
            }else if(nuevoPass2 == null || nuevoPass2 == ""){
                bootbox.alert("Debes completar el campo Repetir de Nueva Clave");
            }else if(nuevoPass1 != nuevoPass2){
                bootbox.alert("La nueva clave y su confirmación no concuerdan.");
            }else{
                app.cambiarClave();
            }
        };
        app.cambiarClave = function(){
            var idUser = $("#id_user").val();
            var nuevoPass = btoa(btoa($("#nuevoPass1").val()));
            var url = "../../controlador/ruteador/Seguridad.php"; 
            var datosEnviar = {accion:"cambiarClave",id_usuario:idUser, nuevoPass:nuevoPass};
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                data: datosEnviar,
                success: function(datosDevueltos) {
                    console.log(datosDevueltos);
                    if(typeof datosDevueltos.cambio != 'undefined'){
                        var msge = "La contraseña se cambió correctamente.<br>La sesión se cerrará para que ingreses con tu nueva contraseña.";
                        app.cerrarSesion(msge);
                    }
                },
                error: function(datosDevueltos) {
                    alert("Error en el ajax de cambiarClave: " + datosDevueltos);
                }
            });
        };
        app.verificarSesion = function () {
            var url = "../../controlador/ruteador/Sesion.php"; 
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                success: function(datos) {
                    if (typeof datos['id_usuario'] != 'undefined') {
                        $("#id_user").val(datos.id_usuario);
                        $("#logedUser").html(datos.usuario_usuario);
                        $("#user").val(datos.usuario_usuario);
                        $("#id_consultorio").val(datos.id_consultorio);//
                        $("#logedCons").html(datos.descripcion_consultorio);
                        var acceso = datos.tipoAcceso_usuario;
                        idUsuario=datos.id_usuario;
                        app.bindings();
                    }else{
                        location.href = "../../index.html";
                    }
                },
                error: function(data) {
                    location.href = "../../index.html";
                } 
            }); 
        };
        app.encriptar = function(){
            var usuario = btoa(btoa($('#user').val()));
            var pass = btoa(btoa($('#pass').val()));
            app.enviarAServidor(usuario, pass);
        };
        app.init();

    })(cambiarClave);


});



