<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= 'ANSP ' ?>|
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>


    <?= $this->Html->css(['impresion']) ?>

    <?php $this->fetch('meta') ?>
    <?php $this->fetch('css') ?>
    <?php $this->fetch('script') ?>
</head>
<body>
<div id="wrapper" class="toggled-2 " >
    <div id="page-content-wrapper" class=""  style="margin-top: 95px; ">
        <div class="container-fluid xyz heigth-content" >
            <div class="row  heigth-content" style=" padding: 0 !important; margin:-56px 0px 0px 2px;">
                <div class="col-lg-12 " style="padding: 0px !important;    margin-bottom: 60px;">
                    <div class="cont-layout">
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>