<?php
$real_url = \Cake\Routing\Router::url(['action' => 'index'], true) .'/';

$cont=0;
$i=0;
$extras = [];
foreach ($controltools as $btn){
    if($cont<=2){
        if($btn['funcion']=="imprimir"){    ?>
            <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
        <?php                   }
        else if($btn['funcion']=="add") {
            ?>
            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']."/".$_SESSION["id"]?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
            <?php
        }
        else{  ?>
            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
        <?php                   }
    }

    else{
        $extras[$i]=$btn;
        $i++;
    }
    $cont++;
}
if(count($extras)>0) {
    echo "<span class='list-controltool'><a>&nbsp;</a><div class='content-list'><ul class='lista-control'>";
    $y=0;

    foreach ($extras as $item) {
        if($y==0 && count($extras) > 1) { ?>
            <li class="border1"><a href="<?=$real_url.$item["funcion"]?>"><?= $item['alias'] ?></a></li>
        <?php                   }else if($y == (count($extras) - 1) && count($extras) > 1) {   ?>
            <li class="border2"><a href="<?=$real_url.$item["funcion"]?>"><?= $item['alias'] ?></a></li>
        <?php                        } else if(count($extras) == 1) { ?>
            <li class="border3"><a href="<?=$real_url.$item["funcion"]?>"><?= $item['alias'] ?></a></li>
        <?php                   } else { ?>
            <li><a href="<?=$real_url.$item["funcion"]?>"><?= $item['alias'] ?></a></li>
            <?php
        }
        $y++;
    }

    echo "</ul></div></span>";
}
?>