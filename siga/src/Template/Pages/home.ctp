<!DOCTYPE html>
<html class="html-inicio">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= 'ANSP | Inicio' ?>

    </title>
    <?= $this->Html->meta('icon', $this->Url->build('../../img/favicon-ansp.png'));
    ?>

    <?= $this->Html->css(['lib/bootstrap.min.css','lib/font-awesome.min','main.css']) ?>
    <?= $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="inicio-sistema">
<?= $this->element('headerinicio') ?>
<div class="container content-inicio clearfix">
    <div class="block-inicio">
        <div class="row block-text-inicio">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php if(isset($_SESSION['fallo-correo'])){
                    $mensaje = $_SESSION['fallo-correo'];
                    unset($_SESSION['fallo-correo']);?>
                    <div class="alert alert-danger alerta-registro" id="exito-registro">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message"><?= $mensaje ?></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <?php

                if(isset($_SESSION["exito-correo"])){
               $mensaje =$_SESSION["exito-correo"];
                   unset($_SESSION["exito-correo"]);?>
                    <div class="alert alert-success alerta-registro" id="exito-registro">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message"><?= $mensaje ?></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php } ?>

            </div>
        </div>
        <div class="row block-text-iniciosesion">
            <p class="text-inicio bold-inicio">Para usuarios y organizaciones registrados</p>
            <a class="button-inicio-home" href="<?= \Cake\Routing\Router::url(['controller' => 'users', 'action' => 'login'], true); ?>">
                <i class="fa fa-key"></i> Iniciar Sesión
            </a>
        </div>
        <div class="row block-text-registro">
            <p class="text-inicio bold-inicio">Solicitar ingreso a la plataforma o aplicar a una convocatoria por primera vez</p>
            <a class="button-solicitud-home" href="<?= \Cake\Routing\Router::url(['controller' => 'pages', 'action' => 'solicitud_registro'], true); ?>">
                <i class="fa fa-sign-in"></i> Registrarse
            </a>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <p class="copy-home text-center">&copy; <?= date("Y"); ?> FIAES, EL SALVADOR</p>
            </div>
        </div>
    </div>
</div>
<div class="_footer"></div>
</body>
<script>
    jQuery(function(){

        $(".alerta-registro").slideDown();
        setTimeout(function () {
            $(".alerta-registro").slideUp();
        }, 5000);
    });
</script>
</html>