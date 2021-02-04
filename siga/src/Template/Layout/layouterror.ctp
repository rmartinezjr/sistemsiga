<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <title>
        <?= 'ANSP ' ?>|
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon', $this->Url->build('../../img/logo.png')); ?>

    <?= $this->Html->css(['lib/bootstrap.min.css','lib/font-awesome.min','main.css']) ?>
    <?= $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<style>
    body {
        padding-top: 160px;
        background-attachment: fixed;
        background-image: url("<?= $this->Url->image('errores.png')?>");
        background-repeat: no-repeat;
        background-position: bottom center;
        background-size: cover;
        margin: 0;
        margin-bottom: 30px;
        height: 100%;
    }

    .icoIni2 {
        background-image: url("<?= $this->Url->image('iconos/Inicio2.png')?>");
        background-repeat: no-repeat;
        background-size: contain;
        margin-left: 30px;
        height: 40px;
        margin-bottom: 23px;
        width: 100%;
        z-index: 1;
    }
    .icono-error {
        height: 40px;
        width: 55px;
    }

    .error-code {
        font-size: 28px;
        font-weight: bold;
        margin-left: 15px;
        position: relative;
        top: 10px;
    }

    #menu-error {
        top: 135px;
    }

    .error-inicio {
        position: relative;
        top: 35px;
        left: -12px;
        color: #2a3163;
        font-size: 16px;
        font-weight: 400;
    }
    @media (max-width: 350px) {
        body {
            padding-top: 85px;
            background-attachment: fixed;
            background-image: url("<?= $this->Url->image('errores.png')?>");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            margin: 0;
            margin-bottom: 30px;
            height: 100%;
        }

        .icoIni2 {
            background-image: url("<?= $this->Url->image('iconos/Inicio2.png')?>");
            background-repeat: no-repeat;
            background-size: contain;
            margin-left: 0;
            height: 30px;
            margin-bottom: 23px;
            width: 100%;
            z-index: 1;
        }

        .icono-error {
            height: 30px;
            width: 45px;
        }

        .error-code {
            font-size: 18px;
            font-weight: bold;
            margin-left: 5px;
            position: relative;
            top: 5px;
        }
        .texto-error p {
            font-size: 11px;
            text-align: justify;
        }

        #menu-error {
            top: 85px;
        }

        .error-inicio {
            position: relative;
            top: 17px;
            left: -12px;
            color: #2a3163;
            font-size: 12px;
            font-weight: 400;
        }
    }
    @media (min-width: 351px) and (max-width: 480px) {
        body {
            padding-top: 85px;
            background-attachment: fixed;
            background-image: url("<?= $this->Url->image('errores.png')?>");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            margin: 0;
            margin-bottom: 30px;
            height: 100%;
        }

        .icoIni2 {
            background-image: url("<?= $this->Url->image('iconos/Inicio2.png')?>");
            background-repeat: no-repeat;
            background-size: contain;
            margin-left: 0;
            height: 40px;
            margin-bottom: 23px;
            width: 100%;
            z-index: 1;
        }

        .texto-error p {
            font-size: 13px;
            text-align: justify;
        }

        #menu-error {
            top: 85px;
        }

        .error-inicio {
            position: relative;
            top: 29px;
            left: -8px;
            color: #2a3163;
            font-size: 13px;
            font-weight: 400;
        }
    }
    @media (min-width: 481px) and (max-width: 764px) {
        body {
            padding-top: 85px;
            background-attachment: fixed;
            background-image: url("<?= $this->Url->image('errores.png')?>");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            margin: 0;
            margin-bottom: 30px;
            height: 100%;
        }
        .texto-error p {
            font-size: 12px;
        }

        #menu-error {
            top: 90px;
        }
    }
</style>
<body background="" class="fondo-error"">
<?php if($nombre_usuario != '') { ?>
    <?= $this->element('header') ?>
<?php } else { ?>
    <?= $this->element('headerinicio') ?>
<?php } ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >
            <ul class="sidebar-nav nav-pills nav-stacked" id="menu-error" style="position: fixed; z-index: 1; padding: 0; list-style: none; width: 50px;"  >
                <li>
                    <a style="width: 50px" href="<?= \Cake\Routing\Router::url(['controller' => 'Pages', 'action' => 'index'], true); ?>"  class="icoIni2" id="icoIni2">
                        <span class="error-inicio" style="">INICIO</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10" >
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </div>
</div>
</body>
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
</script>
</html>
