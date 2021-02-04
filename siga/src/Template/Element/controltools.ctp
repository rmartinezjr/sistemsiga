<?php
    $real_url = \Cake\Routing\Router::url(['action' => 'index'], true) .'/';

    $cont=0;
    $i=0;
    $extras = [];
    if(!isset($multiple)) {
        foreach ($controltools as $btn){
            if($cont<=2){
                if($btn['funcion']=="imprimir"){    ?>
                    <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                <?php                   }
                else if ($btn['funcion'] == "edit") {
                    if(isset($id)){

                        ?>
                        <a class="btn btn-sistem <?= $btn['class'] ?>"
                           href="<?= $real_url . $btn['funcion'] . '/' . $id ?>"><span><?= $btn['alias'] ?></span><i
                                    class="fa <?= $btn['icon'] ?> icono"></i></a>
                    <?php }
                }
                else{  ?>
                    <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                <?php                   }
            }else{
                $extras[$i]=$btn;
                $extras[$i]['url'] = $real_url;
                $i++;
            }
            $cont++;
        }
    } else {
        foreach ($controltools as $btn){

            if($btn['funcion'] != $multiple['action']) {
                if($cont <= 2){
                    if($btn['funcion']=="imprimir"){    ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php  }

                    else{  ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                   }
                }else{
                    $extras[$i]=$btn;
                    $extras[$i]['url'] = $real_url;
                    $i++;
                }
                $cont++;
            } else {
                foreach ($multiple['buttons'] as $button){
                    if($cont <= 2) { ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" style="background-color: <?=$button['color']?>" href="<?= $button['url'].$btn['funcion'] ?>"><span><?=$button['label']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                <?php } else {
                        $extras[$i]=$btn;
                        $extras[$i]['url'] = $button['url'];
                        $extras[$i]['alias'] = $button['label'];
                        $i++;
                    }
                    $cont++;
                }
            }
        }
    }

    if(count($extras)>0) {
        echo "<span class='list-controltool'><a>&nbsp;</a><div class='content-list'><ul class='lista-control'>";
        $y=0;

        foreach ($extras as $item) {
            if($y==0 && count($extras) > 1) { ?>
                <li class="border1"><a href="<?=$item['url'].$item["funcion"]?>"><?= $item['alias'] ?></a></li>
            <?php                   }else if($y == (count($extras) - 1) && count($extras) > 1) {   ?>
                <li class="border2"><a href="<?=$item['url'].$item["funcion"]?>"><?= $item['alias'] ?></a></li>
            <?php                        } else if(count($extras) == 1) { ?>
                <li class="border3"><a href="<?=$item['url'].$item["funcion"]?>"><?= $item['alias'] ?></a></li>
            <?php                   } else { ?>
                <li><a href="<?=$item['url'].$item["funcion"]?>"><?= $item['alias'] ?></a></li>
                <?php
            }
            $y++;
        }

        echo "</ul></div></span>";
    }

?>