jQuery(function(){
    $("#send").click(function () {
        var nombre = $("#nombre").val();
        var edad = $("#edad").val();
        var genero = $("#genero").val();
        var telefono = $("#telefono").val();
        var correo = $("#correo").val();
        var departamento = $("#departamento").val();
        var descripcion = $("#descripcion").val();
        var provServ = $("#provserv").val();
        var url = getUrl()+"formproveedores/saveExternal";
        if(nombre != '' && correo != '' && descripcion != '' && edad != '' && genero != '' && telefono != '' && departamento != ''){
            $('#content').html('<img src="../../img/iconos/loading.gif" alt="loading" /><br/>Un momento, por favor...');
            $.ajax({
                url: url,
                data: {nombre: nombre, correo: correo, descripcion: descripcion, provServ: provServ, edad: edad, genero: genero, departamento: departamento,
                telefono:telefono},
                type: 'post',
                cache: false,
                async: false,
                dataType: 'json',
                success: function(resp){
                    //Cargamos finalmente el contenido deseado
                    $('#content').fadeIn(1000).html("");
                    if(resp["error"]==0){
                        //$("#content").html("<div class='alert alert-success no-display alert-prov'><span class='icon icon-cross-circled'></span><span class='message'>Solicitud Enviada.</span></div>");
                        $("#alertError .message").text("Sucedio un error. Intente nuevamente");
                        $("#alertError").slideDown();
                        setTimeout(function () {
                            $("#alertError").slideUp();
                        }, 4000);
                    }else{
                        $("#nombre").val("");
                        $("#edad").val("");
                        $("#genero").val("");
                        $("#telefono").val("");
                        $("#correo").val("");
                        $("#departamento").val("");
                        $("#descripcion").val("");

                        $(".alert-prov").slideDown();
                        setTimeout(function () {
                            $(".alert-prov").slideUp();
                        }, 4000);
                    }

                }
            });
        }else{
            $("#alertError .message").text("Complete los campos requeridos.");
            $("#alertError").slideDown();
            setTimeout(function () {
                $("#alertError").slideUp();
            }, 4000);
        }
    });



});
function validar_email( email )
{
    var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email) ? true : false;
}
function validar_telefono(val){
    var regex = /^[ 0-9-()]*$/;
    return regex.test(val);

}