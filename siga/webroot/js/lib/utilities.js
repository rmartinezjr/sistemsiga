function alertMessage(message, type, icon) {
    $("#alerta").removeAttr('class');
    $("#alerta").addClass("alert " + type);
    $("#alerta .icon").addClass(icon);
    $("#alerta .message").text(message);
    $("#alerta").slideDown();

    setTimeout(function () {
        $("#alerta").slideUp();
    }, 2000);
}

function loadPrivileges(action, idPerfil, idModulo) {

    $(".loading-overlay").removeClass('hide');
    $(".perfil.active").removeClass('active');
    $("#perfil-" + idPerfil).addClass('active');
    //console.log(idPerfil);
    $("#privilegios .load").load(action, {id: idPerfil}, function () {
        if (idModulo) {
            $(".for-modulo-" + idModulo).removeClass('hide');
        }

        $(".loading-overlay").addClass('hide');
    });
}