$(function () {

    var TipoServicio = {};
    var idUsuario = "";
    (function (app) {

        app.init = function () {
            $("#reporDetalle").hide();
            app.verificarSesion();
        };

        app.bindings = function () {
            // cerrar session Adherido en cambio final
            $("#cerrarSesion").on('click', function (event) {
                alert(idUsuario);
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

        app.verificarSesion = function () {
            var url = "../../controlador/ruteador/Sesion.php";
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                success: function (datos) {
                    if (typeof datos['id_usuario'] != 'undefined' && typeof datos['tipoAcceso_usuario'] != 'undefined') {
                        $("#id_user").val(datos.id_usuario);
                        var tA = datos.tipoAcceso_usuario;
                        idUsuario = datos.id_usuario;
                        if (parseInt(tA) === 1 || parseInt(tA) === 2 ) {
                            $("#logedUser").html(datos.usuario_usuario);
                            var acceso = datos.tipoAcceso_usuario;
                            app.buscarTiposervicio();
                            app.bindings();
                            app.cargarBotones(acceso);
                        } else {
                            location.href = "../../admin.html";
                        }
                    } else {
                        location.href = "../../index.html";
                    }
                },
                error: function (data) {
                    location.href = "../../index.html";
                }
            });
        };
        app.bindings = function () {
            $('#txtModificacion').attr('disabled', true);
            $('#txtCreacion').attr('disabled', true);
            $("#agregar").on('click', function (event) {
                event.preventDefault();
//                app.borrarCampos();
                app.activarControles();
                $('#fCrea').hide();
                $('#fModi').hide();
                $("#id").val(0);
                $("#mHeader").removeClass();
                $("#mHeader").attr("class", "modal-header bg-primary");
                $("#tituloModal").html("Nuevo Tipo de Servicio");//Cambio el título del Modal
                $("#modalTiposervicio").modal({show: true, backdrop: 'static', keyboard: false});//lo muestro
                $("#accion").attr("value", "guardar");//Cambio el nombre del boton
                $("#guardar").html("Agregar");
                $("#guardar").show();//muestro el boton guardar
                $("#reporDetalle").hide();//
            });
            $('#modalTiposervicio').on('shown.bs.modal', function () {
                $('#nombreTiposervicio').focus();
            });

            $("#cuerpoTiposervicio").on('click', '.editar', function (event) {
                event.preventDefault();
                console.log("entraste a editar");
                $("#id").val($(this).attr("data-id_tiposervicio"));

                $('#fCrea').show();
                $('#fModi').show();

                $("#mHeader").removeClass();
                $("#mHeader").attr("class", "modal-header bg-success");
                $("#nombreTipo").val($(this).parent().parent().children().first().next().html());
                $("#descripcionTipo").val($(this).parent().parent().children().first().next().next().html());
                $('#txtCreacion').attr("disabled", true);
                $('#txtCreacion').val($(this).parent().parent().children().first().next().next().next().html());
                $('#txtModificacion').val($(this).parent().parent().children().first().next().next().next().next().html());

                app.activarControles();
                $("#guardar").html("Modificar");
                $("#accion").attr("value", "modificar");
                $("#tituloModal").html("Editar Tipo De Servicios");
                $("#modalTiposervicio").modal({show: true, backdrop: 'static', keyboard: false});
                $("#guardar").show();
                $("#reporDetalle").hide();

            });

            $("#cuerpoTiposervicio").on('click', '.seleccionar', function (event) {
                event.preventDefault();
                $("#id").val($(this).attr("data-id_tiposervicio"));
                $("#mHeader").removeClass();
                $("#mHeader").attr("class", "modal-header bg-info");
                $('#fCrea').show();
                $('#fModi').show();


                $("#nombreTipo").val($(this).parent().parent().children().first().next().html());
                $("#descripcionTipo").val($(this).parent().parent().children().first().next().next().html());
                $('#txtCreacion').val($(this).parent().parent().children().first().next().next().next().html());
                $('#txtModificacion').val($(this).parent().parent().children().first().next().next().next().next().html());
                app.desactivarControles();
                $("#guardar").hide();
                $("#reporDetalle").hide();
                $("#guardar").html("Modificar");
                $("#guardar").attr("value", "Modificar");
                $("#tituloModal").html("Detalles Tipo Servicio");
                $("#modalTiposervicio").modal({show: true, backdrop: 'static', keyboard: false});
            });

            $("#cuerpoTiposervicio").on('click', '.eliminar', function () {
                app.eliminarUsuario($(this).attr("data-id_tiposervicio"));
            });

            $("#cancelar").on("click", function (event) {
                event.preventDefault();
                app.borrarCampos();
                $("#modalTiposervicio").modal('hide');
            });
            
            $("#x").on("click", function (event) {
                event.preventDefault();
                app.borrarCampos();
                $("#modalUsuario").modal('hide');
            });

            $("#guardar").on("click", function (event) {
                event.preventDefault();
                app.validarCampos();
            });

            $("#formTipo").bootstrapValidator({
                excluded: []
            });

            $("#cerrarSesion").on('click', function (event) {
                app.cerrarSesion(event);

            });
            $("#salir").on('click', function (event) {
                app.cerrarSesion(event);
            });
        };
        app.validarCampos = function () {
            var nom = $('#nombreTipo').val();
            var ape = $('#descripcionTipo').val();
            if (nom == null || nom == "") {
                bootbox.alert("Debes completar el campo Nombre");
            } else if (ape == null || ape == "") {
                bootbox.alert("Debes completar el campo Descripcion");
            }else {
                app.guardarTipo();
            }
        };

        app.desactivarControles = function () {
            $('#nombreUsuario').attr('disabled', true);
            $('#apellidoUsuario').attr('disabled', true);
            $('#usuario').attr('disabled', true);
            $('#pass').attr('disabled', true);
            $('#rPass').attr('disabled', true);
            $('#accesoRestringido').attr('disabled', true);
            $('#accesoTotal').attr('disabled', true);
            $('#accesoAdministrativo').attr('disabled', true);
            $('#txtCreacion').attr('disabled', true);
            $('#txtModificacion').attr('disabled', true);
        };
        app.activarControles = function () {
            $('#nombreTipo').removeAttr('disabled');
            $('#descripcionTipo').removeAttr('disabled');
        };
        app.borrarCampos = function () {
            $("#mensaje").val("").html();
            $('#nombreTipo').val("").html();
            $('#descripcionTipo').val("").html();
            $('#txtCreacion').val("").html();
            $('#txtModificacion').val("").html();
            $("#modalTiposervicio").bootstrapValidator('resetForm', true);
            $("#mensaje").val("").html();
            $("#mensaje").hide();
        };

        app.guardarTipo = function () {
            var url = "../../controlador/ruteador/Ruteador.php"; //voy al ruteador a guardar Usuario (tanto para modific como para agregar)
            //data del formulario persona
            $('#pass').removeAttr("disabled");
            $('#txtCreacion').removeAttr('disabled');
            var data = $("#formTipo").serialize();//convierto los datos del alumno en un array para enviarlos al ruteados
            $.ajax({
                url: url, //paso la url
                method: 'POST', //método que utilizo
                dataType: 'json', //tipo de datos
                data: data, //el formulario del usuario que estoy pasando
                success: function (datos) {//si todo ó bien 
                    if (typeof datos['incorrecto'] != 'undefined') {
                        $("#mensaje").show();
                        $("#mensaje").html("El nombre de usuario ya existe, por favor intente con otro.");//CARTEL AL TIPO DE YA EXISTE USER

                    } else {
                        $("#modalTiposervicio").modal('hide');//oculto el modal
                        app.borrarCampos();
                        app.actualizarDataTable(datos, $("#id").val());
                        app.buscarTiposervicio();
                    }
                },
                error: function (data) {//si hay un error lo muestro por pantalla
                    alert(data);//mensaje de error
                },
                beforeSend: function ()//esta función se realiza antes de enviar los datos al servidor cumple solo la función de mostrar un spinner
                {
                    var dialog = bootbox.dialog({
                        message: "<p class='text-center'><img src='../../vista/images/ajax-loader.gif'></p>",
                        closeButton: false
                    });
                    dialog.modal('hide');
                }
            });
        };

        app.eliminarUsuario = function (id) {

            bootbox.confirm({
                size: 'medium',
                message: "Esta Seguro que desea Eliminar el Tipo de Servicio?",
                callback: function (result) {
                    if (result) {
                        var url = "../../controlador/ruteador/Ruteador.php?accion=eliminar&nombreFormulario=Tiposervicio&id=" + id; //cambiar url
                        $.ajax({
                            url: url,
                            method: "GET",
                            dataType: 'json',
                            success: function (data) {
                                app.borrarFilaDataTable(id);
                            },
                            error: function (data) {
                                alert('error');
                            }
                        });
                    }
                }
            });
        };


        app.borrarFilaDataTable = function (id) {
            var fila = $("#cuerpoTiposervicio").find("a[data-id_tiposervicio='" + id + "']").parent().parent().remove();

        };

        app.buscarTiposervicio = function () {//función que se encarga de realizar la busqueda de los usuarios
            var url = "../../controlador/ruteador/Ruteador.php?accion=buscar&nombreFormulario=Tiposervicio";//paso la dirección del 
            //ruteador para obtener los datos de la BD
            $.ajax({//ajax para realizar la petición de los datos
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function (data) {//si todo sale bien llamo a la funcion correspondiente
                    app.rellenarDataTable(data);//esta es la función encargada de rellenar la tabla conlos datos de los usuarios del sistema
                },
                error: function () {//si algo sale mal muestra un mensaje de error
                    alert('error');
                },
                beforeSend: function ()//esta función se realiza antes de enviar los datos al servidor cumple solo la función de mostrar un spinner
                {
                    var dialog = bootbox.dialog({
                        message:"<p class='text-center'><img src='../../vista/images/ajax-loader.gif'></p>",
                        closeButton: false
                    });
                    dialog.modal('hide');
                }

            });
        };


        app.rellenarDataTable = function (data) {//función para rellenar la tabla
            var html = "";//variable que voy a utilizar para rellenar la tabla
            if ($.fn.DataTable.isDataTable('#tablaTiposervicio')) {
                $('#tablaTiposervicio').DataTable().destroy();
            }
            $.each(data, function (clave, tipo) {//recorro todos los datos devueltos con el JSon
                html += '<tr class="text-warning">' +
                        '<td><a class="seleccionar" data-id_tiposervicio="' + tipo.id_tiposervicio + '"><button class="btn btn-info btn-sm">' +
                        '<span class="glyphicon glyphicon-eye-open left">  Info</span></button></a></td>' +
                        '<td>' + tipo.nombre_tiposervicio + '</td>' +
                        '<td>' + tipo.descripcion_tiposervicio + '</td>' + 
                        '<td>' + tipo.fch_creacion + '</td>' +
                        '<td>' + tipo.fch_modificacion + '</td>' +
                        '<td>' +
                        '<a class="pull-left editar" data-id_tiposervicio="' + tipo.id_tiposervicio + '"><button class="btn btn-success btn-sm">' +
                        '<span class="glyphicon glyphicon-pencil"> Editar</span></button></a>' +
                        '<a class="pull-right eliminar" data-id_tiposervicio="' + tipo.id_tiposervicio + '"><button class="btn btn-danger btn-sm">' +
                        '<span class="glyphicon glyphicon-remove"> Eliminar</span></button></a>' +
                        '</td>' +
                        '</tr>';
            });

            $("#cuerpoTiposervicio").html(html);//meto los datos en la tabla que corresponde
            $("#tablaTiposervicio").dataTable({//transforma la tabla en dataTable
                responsive: true,
                "sPagiationType": "full_numbers", //activa la paginación con números
                "language": {//cambia el lenguaje de la dataTable
                    "url": "../js/dataTable-es.json" //este es el archivo json del lenguaje español
                }
            });
            $(".oculto").hide();
        };
        app.actualizarDataTable = function (tipo, id) {
            if (id == 0) { //si entra acá es porque es agregar
                html += '<tr class="text-warning">' + //agrego cada uno de los datos correspondientes además habilito dos columnas para poder
                        '<td><a class="seleccionar" data-id_tiposervicio="' + tipo.id_tiposervicio + '"><button class="btn btn-info btn-sm">' +
                        '<span class="glyphicon glyphicon-eye-open left">  Info</span></button></a></td>' +
                        '<td>' + tipo.nombre_tiposervicio + '</td>' +
                        '<td>' + tipo.descripcion_tiposervicio + '</td>' +
                        '<td>' + tipo.fch_creacion + '</td>' +
                        '<td>' + tipo.fch_modificacion + '</td>' +
                        '<a class="pull-left editar" data-id_tiposervicio="' + tipo.id_usuario + '">' +
                        '<button class="btn btn-success btn-sm">' +
                        '<span class="glyphicon glyphicon-pencil"> Editar</span></button></a>' +
                        '<a class="pull-right eliminar" data-id_tiposervicio="' + tipo.id_usuario + '">' +
                        '<button class="btn btn-danger btn-sm">' +
                        '<span class="glyphicon glyphicon-remove"> Eliminar</span></button></a>' +
                        '</td>' +
                        '</tr>';
                $("#cuerpoTiposervicio").append(html);

            } else {
                var html = "";
                var fila = "";
                fila = $("#exampleBody").find("a[data-id_tiposervicio='" + id + "']").parent().parent();
                html += '<td><a class="seleccionar" data-id_tiposervicio="' + tipo.id_tiposervicio + '"><button class="btn btn-info btn-sm">' +
                        '<span class="glyphicon glyphicon-eye-open left">  Info</span></button></a></td>' +
                        '<td>' + tipo.nombre_tiposervicio + '</td>' +
                        '<td>' + tipo.descripcion_tiposervicio + '</td>' +
                        '<td>' + tipo.fch_creacion + '</td>' +
                        '<td>' + tipo.fch_modificacion + '</td>' +
                        '<a class="pull-left editar" data-id_tiposervicio="' + tipo.id_tiposervicio + '"><button class="btn btn-success btn-sm">' +
                        '<span class="glyphicon glyphicon-pencil"> Editar</span></button></a>' +
                        '<a class="pull-right eliminar" data-id_tiposervicio="' + tipo.id_tiposervicio + '"><button class="btn btn-danger btn-sm">' +
                        '<span class="glyphicon glyphicon-remove"> Eliminar</span></button></a>' +
                        '</td>';
                fila.html(html);
            }
            $(".oculto").hide();
        };
        
        
        app.cargarBotones = function (acceso) {
            if (acceso === '1') {
                $('#boton').html('<div class="btn-group">' +
                        '<a href="usuario.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">CARGA USUARIO</button></a>' +
                        '<a href="servicios.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">CARGAR SERVICIOS</button></a>' +
                        '<a href="../../admin.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-secondary">INICIO</button></a>' +
                        '</div>');

            } else if (acceso === '2') {
                $('#boton').html('<div class="btn-group">' +
                        '<a href="servicios.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">CARGAR SERVICIOS</button></a>' +
                        '<a href="../../admin.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-secondary">INICIO</button></a>' +
                        '</div>');
            }else{
                $('#boton').html('<div class="btn-group">' +
                        '<a href="../../admin.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-secondary">INICIO</button></a>' +
                        '</div>');
                
            }
        };
        
        
        app.init();
    })(TipoServicio);


});

