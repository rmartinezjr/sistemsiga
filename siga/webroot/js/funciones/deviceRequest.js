var parser = new UAParser();
var userAgent=parser.getResult();
function openMenuDevice(){
    $("#sidebar-wrapper").css("width","73px");
    $("#sidebar-wrapper").css("margin-top","0");
    $("#sidebar-wrapper").css("top","75px");
    $("#wrapper").css("padding-left","73px");
    $("#menu").css("display","block");
    $(".menuBottom").css("display","block");
    $(".div-menu-x").css("display","none");
    //$(".contenidoDiv").children()[1].style.marginTop=0;
    $(".cont-btn-salir").removeClass("text-hidden");
    $(".contenido-menuMovil").css("display","none");
}
if(userAgent.device.type==undefined){//para device no mobiles
    $(".no_responsi").removeClass("no_responsi");
    if (window.innerWidth < 769) {
        openMenuDevice();
    }
}else{
    var items=document.querySelectorAll(".itemsa");
    items.forEach(function(element){
        if(element.title=='Preferencias'){//ocultar si se genera enlace a cat�logo de preferencias
            var parent=document.getElementById(element.id).parentElement;
            var className=parent.className+" no-display";
            parent.className=className;
        }
    });
}