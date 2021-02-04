// Funcion que carga listado de entidades
function  ListadoEntidades(url, modelo, order) {
    var data = $('#filtro-entidad').val();

    if(order !== '') {
        var filtro_entidad = '';
        if(data !== '') {
            data = data.split(',');
            if(data[3] === 'ordenamiento') {
                if(data[0] === modelo && data[1] === order) {
                    if(data[2] === 'asc') {
                        filtro_entidad = modelo + ',' + order +',' + 'desc' + ',' + 'ordenamiento';
                        $(".listado-entidades").load(url , {modelo: modelo, order: order, tipoorder: 'desc', load: 'ordenamiento'}, function () {
                            $('#filtro-entidad').val(filtro_entidad);
                            $('#refresh-Entidads').parent().css("display", "none");
                        });
                    } else {
                        filtro_entidad = modelo + ',' + order +',' + 'asc' + ',' + 'ordenamiento';
                        $(".listado-entidades").load(url , {modelo: modelo, order: order, tipoorder: 'asc', load: 'ordenamiento'}, function () {
                            $('#filtro-entidad').val(filtro_entidad);
                            $('#refresh-Entidads').parent().css("display", "none");
                        });
                    }

                } else {
                    filtro_entidad = modelo + ',' + order +',' + 'asc' + ',' + 'ordenamiento';

                    $(".listado-entidades").load(url , {modelo: modelo, order: order, tipoorder: 'asc', load: 'ordenamiento'}, function () {
                        $('#filtro-entidad').val(filtro_entidad);
                        $('#refresh-Entidads').parent().css("display", "none");
                    });
                }
            }
        } else {
            filtro_entidad = modelo + ',' + order +',' + 'asc' + ',' + 'ordenamiento';

            $(".listado-entidades").load(url , {modelo: modelo, order: order, tipoorder: 'asc', load: 'ordenamiento'}, function () {
                $('#filtro-entidad').val(filtro_entidad);
                $('#refresh-Entidads').parent().css("display", "none");
            });
        }
    } else {
        $(".listado-entidades").load(url, function () {
            $('#refresh-Entidads').parent().css("display", "none");
        });
    }
}

// Funcion que carga listado de contactos
function  ListadoContactos(url, modelo, order) {
    var data = $('#filtro-contacto').val();
    if(order !== '') {
        var filtro_contacto = '';
        if(data !== '') {
            data = data.split(',');
            if(data[3] === 'ordenamiento') {
                if(data[0] === modelo && data[1] === order) {
                    if(data[2] === 'asc') {
                        filtro_contacto = modelo + ',' + order +',' + 'desc' + ',' + 'ordenamiento';
                        $(".listado-contactos").load(url , {modelo: modelo, order: order, tipoorder: 'desc', load: 'ordenamiento'}, function () {
                            $('#filtro-contacto').val(filtro_contacto);
                            $('#refresh-Contactos').parent().css("display", "none");
                        });
                    } else {
                        filtro_contacto = modelo + ',' + order +',' + 'asc' + ',' + 'ordenamiento';
                        $(".listado-contactos").load(url , {modelo: modelo, order: order, tipoorder: 'asc', load: 'ordenamiento'}, function () {
                            $('#filtro-contacto').val(filtro_contacto);
                            $('#refresh-Contactos').parent().css("display", "none");
                        });
                    }
                } else {
                    filtro_contacto = modelo + ',' + order +',' + 'asc' + ',' + 'ordenamiento';
                    $(".listado-contactos").load(url , {modelo: modelo, order: order, tipoorder: 'asc', load: 'ordenamiento'}, function () {
                        $('#filtro-contacto').val(filtro_contacto);
                        $('#refresh-Contactos').parent().css("display", "none");
                    });
                }
            }
        } else {
            filtro_contacto = modelo + ',' + order +',' + 'asc' + ',' + 'ordenamiento';
            $(".listado-contactos").load(url , {modelo: modelo, order: order, tipoorder: 'asc', load: 'ordenamiento'}, function () {
                $('#filtro-contacto').val(filtro_contacto);
                $('#refresh-Contactos').parent().css("display", "none");
            });
        }
    } else {
        $(".listado-contactos").load(url, function () {
            $('#refresh-Contactos').parent().css("display", "none");
        });
    }
}

// Funcion que carga filtro de listado de entidades
function  FiltroListadoEntidades(url) {
    $(".listado-entidades").load(url);

}

// Funcion que carga filtro de listado de contactos
function  FiltroListadoContactos(url) {
    $(".listado-contactos").load(url);
}

// Funcion que obtiene la mascara del tipo de documento seleccionado
function  getMascaraDocumento(id, url, elemento, isLoadPage) {
    if( id != '' && id != '0') {
        $.ajax({
            url: url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            cache:false,
            async:false,
            success:function (resp) {
                if(resp.error !== '0') {
                    if(!isLoadPage) {
                        elemento.val('');
                    }

                    elemento.removeAttr( "disabled" );
                    elemento.attr( "placeholder", resp.data.mascara );
                    elemento.mask(resp.data.mascara);
                }
            }
        });
    } else {
        elemento.val('');
        elemento.attr( "disabled", "disabled" );
        elemento.attr( "placeholder", 'Documento Identidad' );
    }
}
