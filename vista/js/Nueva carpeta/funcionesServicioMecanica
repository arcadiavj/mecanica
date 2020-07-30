
$(function () {

    var Servicio = {};
    var idUsuario = "";
    var arrayKm = [];
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
                    if (typeof datos['id_usuario'] !== 'undefined' && typeof datos['tipoAcceso_usuario'] !== 'undefined') {
                        $("#id_user").val(datos.id_usuario);
                        var tA = datos.tipoAcceso_usuario;
                        idUsuario = datos.id_usuario;
                        if (tA !== null) {
                            $("#logedUser").html(datos.usuario_usuario);
                            var acceso = datos.tipoAcceso_usuario;
                            app.buscarMovil();
                            app.bindings();
                            app.cargarBotones(acceso);
                            app.buscarKm();

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
                //app.borrarCampos();
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
                $('#comboTipo').focus();
            });

            $("#cuerpoTiposervicio").on('click', '.agregar', function (event) {
                event.preventDefault();
                app.cargarComboTiposervicio();
                app.mostrar();
                app.buscarKm();
                $('#modalSer').attr("class", "modal-dialog modal-md");
                $('#id').val($(this).attr("data-id_tiposervicio"));
                $('#mHeader').removeClass();
                $('#mHeader').attr("class", "modal-header bg-success");
                $('#patenteMovil').attr("disabled", true);
                var patente = $(this).parent().parent().children().first().next().html();
                $('#patenteMovil').val(patente);
                $('#descripcionTipo').val();
                $('#km').val(app.patKm(arrayKm, patente));
                $('#km').attr('disabled', true);
                app.activarControles();
                $('#guardar').html("Agregar");
                $('#accion').attr("value", "modificar");
                $('#tituloModal').html("AGREGAR Servicios");
                $('#modalTiposervicio').modal({show: true, backdrop: 'static', keyboard: false});
                $('#guardar').show();
                $('#reporDetalle').hide();

            });

            $("#cuerpoTiposervicio").on('click', '.seleccionar', function (event) {
                event.preventDefault();
                app.borrarCampos();
                var id = $(this).attr("data-id_movilesu");
                $("#mHeader").removeClass();
                $("#mHeader").attr("class", "modal-header bg-info");                
                $("#lblTipo").hide();
                $("#comboTipo").hide();
                $("#km").hide();
                $("#patenteMovil").hide();//.val($(this).parent().parent().children().first().next().html());
                $("#descripcionTipo").hide();//val($(this).parent().parent().children().first().next().next().html());
                $('#txtCreacion').hide();//val($(this).parent().parent().children().first().next().next().next().html());
                $('#txtModificacion').hide();//val($(this).parent().parent().children().first().next().next().next().next().html());
                $('#lblNomTipoServicio').hide();
                $('#lblDescTipo').hide();
                $('#lblTipoComb').hide();
                $('#lblComb').hide();
                app.desactivarControles();
                $("#guardar").hide();
                $("#reporDetalle").hide();
                $("#guardar").html("Modificar");
                $("#guardar").attr("value", "Modificar");
                $("#tituloModal").html("Listado de Servicios");
                //$("#modalTiposervicio").modal({show: true, backdrop: 'static', keyboard: false});
                console.log(id);
                app.buscarServicio(id);
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
            } else {
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
            $("#formTipo").bootstrapValidator('resetForm', true);
            $("#mensaje").val("").html();
            $('#nombreTipo').val("").html();
            $('#descripcionTipo').val("").html();
            $('#txtCreacion').val("").html();
            $('#txtModificacion').val("").html();
            $("#mensaje").val("").html();
            $("#mensaje").hide();
        };

        app.mostrar = function () {
            $("#formTipo").bootstrapValidator('resetForm', true);
            $("#relleno").html("");
            $("#km").show();
            $("#lblTipo").show();
            $("#comboTipo").show();
            $("#patenteMovil").show();
            $("#descripcionTipo").show();
            $('#txtCreacion').show();
            $('#txtModificacion').show();
            $('#comboTipoComb').show();
            $('#lblNomTipoServicio').show();
            $('#lblDescTipo').show();
            $('#lblTipoComb').show();
            $('#lblComb').show();
        };

        app.guardarTipo = function () {
            console.log("ENTRASTE ACA");
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

        app.buscarMovil = function () {//función que se encarga de realizar la busqueda de los usuarios
            var url = "../../controlador/ruteador/Ruteador.php?accion=buscar&nombreFormulario=Movilesu";//paso la dirección del 
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
                        message: "<p class='text-center'><img src='../../vista/images/ajax-loader.gif'></p>",
                        closeButton: false
                    });
                    dialog.modal('hide');
                }

            });
        };

        app.crearTarjetas = function (data, comb) {
            var tarjeta = "";
            var arrayTipoC = [];
            $.each(data, function (clave, tipo) {
                arrayTipoC.push(tipo.Id_TipoCombustible);
                if (tipo.Tipo === "Camioneta") {
                    tarjeta += $("#comboTipoComb").append('<option value="' + tipo.Id_Movil + '">' + tipo.Patente + '</option>');
                }
            });

            const newArr = [], arrayUnico = [];
            arrayTipoC.map(el => el in arrayUnico ? '' : arrayUnico[el] = true && newArr.push(el));
            var comb = "";
            $.each(arrayUnico, function (index, valor) {
                if (valor !== undefined)
                {
                    comb += '<div class="btn-group" role="group" aria-label="Basic example">' +
                            '<button type="button" class="btn btn-secondary">' + valor + '</button>';
                }
            });

            $('#select').html(comb);

        };
        app.buscarCombustible = function () {//función que se encarga de realizar la busqueda de los usuarios
            var url = "../../controlador/ruteador/Ruteador.php?accion=buscar&nombreFormulario=Combustible";//paso la dirección del 
            //ruteador para obtener los datos de la BD
            $.ajax({//ajax para realizar la petición de los datos
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function (data) {//si todo sale bien llamo a la funcion correspondiente
                    app.cargaArrayComb(data);
                },
                error: function () {//si algo sale mal muestra un mensaje de error
                    alert('error buscar Combustible');
                }

            });
        };
        app.cargaArrayComb = function (data) {
            $.each(data, function (index, tipos) {
                arrayComb.push(tipos.nombre_combustible);
            });

        };

        app.rellenarDataTable = function (data) {//función para rellenar la tabla
            var html = "";//variable que voy a utilizar para rellenar la tabla
            if ($.fn.DataTable.isDataTable('#tablaTiposervicio')) {
                $('#tablaTiposervicio').DataTable().destroy();
            }
            $.each(data, function (clave, movil) {//recorro todos los datos devueltos con el JSon
                html += '<tr class="text-warning">' +
                        '<td><a class="seleccionar" data-id_movilesu="' + movil.id_movilesu + '"><button class="btn btn-info btn-sm">' +
                        '<span class="glyphicon glyphicon-eye-open left">  Listar</span></button></a></td>' +
                        '<td>' + movil.Patente + '</td>' +
                        '<td>' + movil.Tipo + '</td>' +
                        '<td>' + movil.Marca + '</td>' +
                        '<td>' + movil.Modelo + '</td>' +
                        '<td>' + movil.Interno + '</td>' +
                        '<td>' +
                        '<a class="pull-left agregar" data-id_movilesu="' + movil.id_movilesu + '"><button class="btn btn-success btn-sm">' +
                        '<span class="glyphicon glyphicon-pencil"> agregar</span></button></a>' +
                        '<a class="pull-right eliminar" data-id_movilesu="' + movil.id_movilesu + '"><button class="btn btn-danger btn-sm">' +
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
        app.actualizarDataTable = function (movil, id) {
            if (id === 0) { //si entra acá es porque es agregar
                html += '<tr class="text-warning">' + //agrego cada uno de los datos correspondientes además habilito dos columnas para poder
                        '<td><a class="seleccionar" data-id_movilesu="' + movil.id_movilesu + '"><button class="btn btn-info btn-sm">' +
                        '<span class="glyphicon glyphicon-eye-open left">  Info</span></button></a></td>' +
                        '<td>' + movil.nombre_tiposervicio + '</td>' +
                        '<td>' + movil.descripcion_tiposervicio + '</td>' +
                        '<td>' + movil.fch_creacion + '</td>' +
                        '<td>' + movil.fch_modificacion + '</td>' +
                        '<a class="pull-left agregar" data-id_movilesu="' + movil.id_movilesu + '">' +
                        '<button class="btn btn-success btn-sm">' +
                        '<span class="glyphicon glyphicon-pencil"> Agregar</span></button></a>' +
                        '<a class="pull-right eliminar"data-id_movilesu="' + movil.id_movilesu + '">' +
                        '<button class="btn btn-danger btn-sm">' +
                        '<span class="glyphicon glyphicon-remove"> Eliminar</span></button></a>' +
                        '</td>' +
                        '</tr>';
                $("#cuerpoTiposervicio").append(html);

            } else {
                var html = "";
                var fila = "";
                fila = $("#exampleBody").find("a[data-id_tiposervicio='" + id + "']").parent().parent();
                html += '<td><a class="seleccionar" data-id_movilesu="' + movil.id_movilesu + +'"><button class="btn btn-info btn-sm">' +
                        '<span class="glyphicon glyphicon-eye-open left">  Info</span></button></a></td>' +
                        '<td>' + movil.nombre_tiposervicio + '</td>' +
                        '<td>' + movil.descripcion_tiposervicio + '</td>' +
                        '<td>' + movil.fch_creacion + '</td>' +
                        '<td>' + movil.fch_modificacion + '</td>' +
                        '<a class="pull-left agregar" data-id_movilesu="' + movil.id_movilesu + '"><button class="btn btn-success btn-sm">' +
                        '<span class="glyphicon glyphicon-pencil"> Agregar</span></button></a>' +
                        '<a class="pull-right eliminar" data-id_movilesu="' + movil.id_movilesu + '"><button class="btn btn-danger btn-sm">' +
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
                        '<button type="button" class="btn btn-primary">CARGA DE USUARIO</button></a>' +
                        '<a href="tipoServicio.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">TIPO DE SERVICIO</button></a>' +
                        '<a href="servicios.html" class="active bg-primary">' +
                        '<a href="../../admin.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-secondary">INICIO</button></a>' +
                        '</div>');

            } else if (acceso === '2') {
                $('#boton').html('<div class="btn-group">' +
                        '<a href="tipoServicio.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-primary">TIPO DE SERVICIO</button></a>' +
                        '<a href="servicios.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-secondary">INICIO</button></a>' +
                        '</div>');
            } else {
                $('#boton').html('<div class="btn-group">' +
                        '<a href="../../admin.html" class="active bg-primary">' +
                        '<button type="button" class="btn btn-secondary">INICIO</button></a>' +
                        '</div>');

            }
        };
        app.buscarServicio = function (id) {//función que se encarga de realizar la busqueda de los usuarios
            var url = "../../controlador/ruteador/Ruteador.php";//paso la dirección del 
            //ruteador para obtener los datos de la BD
            var datos = {
                "accion": "buscarJoin", 
                "nombreFormulario": "Servicios", 
                "nombreCampo": "id_movilesu", 
                "id": id
            };
            $.ajax({//ajax para realizar la petición de los datos
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json',
                success: function (data) {//si todo sale bien llamo a la funcion correspondiente

                    app.rellenarModal(data);
                },
                error: function () {//si algo sale mal muestra un mensaje de error
                    alert('error en BuscarServicio');
                }
            });
        };

        app.rellenarModal = function (data) {
            $("#modalTiposervicio").bootstrapValidator('resetForm', true);
            var km = "";
            var relleno = "";
            var i = 0;

            $.each(data, function (clave, movil) {
                console.log(arrayKm.patente);

                if (i === 0) {
                    var pat = movil.Patente;
                    console.log(pat);
                    var km = 0;
                    console.log(km);
                    for (a = 0; a < arrayKm.length; a++) {
                        if (arrayKm[a].patente === pat)
                            km = Math.trunc(arrayKm[a].km);
                        console.log(km);
                    }

                    relleno += '<div class="col-md-4"></div><b>Marca:</b>    ' + movil.Marca + '<br>';
                    relleno += '<div class="col-md-4"></div><b>Patente:</b>  ' + movil.Patente + '<br>';
                    relleno += '<div class="col-md-4"></div><b>Usuario:</b>  ' + movil.nombre_usuario + '<br>';
                    relleno += '<div class="col-md-4"></div><b>KM:</b>  ' + km + '<br>';

                }
                app.buscarKm(movil.Patente);
                relleno += '<div class="modal-body">' +
                        '<div class="container-fluid">' +
                        '<div class="row">' +
                        '<div class="col-md-3">.<b>TIPO SERVICIO:</b> </div>' +
                        '<div class="col-md-3 ml-auto">' + movil.nombre_tiposervicio + '<br></div>' +
                        '<div class="col-md-3 ml-auto"><b>OBSEVACIONES: </b>' + movil.Observaciones + '<br></div>' +
                        '<div class="col-md-3 ml-auto"><b>DESCRIPCION: </b>' + movil.descripcion_tiposervicio + '<br></div>' +
                        '</div></div>' +
                        '</div>';

                i++;
            });
            relleno += '<div class="col-md-1"></div><h2> CANTIDAD DE SERVICIOS:  ' + i + '</h2>';
            $("#relleno").html(relleno);
            if (i === 0) {
                $("#modalSer").attr("class", "modal-dialog modal-md");
            } else {
                $("#modalSer").attr("class", "modal-md");
            }

            i = 0;
            $("#modalTiposervicio").modal({show: true, backdrop: 'static', keyboard: false});
            km = 0;

        };
        app.cargarComboTiposervicio = function (index) {
            var url = "../../controlador/ruteador/Ruteador.php?nombreFormulario=Tiposervicio&accion=buscar";
            $("#comboTipo").html("<option value='-1'>Seleccionar</option>");
            console.log(index);
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                success: function (datosDevueltos) {
                    $.each(datosDevueltos, function (clave, tipo) {
                        $("#comboTipo").append('<option value="' + tipo.id_tiposervicio + '">' + tipo.nombre_tiposervicio + '</option>');
                        if (tipo.id_tiposervicio === index)
                            $("#comboTipo option").each(function () {
                                this.selected = tipo.nombre_tiposervicio;
                            });
                    });
                },
                error: function () {
                    alert("error al enviar al servidor busqueda Tipo Servicio");
                }
            });
        };

        app.buscarKm = function () {//función que se encarga de realizar la busqueda de los usuarios
            var url = "../../controlador/ruteador/Ruteador.php?nombreFormulario=Sitrack&accion=buscar";//paso la dirección del 
            //ruteador para obtener los datos de la BD
            $.ajax({//ajax para realizar la petición de los datos
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function (data) {//si todo sale bien llamo a la funcion correspondiente
                    $.each(data, function (clave, patente)
                    {
                        arrayKm.push({'patente': patente.Patente, 'km': patente.dato});
                    }
                    );
                },
                error: function () {//si algo sale mal muestra un mensaje de error
                    alert('error en BuscarKM');
                }
            });
        };
        
        app.patKm = function (arrayKm, pat) {

            $.each(arrayKm, function (clave, movil) {
                console.log(arrayKm.patente);
                console.log(pat);
                var km = 0;
                console.log(km);
                /*for (a = 0; a < arrayKm.length; a++) {
                    if (arrayKm[a].patente === pat)
                        km = Math.trunc(arrayKm[a].km);
                    console.log(km);
                }*/
            });
        };


        app.init();
    })(Servicio);


});