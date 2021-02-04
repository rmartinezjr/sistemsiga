<?php
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';

$dir='//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/img/iconos/';
$only_url_base = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/';
?>
<!DOCTYPE html>
<html class="html-home">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= 'ANSP ' ?>|
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon', $this->Url->build('../../img/favicon-ansp.png')); ?>

    <?= $this->Html->css(['lib/bootstrap.min','lib/font-awesome.min','main']) ?>
    <?= $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']) ?>
    <?php $this->fetch('meta') ?>
    <?php $this->fetch('css') ?>
    <?php $this->fetch('script') ?>
    <script>
        function getUrl(){
            return "<?=$real_url?>";
        }
        function getUrlIcons(){
            return "<?=$dir?>";
        }
        function getOnlyUrlBase(){
            return "<?=$only_url_base?>";
        }
    </script>
    <script>
        var icon=[];
        var icoItems=[];
        $(document).on('ready', function ()
        {
            $("html, body").animate({
                scrollTop: 0
            }, 1000);

            <?php foreach ($_SESSION['menus']['nivel1'] as $items)
            {?>
            if (icoItems.indexOf(parseInt($(this).attr('data-mm'))) === -1) {
                <?php  $nombre = explode(".", $items->filename); ?>
                icon.push("<?=$items->filename;?>")
                icon.push("<?=$nombre[0] . "2." . $nombre[1];?>")
                icoItems[parseInt(<?=$items->id;?>)] = icon;
                icon = [];
            }
            <?php } ?>

        });
    </script>
</head>
<body class="body-home">
<?php $real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base')."/";  ?>

<div  class="hidden-sm  hidden-md hidden-lg contenido-menuMovil ">
    <ul class="nav navbar-nav listM" >
        <li class="active listM "><a id="menu-togglemm" class="hidden-sm  hidden-md hidden-lg menu-toggle" data-mm="1">
                <i class="fa fa-bars  fa-stack-1x "></i></a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right listM2   " >
        <li class="listM"><a  href="<?=$real_url?>users/logout">Salir <i class="fa fa-power-off fa-lg" aria-hidden="true"></i></a></li>
    </ul>
</div>
<?= $this->element('header') ?>

<div id="wrapper" class="toggled-2 heigth-content " style="position: relative;">
    <div id="sidebar-wrapper" class="content-menu" style="position: fixed;z-index: 1;top: 95px;box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2);
">
        <?= $this->element('menuLeft') ?>
    </div>
    <div id="page-content-wrapper" class=" img-fondo heigth-content content-fondo"  >
        <div class="container-fluid xyz heigth-content" >
            <div class="row  heigth-content" style=" padding: 0 !important; ">
                <div class="col-lg-12 " style="padding: 0px !important;">
                    <div class="alert alert-danger alert-dismissable " style="display: none;">
                        <span class="message"></span>
                        <a href="#" class="close"  >&times;</a>
                    </div>
                    <!-- div para mensaje de notificaciones -->
                    <div class="alert alert-dismissable alert-noti no-display" id="alert-noti">
                        <div class="col-xs-1 p-0"><div class="notify-icon"></div></div>
                        <div class="col-xs-8 p-0"><span id="content-notify"></span></div>
                        <div class="col-xs-3 p-0 txt-align-r m-t-17"><a href="#" id="ahref-notify" class="no-underline-hover" onclick="viewNotify(this.id); return false;">Ver detalles</a></div>
                    </div>
                    <center>
                        <?php
                        $menus=$_SESSION['menus'];
                        foreach ($menus['nivel1'] as $items)
                        {
                    //      echo $this->Html->image(array('controller'=>'Files','action'=>'download',$items->id),array('alt'=>'Imagen desde Blob'));
echo "<br>";
                        }
                        ?>
                        <div class="col-md-6 col-md-offset-3">
                            <?= $this->Flash->render() ?>
                        </div>
                        <?= $this->fetch('content') ?>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="_footer2">
    <span>© <?=date("Y")?> | Academia Nacional de Seguridad Publica (ANSP)</span>
</div>
<!-- Script para  identificar device de petici�n: desktop, laptop, mobile, etc -->
<?= $this->Html->script(['lib/ua-parser.min.js','funciones/deviceRequest.js']) ?>
<?= $this->Html->script(['lib/sidebar_menu','lib/layout']) ?>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<script type="text/javascript">

    var dateContainer = $(".hora-header-inicio");
    setInterval(function() {
        var date = new Date();
        var html = '';

        html += zeroSpan(date.getHours()) + ":";
        html += zeroSpan(date.getMinutes()) + ":";
        html += zeroSpan(date.getSeconds());

        dateContainer.html(html);
    }, 1000);

    function zeroSpan (number) {
        if (number < 10) {
            return "0" + number;
        }
        return number;
    }

    $(document).on('ready', function ()
    {
        $( window ).resize(function() {
            if((($("#sidebar-wrapper").height()) < 525) && ($(window).width() < 767))
            {
                $(".menuBottom").css("margin-left", "-50px");
                $(".menuBottom").css("display", "none");
                $("#sidebar-wrapper").css("overflow-y","overlay");
                $(".menuTool").show(900);
                $("#menu").css("display","none");
            }
            else if((($("#sidebar-wrapper").height()) > 525) && ($(window).width() < 767))
            {
                $(".menuTool").show(900);
                $(".menuBottom").css("margin-left","-50px");
                $(".menuBottom").css("display","none");
                $("#menu").css("height",$("#sidebar-wrapper").height()-73);
                $("#sidebar-wrapper").css("overflow-y","overlay");

            }
            else if((($("#sidebar-wrapper").height()) < 525) && ($(window).width() >= 767))
            {

                $("#menu").css("height",$("#sidebar-wrapper").height()-73);

                $("#subMenu").css("margin-top","-20px");

                $(".menuBottom").css("margin-left","-50px");
                $(".menuBottom").css("display","none");
                $(".menuTool").show(900);
            }else if((($("#sidebar-wrapper").height()) >= 525) && ($(window).width() >= 767))
            {
                $("#sidebar-wrapper").css("overflow-y","hide");
              if($(".menuBottom").css('margin-left')=="-50px")
              {
                  width=-50;
                  $(".menuTool").hide();
                  $(".menuBottom").css("display","block");
                  idFrame=setInterval(subMenuBottom,0.1);
              }
            }

        });
        });

    function subMenuBottom() {
        if (width == 0) {

            clearInterval(idFrame);
        } else {
            width++;
            $(".menuBottom").css("margin-left",width);
        }
    }
</script>
</body>
</html>