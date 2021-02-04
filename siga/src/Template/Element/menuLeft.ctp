<?php $dirIcon='//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/img/iconos/'; ?>
<div style=" height: 100%; padding-top: 10px;">
    <div style="width: 73px; height: 50%; padding-top: 10px;" class="divSubmenu">
<ul class="sidebar-nav nav-pills nav-stacked" id="menu" style="position: fixed; margin-top: 70px;z-index: 1;
    padding: 0;
    list-style: none; width: 30px;"  >
    <li>
        <a style="width: 30px;"  data-placement="right" title="Inicio" data-toggle="tooltip" href="<?= \Cake\Routing\Router::url(['controller' => 'Pages', 'action' => 'index'], true); ?>"  class="icoIni " id="icoIni2"></a>
    </li>
    <?php
    $menus=$_SESSION['menus'];
    foreach ($menus['nivel1'] as $items)
    {
        if($items->posicion == 0)
        {
            if ($items->sum == 0) {
                echo '<li class="no_responsi "><a href="#"  data-placement="right" title="'.$items->alias.'" style="width: 30px;background-image: url(' . $this->Url->image($dirIcon.$items->filename) . ')"  class="icoPla itemsNivel1  menu-toggle-2 _'.$items->id.'" id="' . $items->id . '"
data-id="'.$items->id.'"></a></li>';
               // echo '<li class="no_responsi">'.$this->Html->image(array('controller'=>'Files','action'=>'show',$items->id),['style' =>'width: 30px;','class'=>'icoPla  menu-toggle-2','id'=>$items->id]).'</li>';

            } else if ($items->sum > 0) {
                echo '<li><a href="#"  data-placement="right" title="'.$items->alias.'" style="width: 30px;background-image: url(' . $this->Url->image($dirIcon.$items->filename) . ')"  class="icoPla itemsNivel1  menu-toggle-2 _'.$items->id.'" id="' . $items->id . '" data-id="'.$items->id.'"></a></li>';
               // onmouseover="this.src='URL de la imagen DOS';" onmouseout="this.src='URL de la imagen UNO';"
                //echo '<li>'.$this->Html->image(array('controller'=>'Files','action'=>'show',$items->id,'1'),['style' =>'width: 30px;','class'=>'icoPla  menu-toggle-2','id'=>$items->id]).'</li>';
            }
        }
    }
    ?>
      <!-- Menu ayuda y admin visible en movil -->
    <?php
   $menus=$_SESSION['menus'];
    foreach ($menus['nivel1'] as $items)
    {
        if($items->posicion == 1)
        {
            if ($items->sum == 0) {
                echo '<li class="no_responsi menuTool "  style="display: none"><a href="#"   data-placement="right" title="'.$items->alias.'"  style="width: 30px;background-image: url('.$this->Url->image($dirIcon.$items->filename).')"  class="icoPla itemsNivel1 menu-toggle-2 _'.$items->id.'" id="'.$items->id.'1" data-id="'.$items->id.'"></a></li>';
            } else if ($items->sum > 0) {
                echo '<li class="menuTool" style="display: none"><a href="#"   data-placement="right" title="'.$items->alias.'"  style="width: 30px;background-image: url('.$this->Url->image($dirIcon.$items->filename).')"  class="icoPla itemsNivel1 menu-toggle-2 _'.$items->id.'" id="'.$items->id.'1" data-id="'.$items->id.'"></a></li>';
            }
        }
    }
    ?>
</ul>
<div class="subMenu" style="overflow-y: auto;">
   <?= $this->element('subMenu') ?>
</div>
</div>
<div  style="    width: 73px;
    height: 50%;
    padding-top: 10px;
    position: fixed;
    bottom: -150px;" class="menuBottom">
    <div style="/*position: absolute;
    bottom: 150px;
    height: 170px;*/">
    <ul class="sidebar-nav nav-pills nav-stacked menuBottom" style="position: inherit; margin: 0;
    padding: 0;
    list-style: none; width: 30px;" >
        <?php
        $menus=$_SESSION['menus'];
        foreach ($menus['nivel1'] as $items)
        {
        if($items->posicion == 1)
        {
            if ($items->sum == 0) {
           // echo '<li class="no_responsi"><a href="#"   data-placement="right" title="'.$items->alias.'" style="width: 30px;background-image: url(' . $this->Url->image($dirIcon.$items->filename) . ')"  class="icoPla itemsNivel1 menu-toggle-2 " id="' . $items->id . '"></a></li>';
            echo '<li class="no_responsi"><a href="#"  data-placement="right" title="'.$items->alias.'" style="width: 30px;background-image: url('.$this->Url->image($dirIcon.$items->filename).')"  class="icoPla itemsNivel1 menu-toggle-2 _'.$items->id.'" id="'.$items->id.'" data-id="'.$items->id.'"></a></li>';

            } else if ($items->sum > 0) {
            echo '<li><a href="#"   data-placement="right" title="'.$items->alias.'"  style="width: 30px;background-image: url('.$this->Url->image($dirIcon.$items->filename).')"  class="icoPla itemsNivel1 menu-toggle-2 _'.$items->id.'" id="'.$items->id.'" data-id="'.$items->id.'"></a></li>';
            }
        }
        }
        ?>

    </ul>
    </div>
</div>
</div>
<!-- Script para notificaciones en tiempo real -->
