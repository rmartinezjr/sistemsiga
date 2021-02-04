    <!DOCTYPE html>
<html class="html-inicio">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= 'ANSP | ' . $title ?>

    </title>
    <?= $this->Html->meta('icon', $this->Url->build('../../img/logo.png')); ?>

    <?= $this->Html->css(['lib/bootstrap.min.css','lib/font-awesome.min','main.css']) ?>
    <?= $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js', 'lib/jquery.maskedinput.js', 'funciones/validaciones.js', 'funciones/entidadContactos.js']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body >
<?= $this->element('headerinicio') ?>
<div id="wrapper" class="toggled-2 heigth-content " >
    <div id="sidebar-wrapper" class="content-menu" style="position: fixed;top: 85px;box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2);">
        <?= $this->element('menuRegistro') ?>
    </div>
    <div id="page-content-wrapper" class=" img-fondo heigth-content content-fondo">
        <div class="container-fluid xyz heigth-content">
            <div class="row  heigth-content registro-org-block">
                <div class="col-lg-12 registro-org-content">
                    <div class="div-av" ></div>
                    <?= $this->fetch('content') ?>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="_footer"></div>
</body>
<script>
    jQuery(function(){
        setTimeout(function () {
            $(".alert-inicio").slideUp();
        }, 4000);
    });
</script>
</html>