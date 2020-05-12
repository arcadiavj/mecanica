 function buscarTiposervicio () {//función que se encarga de realizar la busqueda de los usuarios
    var url = "../../controlador/ruteador/Ruteador.php?accion=buscar&nombreFormulario=Tiposervicio";//paso la dirección del 
    //ruteador para obtener los datos de la BD
    $.ajax({//ajax para realizar la petición de los datos
        url: url,
        method: 'GET',
        dataType: 'json',
        success: function (data) {//si todo sale bien llamo a la funcion correspondiente
            console.log(data);
        },
        error: function () {//si algo sale mal muestra un mensaje de error
            alert('error');
        }
    });
};

