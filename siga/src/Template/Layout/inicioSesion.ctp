<!DOCTYPE html>
<html class="html-inicio">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= 'ANSP | ' . $title ?>

    </title>
    <?= $this->Html->meta('icon', $this->Url->build('../../img/favicon-ansp.png')); ?>

    <?= $this->Html->css(['lib/bootstrap.min.css','lib/font-awesome.min','main.css',"https://use.fontawesome.com/releases/v5.3.1/css/all.css"]) ?>
    <?= $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="inicio-sesion">
<?= $this->element('headerinicio') ?>
<div class="container content-inicio clearfix">
    <div class="row row-title-inicio">
        <div class="header-login">

        </div>
    </div>
    <div class="row row-inicio">
        <div class="form-block">
            <div class="alert alert-danger alert-dismissable" id="alert" style="display: none;">
                <span class="message"></span>
                <a href="#" class="close"  >&times;</a>
            </div>
            <?php if(isset($_SESSION['solicitud-errorpost'])){
                if($_SESSION['solicitud-errorpost']==1){
                    unset($_SESSION['solicitud-errorpost']);?>
                    <div class="alert alert-danger alerta-registro" id="alert-registro">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">No se ha podido acceder a esa pantalla.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php }
            } ?>
            <?php if(isset($_SESSION['solicitud-errorempty'])){
                if($_SESSION['solicitud-errorempty']==1){
                    unset($_SESSION['solicitud-errorempty']);?>
                    <div class="alert alert-danger alerta-registro" id="alert-registro">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Se debe de ingresar un número de documento, ya sea de la organizacion o de la persona.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php }
            } ?>

            <?php if(isset($_SESSION['solicitud-error'])){
                if($_SESSION['solicitud-error'] == 1){
                    unset($_SESSION['solicitud-error']);?>
                    <div class="alert alert-danger alerta-registro" id="alert-registro">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Ha ocurrido un error.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php }
            } ?>
            <div>
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        </div>
    </div>
</div>
<div class="_footer">
    <span>© <?=date("Y")?> | Academia Nacional de Seguridad Publica (ANSP)</span>
</div>

</body>
<script>
    jQuery(function(){
        setTimeout(function () {
            $(".alert-inicio").slideUp();
        }, 4000);

        setTimeout(function () {
            $(".alerta-registro").slideUp();
        }, 4000);
    });
</script>
</html>