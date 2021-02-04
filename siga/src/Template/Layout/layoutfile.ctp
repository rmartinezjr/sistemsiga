<?php
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
$dir='//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/img/iconos/';
?>
<script>
    function getUrl(){
        return "<?=$real_url?>";
    }
    function getUrlIcons(){
        return "<?=$dir?>";
    }

</script>
<!DOCTYPE html>
<html class="html-home">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= 'OIMRE ' ?>|
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon', $this->Url->build('../../img/logo.png')); ?>


    <?= $this->Html->css(['lib/bootstrap.min','lib/font-awesome.min','main','lib/fileinput']) ?>
    <?= $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']) ?>
    <?php $this->fetch('meta') ?>
    <?php $this->fetch('css') ?>
    <?php $this->fetch('script') ?>


            <?php
            $id=0;
            if(isset($_SESSION['idimagenmovil'])) {
                $id=$_SESSION["idimagenmovil"];
            }
        ?>
<style>
    .nav li a:hover {
        text-decoration: none;
        background-color: inherit;
        margin-top: -2px;
        background-color: #30406f;
    }
    .nav>li>a:hover {
        padding-top: 10px;
        padding-bottom: 10px;
        line-height: 20px;
        background-color: #2b3e75;
    }  .nav>li>a:focus {
               background-color: #364779;
    }
</style>
</head>

<body>
<?php $real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base')."/";  ?>
<div class="hidden-sm  hidden-md hidden-lg div-menu-x" style="margin-top:-40px;font-size: 12px;height: 40px;position: fixed;">
    <ul class="nav navbar-nav listM" >
        <li style="    margin-top: -4px;width: 45px;margin-left: 9px;">
            <a class="hidden-sm  hidden-md hidden-lg" href="javascript:history.go(-1)">
                <i class="fa  fa-chevron-left" aria-hidden="true"></i></a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right listM2   " >
        <li style="padding-left: initial;
    padding-right: initial;  margin-top: -4px;width: 45px;margin-right: 9px;"><a  style="    text-decoration: none;
    color: white;" href="<?= \Cake\Routing\Router::url(['controller' => 'Regaccions', 'action' => 'downloadOne',$id]);?>">
                <i class="glyphicon glyphicon-cloud-download"></i>
            </a></li>

    </ul>
</div>

                    <div class="cont-layout" style="margin-top: 40px;margin-right: 0px;" >
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content') ?>
                    </div>




<div class="_footer  hidden-sm hidden-md hidden-lg" style="position: absolute;bottom: 0;width: 100%;height: 35px;background-color: #1d2a50;background-image: none;text-align: center;
padding-top: 8px;font-size: 12px;">
    &#169; 2017 FIAES, EL SALVADOR
</div>


<?= $this->Html->script(['lib/sidebar_menu','lib/layout']) ?>

</body>
</html>