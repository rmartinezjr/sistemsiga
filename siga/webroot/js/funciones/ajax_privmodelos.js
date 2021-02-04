function changeprivilegios(matriz_priv){
    var resp=false;
    $.ajax({
        url: getUrl()+'changeprivilegios',
        type: 'GET',
        dataType: 'html',
        async: false,
        data: {modelofuncion_id:matriz_priv, perfil_id:perfil_id}
    })
        .done(function(data){
            data=JSON.parse(data);
            resp=data
        })
        .fail(function(data){
            data=JSON.parse(data);
            resp=data
        });
    return resp;
}

function savepersonalizacion(option_radioadming, option_selectcustom){
    var resp=false;
    $.ajax({
        url: getUrl()+'savepersonalizacion',
        type: 'GET',
        dataType: 'html',
        async: false,
        data: {option_radioadming:option_radioadming, option_selectcustom:option_selectcustom, perfil_id:perfil_id}
    })
        .done(function(data){
            data=JSON.parse(data);
            resp=data
        })
        .fail(function(data){
            data=JSON.parse(data);
            resp=data
        });
    return resp;
}

function getopcionesdetallepersonalizacion(option_radioadming, option_selectcustom){
    var resp=false;
    $.ajax({
        url: getUrl()+'getopcionesdetallepersonalizacion',
        type: 'GET',
        dataType: 'html',
        async: false,
        data: {option_radioadming:option_radioadming, option_selectcustom:option_selectcustom, perfil_id:perfil_id}
    })
        .done(function(data){
            data=JSON.parse(data);
            resp=data
        })
        .fail(function(data){
            data=JSON.parse(data);
            resp=data
        });
    return resp;
}

function savedetallepersonalizacion(option_radioadming, option_selectcustom, option_selectdetailcustom){
    var resp=false;
    $.ajax({
        url: getUrl()+'savedetallepersonalizacion',
        type: 'GET',
        dataType: 'html',
        async: false,
        data: {option_radioadming:option_radioadming, option_selectcustom:option_selectcustom, option_selectdetailcustom:option_selectdetailcustom, perfil_id:perfil_id}
    })
        .done(function(data){
            data=JSON.parse(data);
            resp=data
        })
        .fail(function(data){
            data=JSON.parse(data);
            resp=data
        });
    return resp;
}