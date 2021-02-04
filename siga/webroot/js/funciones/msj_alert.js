$(document).ready(function () {
    //Seccion para establecer nuevos estilos de alert
    var div_alert=document.querySelectorAll('.alert-danger');
    var id="";
    var nodo_aux=false;
    var i_aux="<i class='fa fa-times fa-alert-custom'></i>";
    for(var div=0; div<div_alert.length; div++){
        id=document.getElementById(div_alert[div].id);
        nodo_aux=i_aux+id.innerHTML;
        if(nodo_aux!=false) id.innerHTML=nodo_aux;
    }

    div_alert=document.querySelectorAll('.alert-success');
    nodo_aux=false;
    i_aux="<i class='fa fa-check fa-alert-custom'></i>";
    for(var div=0; div<div_alert.length; div++){
        id=document.getElementById(div_alert[div].id);
        nodo_aux=i_aux+id.innerHTML;
        if(nodo_aux!=false) id.innerHTML=nodo_aux;
    }

    div_alert=document.querySelectorAll('.alert-warning');
    nodo_aux=false;
    i_aux="<i class='fa fa-exclamation fa-alert-custom'></i>";
    for(var div=0; div<div_alert.length; div++){
        id=document.getElementById(div_alert[div].id);
        nodo_aux=i_aux+id.innerHTML;
        if(nodo_aux!=false) id.innerHTML=nodo_aux;
    }
});