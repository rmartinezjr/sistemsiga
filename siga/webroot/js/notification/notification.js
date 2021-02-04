/******
 * Author: Manuel Anzora
 * create: 04-01-2019
 * description: funcion para buscar las notificaciones segun el usuario logeado.
 * param: id de usuario logeado*******/
function searchNotif(userid){
    var url = getbaseapp()+'/notificacions/getnotif';
    var badge = 0;
    $.ajax({
        url: url,
        type: 'post',
        data: {userid:userid},
        cache: false,
        async: false,
        success: function(resp){
            if(parseInt(resp)>0){
                badge = parseInt(resp);
                if(parseInt(resp)>99) badge = '99+';
                document.getElementById('icoNoti').setAttribute('data-badge',badge);
                document.getElementById('icoNoti').style.backgroundImage="url('"+getUrlIcons()+"notificaciones_activo.png')";
                $("#icoNoti").addClass("badge_notification");
            }else{
                document.getElementById('icoNoti').removeAttribute('data-badge');
                document.getElementById('icoNoti').style.backgroundImage="url('"+getUrlIcons()+"notificaciones.png')";
                $("#icoNoti").removeClass("badge_notification");
            }

            setTimeout(function(){
                searchNotif(userid);
                }, 15000);
        }
    });
}

