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
    <?= $this->Html->meta('icon', $this->Url->build('../../img/logo.png')); ?>


    <?= $this->Html->css(['lib/bootstrap.min','mainpublic','//use.fontawesome.com/releases/v5.1.1/css/all.css','lib/font-awesome.min.css']) ?>
     <?= $this->Html->script(['lib/jquery-2.2.4.min.js', '//code.jquery.com/ui/1.12.1/jquery-ui.js','lib/bootstrap.min.js']) ?>
    <?php $this->fetch('meta') ?>
    <?php $this->fetch('css') ?>
</head>

<body>
<?php $real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base')."/";  ?>

<div class="container-fluid">
    <div class="cont-layout-public">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>
</div>

<?= $this->Html->script(['funciones/msj_alert']) ?>
</body>
</html>