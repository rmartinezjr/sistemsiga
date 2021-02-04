<?php
use Cake\Core\Configure;
$cakeDescription = 'CakePHP: the rapid development PHP framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>

    <?= $this->Html->meta('icon') ?>
    
</head>
<body class="home">

<div class="row">
    <div class="columns large-12">
        <?= $this->fetch('content') ?>
    </div>
</div>
</body>

</html>