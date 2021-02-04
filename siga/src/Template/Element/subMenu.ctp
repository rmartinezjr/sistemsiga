<ul class="sidebar-nav nav-pills nav-stacked sidebar-nav-subMenu "  style=" padding-bottom: 150px;background-color: #303941;position: inherit;width: 196px;" >
    <?php $real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base')."/";  ?>
    <?php $dirIcon='//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/img/iconos/'; ?>

    <?php
    foreach ($_SESSION['menus']['nivel1'] as $items)
    {
        if($items->sum == 0)
        {
            echo '<li  class="items'.$items->id.' txtIcoPla hidden-items no_responsi " id="items'.$items->id.'"  data-items="'.$items->id.'">';
        }

        else if($items->sum > 0)
        {
            echo '<li  class="items'.$items->id.' txtIcoPla hidden-items " id="items'.$items->id.'"  data-items="'.$items->id.'">';
        }
        echo '<a style="background-image: url('.$this->Url->image($dirIcon.$items->filename).')"  class="icoPla2" >
<span class="txt-SubIco">';
        ?>

        <?= h((strlen($items->alias) > 19) ? substr($items->alias,0,19   ) . "..." : $items->alias) ?>
        <?php
        echo'</span></a></li>';
    }
    $itemsNivel2="";

    foreach ($_SESSION['menus']['nivel2'] as $items2)
    {
        $itemsNivel2=$items2->id;
        //variable para conocer el id de nivel de nombre Recursos
        $idrecurso=null;
        if(strcmp($items2->alias,'Recursos')==0) $idrecurso=$items2->id;
        if(!empty($idrecurso)){//s�lo se realizara si la variable tiene el id indicado en base a la comparacion del nombre alias Recursos
            if(isset($_SESSION['structWf_count'])) {
                if($_SESSION['structWf_count']>0){//si existen estructuras publicadas y con workflows asignados y vinculo a 1
                    if($items2->sum==0)
                    {
                        echo '<li  class="items'.$items2->menu_id.' hidden-items no_responsi" style="float: left;margin-bottom: -7px;">';
                    }

                    else if($items2->sum>0)
                    {
                        echo '<li  class="items'.$items2->menu_id.' hidden-items " style="float: left;margin-bottom: -10px;">';
                    }

                    echo '<hr>';
                    if(strlen($items2->alias) > 19) {
                        echo '<a  data-placement="right" title="'.$items2->alias.'" style="background-image: url(' . $this->Url->image($items2->icon) . '); margin-bottom: 15px;"  class="icoPla2">
                <span class="txt-SubIco">' . trim(substr($items2->alias, 0, 19)) . '...' . '</span></a><ul class="menuNivel3 items' . $items2->menu_id . ' hidden-items" >';
                    }
                    else
                    {
                        echo '<a  style="background-image: url(' . $this->Url->image($items2->icon) . '); margin-bottom: 15px;"  class="icoPla2">
                <span class="txt-SubIco">' . $items2->alias. '</span></a><ul class="menuNivel3 items' . $items2->menu_id . ' hidden-items" >';

                    }
                    foreach ($_SESSION['menus']['nivel3'] as $itemsr)
                    {
                        if($idrecurso==$itemsr->menu_id) $items=$itemsr;//se obtienen las propiedades del menu Recursos
                    }
                    foreach($_SESSION['structWf_records'] as $key){
                        echo $items->movil;
                        if($items->m['movil']=='0')
                        {
                            echo '<li  class="items'.$items->menu_id.' itemsNivel3 no_responsi" >';
                        }

                        else if($items->m['movil']=='1')
                        {
                            echo '<li  class="items'.$items->menu_id.' itemsNivel3" >';
                        }
                        echo '<a id="'.$items->m['id'].'_1" data-placement="right" title="'.$key['nombre'].'" href="'.$real_url.$items->m['modelo'].'/index/'.$key['id'].'" class="itemsa" style="padding: 6px 0px 6px 5px; width: 160px;margin: 1px 0px 1px 0px;">';

                        if(strlen($key['nombre']) > 24){
                            echo '<span class="txt-Nivel3">' .  trim(substr($key['nombre'],0,24)).'...'. '</span></a></li>';
                        }
                        else
                        {
                            echo '<span class="txt-Nivel3">' .$key['nombre']. '</span></a></li>';
                        }
                    }
                    echo '</ul></li>';
                }
            }
        }else{
            if($items2->sum==0)
            {
                echo '<li  class="items'.$items2->menu_id.' hidden-items no_responsi" style="float: left;margin-bottom: -7px;">';
            }

            else if($items2->sum>0)
            {
                echo '<li  class="items'.$items2->menu_id.' hidden-items " style="float: left;margin-bottom: -10px;">';
            }

            echo '<hr>';
            if(strlen($items2->alias) > 19) {
                echo '<a  data-placement="right" title="'.$items2->alias.'" style="background-image: url(' . $this->Url->image($items2->icon) . ');margin-bottom: 15px;"  class="icoPla2">
           <span class="txt-SubIco">' . trim(substr($items2->alias, 0, 19)) . '...' . '</span></a><ul class="menuNivel3 items' . $items2->menu_id . ' hidden-items" >';
            }
            else{
                echo '<a  style="background-image: url(' . $this->Url->image($items2->icon) . ');margin-bottom: 15px;"  class="icoPla2">
           <span class="txt-SubIco">' .$items2->alias . '</span></a><ul class="menuNivel3 items' . $items2->menu_id . ' hidden-items">';
            }
            foreach ($_SESSION['menus']['nivel3'] as $items)
            {
                if($itemsNivel2==$items->menu_id) {
                    echo $items->movil;
                    if ($items->m['movil'] == '0') {
                        echo '<li class="items'.$items->menu_id.' itemsNivel3 no_responsi">';
                    } else if ($items->m['movil'] == '1') {
                        echo '<li class="items'.$items->menu_id.' itemsNivel3">';
                    }
                    echo '<a  id="'.$items->m['id'].'_2" data-placement="right" title="'.$items->alias.'"  href="' . $real_url . $items->m['modelo'] .'" class="itemsa" style="padding: 6px 0px 6px 5px; width: 160px; margin: 1px 0px 1px 0px;">';
                    if(strlen($items->alias) > 24){
                        echo '<span class="txt-Nivel3">' . trim(substr($items->alias,0,24)).'...'. '</span></a></li>';
                    }
                    else
                    {
                        echo '<span class="txt-Nivel3">' .$items->alias. '</span></a></li>';
                    }

                }
            }
            echo '</ul></li>';

        }
    }
    ?>
</ul>
