<?php
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';

// Si hay flujo de trabajo
if (!empty($acciones)) {

    if( count($acciones) > 2)
    {
        foreach ($acciones as $key): ?>
        <button class="btn btn-sistem btn-<?= $key['nombre'] ?>"
                style="background-color:<?= $key['color_fondo'] ?>; color:<?= $key['color_texo'] ?>;"
                onclick="setTypesave(<?= $key['id'] ?>)"><span><?= $key['nombre'] ?></span><i
                class="fa <?= $key['icon'] ?> icono"></i></button>
    <?php endforeach;
    }
    elseif (count($acciones) <= 2)
    {

        //Obtengo el primer element del arreglo
        switch (count($acciones)) {
            case 1:
                $array= array_splice($controltools,-2);
                break;
            case 2:
                $array= array_splice($controltools,-1);
                break;
        }

        foreach ($acciones as $key): ?>
            <button class="btn btn-sistem btn-<?= $key['nombre'] ?>"
                    style="background-color:<?= $key['color_fondo'] ?>; color:<?= $key['color_texo'] ?>;"
                    onclick="setTypesave(<?= $key['id'] ?>)"><span><?= $key['nombre'] ?></span><i
                    class="fa <?= $key['icon'] ?> icono"></i></button>
        <?php endforeach;
        foreach ($array as $btn)
        {
            if($btn['funcion']==="imprimir"){   ?>
                <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
            <?php                }
            else  if($btn['funcion']==='index'){?>
                <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.'lista/'.$_SESSION["id"];?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
            <?php } else if ($btn['funcion'] === "edit") { ?>
                <a class="btn btn-sistem <?= $btn['class'] ?>"
                   href="<?= $real_url . $btn['funcion'].'/'.$regaccion->id?>"><span><?= $btn['alias'] ?></span><i
                            class="fa <?= $btn['icon'] ?> icono"></i></a>
            <?php }
            else if ($btn['funcion'] === "add") { ?>
                <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']."/".$_SESSION["id"]?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>

            <?php }    else {
                ?>
                <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
            <?php                   }
        }

    }

   // Submenu
    echo "<span class='list-controltool'><a>&nbsp;</a><div class='content-list'><ul class='lista-control' style='min-width: 180px;'>";
    $leng=count($controltools)-1;

    foreach ($controltools as $key => $btn) {

        if (count($controltools) > 1) {
        if ($key == 0) {

            if ($btn['funcion'] === "imprimir") { ?>
                <li class="border1"><a class=""
                                       onClick="window.open('<?= $real_url . $btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i
                                class="fa <?= $btn['icon'] ?> icono"></i></a></li>
            <?php } else if ($btn['funcion'] === 'index') { ?>
                <li class="border1"><a class=" "
                                       href="<?= $real_url . 'lista/' . $_SESSION["id"]; ?>"><?= $btn['alias'] ?></a>
                </li>
            <?php } else if ($btn['funcion'] === "edit") { ?>
                <li class="border1"><a
                            href="<?= $real_url . $btn['funcion'] . '/' . $regaccion->id ?>"><?= $btn['alias'] ?></a>
                </li>
            <?php } else if ($btn['funcion'] === "add") { ?>
                <li class="border1"><a
                            href="<?= $real_url . $btn['funcion'] . "/" . $_SESSION["id"] ?>"><?= $btn['alias'] ?></a>
                </li>

            <?php } else {
                ?>
                <li class="border11"><a href="<?= $real_url . $btn['funcion'] ?>"><?= $btn['alias'] ?></a></li>
            <?php }

        } else if ($leng == $key) {

            if ($btn['funcion'] === "imprimir") { ?>
                <li class="border2"><a class=""
                                       onClick="window.open('<?= $real_url . $btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i
                                class="fa <?= $btn['icon'] ?> icono"></i></a></li>
            <?php } else if ($btn['funcion'] === 'index') { ?>
                <li class="border2"><a class=" "
                                       href="<?= $real_url . 'lista/' . $_SESSION["id"]; ?>"><span><?= $btn['alias'] ?></span></a>
                </li>
            <?php } else if ($btn['funcion'] === "edit") { ?>
                <li class="border2"><a
                            href="<?= $real_url . $btn['funcion'] . '/' . $regaccion->id ?>"><span><?= $btn['alias'] ?></span></a>
                </li>
            <?php } else if ($btn['funcion'] === "add") { ?>
                <li class="border2"><a
                            href="<?= $real_url . $btn['funcion'] . "/" . $_SESSION["id"] ?>"><span><?= $btn['alias'] ?></span></a>
                </li>

            <?php } else {
                ?>
                <li class="border2"><a href="<?= $real_url . $btn['funcion'] ?>"><span><?= $btn['alias'] ?></span></a>
                </li>
            <?php }
        } else {
            if ($btn['funcion'] === "imprimir") { ?>
                <li><a class=""
                       onClick="window.open('<?= $real_url . $btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i
                                class="fa <?= $btn['icon'] ?> icono"></i></a></li>
            <?php } else if ($btn['funcion'] === 'index') { ?>
                <li><a class=" "
                       href="<?= $real_url . 'lista/' . $_SESSION["id"]; ?>"><span><?= $btn['alias'] ?></span></a>
                </li>
            <?php } else if ($btn['funcion'] === "edit") { ?>
                <li><a
                            href="<?= $real_url . $btn['funcion'] . '/' . $regaccion->id ?>"><span><?= $btn['alias'] ?></span></a>
                </li>
            <?php } else if ($btn['funcion'] === "add") { ?>
                <li><a
                            href="<?= $real_url . $btn['funcion'] . "/" . $_SESSION["id"] ?>"><span><?= $btn['alias'] ?></span></a>
                </li>

            <?php } else {
                ?>
                <li><a href="<?= $real_url . $btn['funcion'] ?>"><span><?= $btn['alias'] ?></span></a></li>
            <?php }
        }
    }
    else{

        if ($btn['funcion'] === "imprimir") { ?>
            <li class="border2"><a class=""
                                   onClick="window.open('<?= $real_url . $btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i
                            class="fa <?= $btn['icon'] ?> icono"></i></a></li>
        <?php } else if ($btn['funcion'] === 'index') { ?>
            <li class="border2"><a class=" "
                                   href="<?= $real_url . 'lista/' . $_SESSION["id"]; ?>"><span><?= $btn['alias'] ?></span></a>
            </li>
        <?php } else if ($btn['funcion'] === "edit") { ?>
            <li class="border2"><a
                        href="<?= $real_url . $btn['funcion'] . '/' . $regaccion->id ?>"><span><?= $btn['alias'] ?></span></a>
            </li>
        <?php } else if ($btn['funcion'] === "add") { ?>
            <li style="border-radius: 6px 6px 6px 6px;"><a href="<?= $real_url . $btn['funcion'] . "/" . $_SESSION["id"] ?>"><?= $btn['alias'] ?></a>
            </li>

        <?php } else {
            ?>
            <li class="border2"><a href="<?= $real_url . $btn['funcion'] ?>"><span><?= $btn['alias'] ?></span></a>
            </li>
        <?php }
    }
    }
    echo "</ul></div></span>";
}
else {?>
    <?php foreach ($controltools as $btn){
        if($btn['funcion']==="imprimir"){   ?>
            <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
        <?php                }
        else  if($btn['funcion']==='index'){?>
            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.'lista/'.$_SESSION["id"];?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
        <?php } else if ($btn['funcion'] === "edit") { ?>
            <a class="btn btn-sistem <?= $btn['class'] ?>"
               href="<?= $real_url . $btn['funcion'].'/'.$regaccion->id?>"><span><?= $btn['alias'] ?></span><i
                    class="fa <?= $btn['icon'] ?> icono"></i></a>
        <?php }
        else if ($btn['funcion'] === "add") { ?>
            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']."/".$_SESSION["id"]?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>

        <?php }    else {
            ?>
            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
        <?php                   }
    }


}?>