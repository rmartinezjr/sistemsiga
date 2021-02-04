var classname_styleF1 = document.getElementsByClassName("styleF1");//Clases para controlar vinculos a carga de datos
var classname_href_form_asig = document.getElementsByClassName("href-form-asig");
function setDisplayVinculo(){
    var enlace=$(this)[0]['attributes'][0]['nodeValue'];
    var tipo=$(this).data('content');
    var msj=Array();
    msj['#']="Usted no posee privilegios para ingresar a este formulario, póngase en contacto con su administrador.";
    msj['form_archivado']="No puedes acceder al formulario seleccionado ya que se encuentra en estado archivo.";
    msj['carga_no_finalizada']="No puedes realizar una nueva carga de datos sin antes llegar a la etapa final de la carga anterior.";
    if(enlace=='#' || enlace=='form_archivado'){//si no lleva ninguna función mostrar mensaje
        $("#span_message_enlace").html(msj[tipo]);
        $("#alert-vinculo").slideDown();
        setTimeout(function () {
            $("#alert-vinculo").slideUp();
        }, 6000);
    }
}

for(var v=0; v<classname_styleF1.length; v++){//asignando funcion a clases href
    classname_styleF1[v].addEventListener('click', setDisplayVinculo, false);
}

for(var v=0; v<classname_href_form_asig.length; v++){
    classname_href_form_asig[v].addEventListener('click', setDisplayVinculo, false);
}