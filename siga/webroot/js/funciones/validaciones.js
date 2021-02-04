// Funcion que valida los campos unicos
var tipo="default";
function  ValUnique(val) {
    var url = getUrl() + "valunique";
    var campo = $("#"+val).val().trim();
    var id = $("#id").val();
    if(id === undefined){
        id = 0;
    }

    $.ajax({
        url: url,
        type: 'post',
        data: {campo: campo, id: id},
        dataType: 'json',
        async:false,
        cache:false,
        success:function (resp) {
            if(resp['error']===1){
                $("#"+val).val("");
                $(".message").text(resp["msj"]);
                $("#alert").slideDown();
                setTimeout(function () {
                    $("#alert").slideUp();
                }, 5000);
            }
        }
    });

}

// Funcion para validar abrevacion
function uniAbrev (val) {
    var url = getUrl() + "uniqueabrv";
    var campo = $("#"+val).val().trim();
    var id = $("#id").val();

    if(id === undefined){
        id = 0;
    }

    $.ajax({
        url: url,
        type: 'post',
        data: {campo: campo, id: id},
        dataType: 'json',
        async:false,
        cache:false,
        success:function (resp) {
            if(resp['error']===1){
                $("#"+val).val("");
                $(".message").text(resp["msj"]);
                $("#alert").slideDown();

                setTimeout(function () {
                    $("#alert").slideUp();
                }, 5000);

            }
        }
    });
}

// Funcion que valida los campos requeridos
function valRequired(id, label, type){
    //EL AREGLO SE LLENA CON LOS ID DE LOS CAMPOS REQUERIDOS
    var campo = $("#"+id).val().trim();
    if(campo === '' || campo === 0){
        if(type === 'select') {
            $(".message").text("Debe seleccionar una opción del campo "+label+".");
            $("#alert").slideDown();
            setTimeout(function () {
                $("#alert").slideUp();
            }, 5000);
        } else {
            $(".message").text("El campo "+label+" debe ser completado.");
            $("#alert").slideDown();
            setTimeout(function () {
                $("#alert").slideUp();
            }, 5000);
        }

        return false;
    }
}


// Funcion que valida varios campos únicos según su tipo
// Agregada el 16/10/2017 para la tarea FIAES 27
function  ValUniqueType(val) {
    var url = getUrl() + "valunique";
    var campo = $("#"+val).val().trim();
    var id = $("#id").val();

    var type=$("#"+val).attr('name');//se toma el tipo de campo definido como un data-type
    tipo=type;//se asigna el valor a una variable en el archivo ctp para ser utilizada y enviada cuando se realice un submit
    if(id === undefined){
        id = 0;
    }

    $.ajax({
        url: url,
        type: 'post',
        data: {campo: campo, id: id, tipo: type},
        dataType: 'json',
        async:false,
        cache:false,
        success:function (resp) {
            if(resp['error']===1){
                $("#"+val).val("");
                $(".message").text(resp["msj"]);
                $("#alert").slideDown();
                setTimeout(function () {
                    $("#alert").slideUp();
                }, 5000);
            }
        }
    });

}

// Funcion que valida si el valor decimal ingreso tiene como maximo 2 decimales y
// que el valor no tenga mas de un punto (.).
// Funcion agregada el 19/10/2017
function ValDecimalValido(elemento) {
    var valor = $( elemento ).val();
    var campo = $( elemento ).attr('data-elemento');
    var arrayValor = valor.split('.');

    if(arrayValor.length > 2) {
        $( elemento ).val('');
        $(".message").text("Ingrese un valor válido para el campo " + campo);
        $("#alert").slideDown();
        setTimeout(function () {
            $("#alert").slideUp();
        }, 5000);
    } else if(arrayValor.length === 2) {
        if(arrayValor[1].length > 2) {
            $( elemento ).val('');
            $(".message").text("El valor del campo " + campo + " debe tener como máximo 2 decimales");
            $("#alert").slideDown();
            setTimeout(function () {
                $("#alert").slideUp();
            }, 5000);
        }
    }

    return false;
}

// Funcion que valida si el porcentaje ingresado es un valor entre 0.00 y 100.00.
// Funcion agregada el 19/10/2017
function ValPorcentaje( elemento ) {
    var valor = $( elemento ).val();
    var campo = $( elemento ).attr('data-elemento');

    if(parseFloat(valor) < 0.00 || parseFloat(valor) > 100.00) {
        $( elemento ).val('');
        $(".message").text("El valor del campo " + campo + " debe estar entre 0.00 y 100.00");
        $("#alert").slideDown();
        setTimeout(function () {
            $("#alert").slideUp();
        }, 5000);
    }
}

// Funcion que valida si el porcentaje ingresado es un valor entre 0.00 y 100.00.
// Funcion agregada el 19/10/2017
function ValPorcentajeISR( elemento ) {
    var valor = $( elemento ).val();
    var campo = $( elemento ).attr('data-elemento');
    console.log(valor);
    if(parseFloat(valor) < 0.00 || parseFloat(valor) > 30.00) {
        $( elemento ).val('');
        $(".message").text("El valor del campo " + campo + " debe estar entre 0.00 y 30.00");
        $("#alert").slideDown();
        setTimeout(function () {
            $("#alert").slideUp();
        }, 4000);
    }
}

// Funcion que valida que el valor ingresado sea numerico, letras y/o carecteres permitidos que se han especificado.
// Funcion agregada el 17/10/2017
// Recibe como parametros;
// * Valor ingresado del teclado
// * Caracteres permitidos
// * Si permite ingreso de numeros
// * Si permite ingreso de letras
function  ValCaracteresValidos(e, keycodes, permiteNumeros, permiteLetras) {
    var keycodespermitidos = [8, 9, 13, 16, 17, 20, 27, 35, 36, 37, 38, 39, 40, 46, 116, 123];
    var estaCaracter = false;
    var keyCodePermitido = false;

    for(var j = 0; j < keycodespermitidos.length; j++) {
        var isKeycode = keycodespermitidos[j];
        if (isKeycode === e.keyCode) {
            keyCodePermitido = true;
            break;
        }
    }

    if(keycodes.length > 0) {
        // Verifica si se encuentra el caracter dentro de los caracteres permitidos especificados
        for(var i=0; i< keycodes.length; i++) {
            var keycode = keycodes[i];
            if (keycode === e.keyCode) {
                estaCaracter = true;
                break;
            }
        }

        // Si permite numeros, letras y caracteres especificados
        if(permiteNumeros && permiteLetras) {
            if ((e.shiftKey || (!(e.keyCode >= 48 && e.keyCode <= 57)))
                && (!(e.keyCode >= 96 && e.keyCode <= 105))
                && (!(e.keyCode >= 65 && e.keyCode <= 90))
                && (!estaCaracter) && (!keyCodePermitido)) {
                e.preventDefault();
            }
            // Si permite numeros y caracteres especificados, no permite letras
        } else if(permiteNumeros && !permiteLetras) {
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57))
                && (e.keyCode < 96 || e.keyCode > 105)
                && (!estaCaracter) && (!keyCodePermitido)) {
                e.preventDefault();
            }
            // Si permite letras y caracteres especificados, no permite numeros
        } else if(!permiteNumeros && permiteLetras) {
            if (e.shiftKey || (e.keyCode < 65 || e.keyCode > 90)
                && (!estaCaracter) && (!keyCodePermitido)) {
                e.preventDefault();
            }
        }
    } else {
        // Solo permite numeros y letras
        if(permiteNumeros && permiteLetras) {
            if ((e.shiftKey || (!(e.keyCode >= 48 && e.keyCode <= 57)))
                && (!(e.keyCode >= 96 && e.keyCode <= 105))
                && (!(e.keyCode >= 65 && e.keyCode <= 90))
                && (!keyCodePermitido)) {
                e.preventDefault();
            }
            // Solo permite numeros
        } else if(permiteNumeros && !permiteLetras) {
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57))
                && (e.keyCode < 96 || e.keyCode > 105)
                && (!keyCodePermitido)) {
                e.preventDefault();
            }
            // Solo permite letras
        } else if(!permiteNumeros && permiteLetras) {
            if (e.shiftKey || (e.keyCode < 65 || e.keyCode > 90) && (!keyCodePermitido)) {
                e.preventDefault();
            }
        }
    }
}
