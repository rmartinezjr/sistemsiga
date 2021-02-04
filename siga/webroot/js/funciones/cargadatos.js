/**
 * Created by tec101 on 17/11/2017.
 */
function savedata(idformpregunta,data,iddataform,idformrespuesta,idformdinamic,idcaso,idpersona){
    var resp=false;
    $.ajax({
            url: getUrl() + 'savecarga',
            type: 'GET',
            dataType: 'html',
            async:false,
            data: {idformpregunta:idformpregunta,data:data,iddataform:iddataform,idformrespuesta:idformrespuesta,idformdinamic:idformdinamic,idcaso:idcaso,idpersona:idpersona, idcasoseguimientosolduradera:casoseguimientosolduradera_id},
        })
        .done(function(data) {
            data=JSON.parse(data);
            resp=data;
        })
        .fail(function(data) {
            data=JSON.parse(data);
            resp=data;
        });
    return resp;
}

function savedataFile(data){
    var resp=false;
    $.ajax({
            url: getUrl() + 'savecarga',
            type: 'POST',
            dataType: 'html',
            async:false,
            cache: false,
            contentType: false,
            processData: false,
            data: data,
        })
        .done(function(data) {
            data=JSON.parse(data);
            resp=data;
        })
        .fail(function(data) {
            data=JSON.parse(data);
            resp=data;
        });
    return resp;
}

function savedataobserv(idformpregunta,data,iddataform,idformrespuesta,idformdinamic){
    var resp=false;
    $.ajax({
            url: getUrl() + 'savecargaobserv',
            type: 'GET',
            dataType: 'html',
            async:false,
            data: {idformpregunta:idformpregunta,data:data,iddataform:iddataform,idformrespuesta:idformrespuesta,idformdinamic:idformdinamic,idcaso:caso_id},
        })
        .done(function(data) {
            data=JSON.parse(data);
            resp=data;
        })
        .fail(function(data) {
            data=JSON.parse(data);
            resp=data;
        });
    return resp;
}
