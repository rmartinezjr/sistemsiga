<?php
echo $this->Html->link(__('Editar'),$rutaEdit,['class'=>'btn btn-primary'
    ,'style'=>'margin-right:10px; width:95px;font-weight: 700;font-size: 1em; text-decoration: none;']);
echo $this->Html->link(__($tituloBottom),['action' => 'index'],['class'=>'btn btn-default',
    'style'=>'font-weight: 700;font-size: 1em; text-decoration: none;']);
?>