<!-- ------------------------------------------------------------------------------------- -->
<!-- ------------------------------------------------------------------------------------- -->
<!--         Este elemento puede ser utilizado para la busqueda si:                        -->
<!--          + Se tiene un campo de texto.                                                -->
<!--          + Se muestra o no un rango de fechas.                                        -->
<!--          + Se muestran hasta 4 selects.                                               -->
<!--          + Se muestra un checkbox para mostrar tanto activos como inactivos.          -->
<!-- ------------------------------------------------------------------------------------- -->
<!-- ------------------------------------------------------------------------------------- -->

<?php

echo $this->Html->script(['lib/moment.min']);

// Se obtienen los filtros de la busqueda
$session = (isset($_SESSION["tabla[$controller]"]))?$_SESSION["tabla[$controller]"]:[];

// Se obtiene el total de select a mostrar en la busqueda
$selects = count($select);

// Se obtienen los valores seleccionados en los select
$select_values = (isset($session['data'][$controller]['select']))
    ? $session['data'][$controller]['select']
    : [];

// Se define el numero de columnas para el campo de texto de la busqueda y se define el numero de columnas
// para los selects de la busqueda y se define el numero de columnas para el div que contiene los selects
// para la busqueda
$colText = '';
$colSelect = '';
$colDivSelect = '';

/*Se verifica si es enviada la opci�n para crear checkbox*/
$checkboxelement=array();
if(isset($checkbox)) $checkboxelement=$checkbox;
$check='';//inicializa la variable para atributo checked del elemento
if(isset($session['checked']))
    if($session['checked']) $check='checked';//se pasara a checked solo si existe la sesion con el valor de 1


switch ($selects) {
    case 1:
        $colSelect = 'col-md-12 col-sm-12';

        if(!$fecha['rangoFecha']) {
            $colText = 'col-md-4 col-sm-12';

            if(isset($widthSelect)) {
                if (!is_null($widthSelect)){
                    $colDivSelect = 'col-md-' . $widthSelect . ' col-sm-8';
                } else {
                    $colDivSelect = 'col-md-4 col-sm-8';
                }
            } else {
                $colDivSelect = 'col-md-4 col-sm-8';
            }
        } else {
            $colText = 'col-md-4 col-sm-6';

            if(isset($widthSelect)) {
                if (!is_null($widthSelect)){
                    $colDivSelect = 'col-md-' . $widthSelect . ' col-sm-8';
                } else {
                    $colDivSelect = 'col-md-3 col-sm-8';
                }
            } else {
                $colDivSelect = 'col-md-3 col-sm-8';
            }
        }
        break;
    case 2:
        $colSelect = 'col-md-6 col-sm-6';

        if(!$fecha['rangoFecha']) {
            $colText = 'col-md-4 col-sm-12';

            if(isset($widthSelect)) {
                if (!is_null($widthSelect)){
                    $colDivSelect = 'col-md-' . $widthSelect . ' col-sm-8';
                } else {
                    $colDivSelect = 'col-md-6 col-sm-8';
                }
            } else {
                $colDivSelect = 'col-md-6 col-sm-8';
            }
        } else {
            $colText = 'col-md-4 col-sm-6';

            if(isset($widthSelect)) {
                if (!is_null($widthSelect)){
                    $colDivSelect = 'col-md-' . $widthSelect . ' col-sm-8';
                } else {
                    $colDivSelect = 'col-md-3 col-sm-8';
                }
            } else {
                $colDivSelect = 'col-md-3 col-sm-8';
            }
        }
        break;
    case 3:
        $colSelect = 'col-md-4 col-sm-4';

        if(!$fecha['rangoFecha']) {
            $colText = 'col-md-4 col-sm-12';

            if(isset($widthSelect)) {
                if (!is_null($widthSelect)){
                    $colDivSelect = 'col-md-' . $widthSelect . ' col-sm-8';
                } else {
                    $colDivSelect = 'col-md-6 col-sm-8';
                }
            } else {
                $colDivSelect = 'col-md-6 col-sm-8';
            }
        } else {
            $colText = 'col-md-3 col-sm-6';

            if(isset($widthSelect)) {
                if (!is_null($widthSelect)){
                    $colDivSelect = 'col-md-' . $widthSelect . ' col-sm-8';
                } else {
                    $colDivSelect = 'col-md-4 col-sm-8';
                }
            } else {
                $colDivSelect = 'col-md-4 col-sm-8';
            }
        }
        break;
    case 4:
        $colSelect = 'col-md-3 col-sm-3';

        if(!$fecha['rangoFecha']) {
            $colText = 'col-md-3 col-sm-12';

            if(isset($widthSelect)) {
                if (!is_null($widthSelect)){
                    $colDivSelect = 'col-md-' . $widthSelect . ' col-sm-8';
                } else {
                    $colDivSelect = 'col-md-7 col-sm-8';
                }
            } else {
                $colDivSelect = 'col-md-7 col-sm-8';
            }
        } else {
            $colText = 'col-md-2 col-sm-6';

            if(isset($widthSelect)) {
                if (!is_null($widthSelect)){
                    $colDivSelect = 'col-md-' . $widthSelect . ' col-sm-8';
                } else {
                    $colDivSelect = 'col-md-5 col-sm-8';
                }
            } else {
                $colDivSelect = 'col-md-5 col-sm-8';
            }
        }
        break;
    default:
        $colSelect = '';
        if(!$fecha['rangoFecha']) {
            $colText = 'col-md-5 col-sm-10 col-xs-11';
            $colDivSelect = '';
        } else {
            $colText = 'col-md-5 col-sm-12';
            $colDivSelect = '';
        }
}


?>

<div id="search-box" class="clearfix no-clone" >
    <div class="form-search">
        <?= $this->Form->create($controller, ['id'=>'busqueda', 'url' => ['action' => (isset($acctions))?$acctions:'index']]); ?>
        <!-- Escritorio -->
        <div class="col-xs-12 col-md-4 col-sm-5 hidden-xs" style="padding-right:0px !important;">

            <?= $this->Form->control('SearchText', [
            'label' => false,
                'name' => $controller .'[search_text]',
            'type'=>'select',
            'div' => false,
            'options' => $options,
            'class'=> 'form-control txt-search inputSelectSearch',
            'placeholder' => "Busqueda por: ".$placeholder,
            'multiple'=>'multiple'],['div'=>['id'=>'etiqueta']]);?>

        </div>

        <!-- Movil -->
        <div class=" col-xs-12 hidden-sm hidden-md hidden-lg" style=" margin-bottom: 5px;padding-right:0px !important;">
            <div class="input-group " >
                <?= $this->Form->control('SearchText2', [
                    'label' => false,
                    'name' => $controller .'[search_text2]',
                    'type'=>'select',
                    'div' => false,
                    'options' => $options,
                    'class'=> 'form-control txt-search inputSelectSearch2',
                    'placeholder' => "Busqueda por: ".$placeholder,
                    'multiple'=>'multiple'],['div'=>['id'=>'etiqueta2']]);?>
                <span   class="input-group-addon " style="background-color: #F2F6E9; color:white;padding: 0px;box-shadow: inset -2px 1px 1px rgba(0,0,0,.075);border-top-right-radius: 50%;border-bottom-right-radius: 50%;border: 0px;border: 1px solid #dfe6ce;">
                <div id="btn-movil" style=" border-radius: 50%;background-color: #a2c54c; height: 30px; width: 30px;    padding-top: 7px;cursor:pointer;">
                          <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </span>
            </div>
        </div>

        <!-- Si existe al menos un select para mostrar en la busqueda -->
        <?php if($selects > 0) { ?>


                <?php foreach ($select as $key => $value) { ?>


                        <?php if(!isset($value['same_table'])) { ?>
                            <div class="  col-xs-12 col-md-4 col-sm-3">
                            <?= $this->Form->input($key, [
                                'name' => $controller .'[select]['. $value['campo'] .']',
                                'empty' => isset($value['empty']) ? $value['empty'] : 'Seleccionar',
                                'label' => false,
                                'options'=> $value['opciones'],
                                'class'=>"form-control list-search",
                                'value' => isset($select_values[$value['campo']])
                                    ? $select_values[$value['campo']]
                                    : ""
                            ]) ?>
                    </div>

                        <?php } else { ?>
                        <div class="  col-xs-12 col-md-2 col-sm-3">
                            <?php $campoArray = explode('_', $key); ?>
                            <?= $this->Form->input($value['campo'], [
                                'name' => $controller .'[select]['. $value['campo'] .']',
                                'empty' => isset($value['empty']) ? $value['empty'] : 'Seleccionar',
                                'label' => false,
                                'options'=> $value['opciones'],
                                'class'=>"form-control list-search ",
                                'value' => isset($select_values[$value['campo']])
                                    ? $select_values[$value['campo']]
                                    : ""
                            ]) ?>
                        </div>
                    <div class="clearfix col-xs-12 hidden-sm hidden-md hidden-lg"  style=" margin-bottom: 5px;"></div>
                        <?php } ?>

                <?php } ?>


        <?php } ?>
        <!-- Checkbox para mostrar registros tanto activos como inactivos -->
        <?php
        echo $this->Form->input("trash",[
            'type'=>'hidden',
            'value'=>$inactivo,
            'name'=>$controller .'[trash]'
        ]);
          ?>

        <div class="col-sm-2 col-xs-1 col-md-1 col-sm-1 col-btn-search   hidden-xs ">
            <button class="btn btn-default btn-search" type="submit"><span><i class="fa fa-search icon-search"></i></span></button>
        </div>

        <!-- Campos de texto ocultos para nombre del controlador y nombre de campos para la busqueda en campo de texto -->
        <?= $this->Form->hidden('campos', ['value' => $campos, 'name' => $controller . '[parametro]']); ?>
        <?= $this->Form->hidden('controller', ['value' => $controller, 'name' => $controller . '[controller]']); ?>
        <?= $this->Form->hidden('hsearch'); ?>


        <?= $this->Form->end(); ?>
    </div>
    
    <!-- Se muestra el boton, si se ha realizado una busqueda -->
    <?php if($session) { ?>
        <div class="col-sm-2 col-btn-limpiar hidden-xs ">
            <?= $this->Html->link($this->Html->tag('span',__(' Limpiar',true), ['class' => '']),
                [
                'action' => (isset($limpiar))?$limpiar:'vertodos'
                 ], [
                'class' => 'btn clean-search pull-left',
                'escape' => false
            ]); ?>
        </div>
        <div class="col-sm-2 col-btn-limpiar hidden-sm hidden-md hidden-lg">
            <?= $this->Html->link($this->Html->tag('span',__(' Limpiar',true), ['class' => '']),
                [
                    'action' => (isset($limpiar))?$limpiar:'vertodos'
                ], [
                    'class' => 'btn clean-search pull-left',
                    'escape' => false,
                    'style'=>'margin-top: 10px;margin-left: 15px; width: 100%; font-weight: 600;',
                ]); ?>
        </div>
    <?php } ?>


</div>
            <div class="clearfix  " style="margin-bottom: 10px;" ></div>
<script>
    var hoy = new Date();
    var fechaFormulario = new Date(hoy);
    $(function () {



    });

    $(document).on("ready", function()
    {

        <?php
        if(isset($_SESSION["tabla[ViewGaleria]"]['etiquetas'])){?>
        $('.inputSelectSearch').val(<?=isset($_SESSION["tabla[ViewGaleria]"]['etiquetas'])?json_encode($_SESSION["tabla[ViewGaleria]"]['etiquetas']):"";?>).trigger('change.select2');
        <?php
        }
        ?>


        $('.inputSelectSearch').select2({
            language: "es",
            theme: "classic",
            tags: true,
            placeholder: "Buscar por: Etiquetas"
        });

        <?php
        if(isset($_SESSION["tabla[ViewGaleria]"]['etiquetas'])){?>
        $('.inputSelectSearch2').val(<?=isset($_SESSION["tabla[ViewGaleria]"]['etiquetas'])?json_encode($_SESSION["tabla[ViewGaleria]"]['etiquetas']):"";?>).trigger('change.select2');
        <?php
        }
        ?>


        $('.inputSelectSearch2').select2({
            language: "es",
            theme: "classic",
            tags: true,
            placeholder: "Buscar por: Etiquetas"
        });

        $(document).on("change", "#estados2", function()
        {
            $("#viewdataaccion-id-cestado").val($(this).val());

        });

        $(document).on("click", "#btn-movil", function(){

            $("#searchtext").val($  ("#searchtext2").val());
                  $(".btn-search").click();
        });

    });

</script>