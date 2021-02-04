/**
 * Created by tec101 on 17/11/2017.
 */
function savedata(idformpregunta,data,iddataform,idformrespuesta,idformdinamic,identidad,idpais){
    var resp=false;
    $.ajax({
            url: getUrl() + 'savecarga',
            type: 'GET',
            dataType: 'html',
            async:false,
            data: {idformpregunta,data,iddataform,idformrespuesta,idformdinamic,identidad,idpais},
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

function savedataobserv(idformpregunta,data,iddataform,idformrespuesta,idformdinamic,identidad){
    var resp=false;
    $.ajax({
            url: getUrl() + 'savecargaobserv',
            type: 'GET',
            dataType: 'html',
            async:false,
            data: {idformpregunta,data,iddataform,idformrespuesta,idformdinamic,identidad},
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
