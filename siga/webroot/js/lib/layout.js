$(window).on("resize width", function() {
    if ($(window).width() < 769) {
        $("#wrapper.toggled").css("padding-left","0px");
        $("#wrapper").css("padding-left","0px");
        if(userAgent.device.type!=undefined){//para device mobiles ocultar men�
            $("#sidebar-wrapper").css("width","0px");
        }else if(userAgent.device.type==undefined){
            openMenuDevice();
        }
    } else {
        $("#wrapper.toggled").css("padding-left","73px");
        $("#wrapper").removeAttr("style");
        $("#sidebar-wrapper").css("width","73px");
        if(userAgent.device.type==undefined){//para device no mobiles establecer valores originales del menu
            $("#sidebar-wrapper").css("top","85px");
            $("#sidebar-wrapper").css("margin-top","15px");
            $(".contenidoDiv").children()[1].style.marginTop="15px";
        }

    }
});

$(document).on("click", ".itemsNivel1", function()
{
    var idNivel1=$(this).attr('data-id');
    $.each($('.itemsNivel1'), function (indice, val) {
        if (idNivel1==$("#items"+$(this).attr('data-id')).attr('data-items'))
        {
            $(".items"+$(this).attr('data-id')).removeClass("hidden-items");
        } else
        {
            $(".items"+$(this).attr('data-id')).addClass("hidden-items");
        }

    });
});



$('.itemsNivel1').hover(function() {
    if(icoItems[$(this).attr('data-id')][0]==$(this).css('background-image').replace('url(','').replace('//','').split("/").pop().replace('")',''))
    {
        $("#"+$(this).attr('id')).tooltip('show');
        $(this).bind("mouseleave",function () {
            $("#"+$(this).attr('id')).tooltip('hide');
            $(this).css("background-image","url(\""+getUrlIcons()+icoItems[$(this).attr('data-id')][0]+"\")");
        });
    }
    else if(icoItems[$(this).attr('data-id')][0]!=$(this).css('background-image').replace('url(','').replace('//','').split("/").pop().replace('")',''))
    {
        $("#"+$(this).attr('id')).tooltip('hide');
        $(this).bind("mouseleave",function () {
            $("#"+$(this).attr('id')).tooltip('hide');
        });
    }
    else {

        $("#"+$(this).attr('id')).tooltip('hide');
    }
    $(this).css("background-image","url(\""+getUrlIcons()+icoItems[$(this).attr('data-id')][1]+"\")");

}, function() {
    $("#"+$(this).attr('id')).tooltip('hide');
    $(this).css("background-image","url(\""+getUrlIcons()+icoItems[$(this).attr('data-id')][0]+"\")");

});

$(document).on("click", "#Mypopov2", function()
{
    console.log("hola");
});



