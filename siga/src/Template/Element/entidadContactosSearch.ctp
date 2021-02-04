<?php
// Se obtienen los filtros de la busqueda
$session = (isset($_SESSION["tabla[$controller]"]))?$_SESSION["tabla[$controller]"]:[];

// Se obtiene el estado que ha sido seleccionado
$select_value = (isset($session['data']['estado']))
    ? $session['data']['estado']
    : '';
?>
<div class="clearfix entidadContactos-searchBox no-clone">
    <div class="form-search">
        <?= $this->Form->create($controller, ['id' => $formId, 'class' => $formId, /*'url' => ['controller' => 'entidadcontactos', 'action' => 'index']*/]); ?>

        <div class="col-md-6 col-sm-8 col-xs-12 col-search-text">
            <?= $this->Form->input('SearchText', array(
                'label' => false,
                'name' => $controller .'[search_text]',
                'value' => isset($session['search_text'])?$session['search_text']:"",
                'div' => false,
                'class'=>'form-control txt-search',
                'placeholder' => "Busqueda por: ".$placeholder
            )); ?>
        </div>
        <div class="col-md-2 col-sm-3 col-xs-12 col-search-estado">
            <?= $this->Form->input('estado_id', [
                'name' => $controller .'[select]['. $select['Cestados']['campo'] .']',
                'empty' => isset($select['Cestados']['empty']) ? $select['Cestados']['empty'] : 'Seleccionar',
                'label' => false,
                'options'=> $select['Cestados']['opciones'],
                'class'=>"form-control list-search",
                'value' => $select_value
            ]) ?>
        </div>
        <?= $this->Form->input("trash",[
            'type'=>'hidden',
            'value'=>$trash,
            'name'=>$controller .'[trash]'
        ]); ?>
        <div class="col-md-1 col-sm-1 col-xs-2 col-search-button">
            <button id="search<?= $controller ?>" class="btn btn-default btn-search" type="submit"><span><i class="fa fa-search icon-search"></i></span></button>
            <!-- Campos de texto ocultos para nombre del controlador y nombre de campos para la busqueda en campo de texto -->
            <?= $this->Form->hidden('campos', ['id' => 'parametro', 'value' => $campos, 'name' => $controller . '[parametro]']); ?>
            <?= $this->Form->hidden('controller', ['value' => $controller, 'name' => $controller . '[controller]']); ?>
            <?= $this->Form->end(); ?>
        </div>
        <div class="col-md-1 col-sm-1 col-xs-2 col-search-button col-refresh">
            <!-- Boton para refrescar tabla y mostrar todos los registros -->
            <!--button id="refresh-<?= $controller ?>" class="btn btn-default refresh-button" type="submit" data-toggle="tooltip" data-placement="top" title="Refrescar tabla"><span><i class="fa fa-refresh icon-search"></i></span></button-->
            <button id="refresh-<?= $controller ?>" class="btn refresh-button" type="submit"><span style="margin-right: 6px"><i class="fa fa-refresh icon-search"></i></span> Ver todos</button>
        </div>
        <!-- Se muestra el boton, si se ha realizado una busqueda -->
        <?php if($session) { ?>
            <div class="col-md-2 col-sm-2 col-xs-2 col-search-todos">
                <?= $this->Html->link($this->Html->tag('span',__(' Limpiar',true), ['class' => '']), [
                    'action' => 'vertodos'
                ], [
                    'class' => 'btn clean-search pull-left',
                    'escape' => false
                ]); ?>
            </div>
        <?php } ?>
    </div>
</div>