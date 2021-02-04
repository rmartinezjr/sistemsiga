var socket = io.connect('localhost:8095');
function setIdUserNotyf(id_user, username){
    //enviando id usuario
    socket.emit('id_perfil',{userId: id_user, username: username});

    //recibiendo notificaciones
    socket.on('notifys', function (data) {
        var obj=JSON.parse(data);
        if(obj.count){
            var badge=obj.count_notify;
            if(obj.count_notify>99) badge='99+';
            document.getElementById('icoNoti').setAttribute('data-badge',badge);
            document.getElementById('icoNoti').style.backgroundImage="url('"+getUrlIcons()+"notificaciones_activo.png')";
            $("#icoNoti").addClass("badge_notification");
        }else{
            document.getElementById('icoNoti').removeAttribute('data-badge');
            document.getElementById('icoNoti').style.backgroundImage="url('"+getUrlIcons()+"notificaciones.png')";
            $("#icoNoti").removeClass("badge_notification");
        }

        if(obj.rows){
            $("#content-notify").html(obj.content.mensaje);
            document.getElementById('ahref-notify').href=getOnlyUrlBase()+obj.content.url;
            document.getElementById('ahref-notify').setAttribute('data-notify', obj.content.id);
            document.getElementById('ahref-notify').setAttribute('data-username', username);
            $("#alert-noti").removeClass('no-display');
            setTimeout(function () {//se muestra notificaci�n por 1 minuto
                $("#alert-noti").addClass('no-display');
                socket.emit('id_perfil',{userId: id_user});
            }, 60000);
        }else{
            socket.emit('id_perfil',{userId: id_user});
        }
    });
}

function viewNotify(id){
    var id_notify=$("#"+id).data('notify');
    var username=$("#"+id).data('username');
    var url=document.getElementById(id).href;
    //enviando id de notificacion a ser vista
    socket.emit('set_notify_visto',{id_notify: id_notify, username:username});
    socket.on('redirect_notify', function (data) {
        if(data.res)window.location.assign(url);
    });
}