var  ItemsAct = [];
var  ItemsActMovil = [];
var  ItemsIcoAct = [];
var width = 0;
var idFrame = 0;
var idFrame2 = 0;

$(".menu-toggle").click(function(e) {

    e.preventDefault();
    //  $("#wrapper").toggleClass("toggled");

    if (ItemsActMovil.indexOf(parseInt($(this).attr('data-mm'))) === -1)
    {
        width = -50;
        ItemsActMovil.push(parseInt($(this).attr('data-mm')));
        $("#wrapper.toggled").css("padding-left","73px");
        $("#wrapper").css("padding-left","73px");
        $("#sidebar-wrapper").css("width","73px");
        $("#wrapper").addClass("toggled");

        if((($("#sidebar-wrapper").height()) > 525) && ($(window).width() > 767))
        {
            $(".menuBottom").css("display","block");
            $(".menuTool").hide();
            idFrame=setInterval(frame,5);
        }
        else if((($("#sidebar-wrapper").height()) > 525) && ($(window).width() < 767))
        {
            $(".menuBottom").css("display","block");
            $(".menuTool").hide();
            idFrame=setInterval(frame,5);
        }
        else
        {
            var items=document.querySelectorAll(".itemsa");
            var pref = 0;
            items.forEach(function(element){
                if(element.title=='Preferencias'){//ocultar si se genera enlace a cat�logo de preferencias
                    var parent=document.getElementById(element.id).parentElement;
                    var className=parent.className+" no-display";
                    parent.className=className;
                    pref = 1;
                }
            });

            if((pref == 0) && ($(window).width() > 767)) {
                $(".menuTool").show(900);
            } else {
                $(".menuTool").hide();
            }
        }
        setTimeout(function () {
            $("#menu").css("display","block");
        }, 160);

        $("#sidebar-wrapper").css("top","65px");
        /// $("#menu").css("top","25px");

    }else {
        width = 0;
        $("#wrapper").removeClass("toggled");
        $("#menu").css("display","none");
        $("#wrapper.toggled").css("padding-left","0px");
        $("#wrapper.toggled").css("width","0px");
        $(" #sidebar-wrapper").css("width","0px");
        $("#wrapper").css("padding-left","0px");
        $("#wrapper.toggled").addClass("");
        idFrame2=setInterval(_frame,10);
        ItemsActMovil = [];
    }
});

$(".menu-toggle-2").click(function(e) {

    $("#"+$(this).attr('id')).tooltip('hide');
    e.preventDefault();
    //   $("#wrapper").toggleClass("toggled-2");

    if (ItemsAct.indexOf(parseInt($(this).attr('data-id'))) === -1)
    {
        ItemsAct = [];
        ItemsAct.push(parseInt($(this).attr('data-id')));
        $("#wrapper").removeClass("toggled-2");
        $("#wrapper.toggled").css("padding-left","273px");
        $("#sidebar-wrapper").css("width","273px");
        $("._"+$(this).attr('data-id')).css("background-image","url(\""+getUrlIcons()+icoItems[$(this).attr('data-id')][1]+"\")");
        ItemsIcoAct.push(ItemsAct);


        $("._"+$(this).attr('data-id')).unbind('mouseleave');

        if(ItemsIcoAct.length== 2)
        {
            $("._"+ItemsIcoAct[0][0]).css("background-image","url(\""+getUrlIcons()+icoItems[ItemsIcoAct[0][0]][0]+"\")");
            ItemsIcoAct.splice(0, 1);
        }

    }else {
        $("#wrapper").addClass("toggled-2");

        $("#wrapper.toggled").css("padding-left","73px");
        /* $("#wrapper.toggled").css("width","73px");*/
        $(" #sidebar-wrapper").css("width","73px");
        $("._"+$(this).attr('data-id')).css("background-image","url(\""+getUrlIcons()+icoItems[$(this).attr('data-id')][0]+"\")");
        ItemsAct = [];
        ItemsIcoAct=[];
        $('#menu ul').hide();
    }
});



function initMenu() {
    $('#menu ul').hide();
    $('#menu ul').children('.current').parent().show();
    //$('#menu ul:first').show();
    $('#menu li a').click(
        function() {
            var checkElement = $(this).next();
            if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
                return false;
            }
            if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
                $('#menu ul:visible').slideUp('normal');
                checkElement.slideDown('normal');
                return false;
            }
        }
    );
}
$(document).ready(function() {

    initMenu();


});
function _frame() {
    if (width == -50) {
        clearInterval(idFrame2);

        $(".menuBottom").css("display","none");
    } else {

        width--;
        $(".menuBottom").css("margin-left",width);
    }
}

function frame() {
    if (width == 0) {

        clearInterval(idFrame);
    } else {
        width++;

        $(".menuBottom").css("margin-left",width);
    }
}


