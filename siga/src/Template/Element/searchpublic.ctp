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
echo $this->Html->css(['lib/bootstrap-datetimepicker.min']);
echo $this->Html->script(['lib/moment.min', 'lib/datetimepicker']);

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
        $colSelect = 'col-md-12 col-sm-12 ';

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
            $colText = 'col-md-4 col-sm-6 col-xs-6';

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
// Se obtiene la fecha actual
$fechaActual = date('d/m/Y');

// Se obtiene la fecha ingresada en el campo desde para la busqueda
$fdesde = (isset($session['desde'])) ? $session['desde'] : '';

// Se obtiene la fecha ingresada en el campo hasta para la busqueda
$fhasta = (isset($session['hasta'])) ? $session['hasta'] : '';
?>

<div id="search-box" class="clearfix no-clone" >
    <div class="form-search">
        <?= $this->Form->create($controller, ['id'=>'busqueda', 'url' => ['action' => (isset($acctions))?$acctions:'indexpublic']]); ?>
        <!-- Escritorio -->
        <div class="<?= $colText ?> col-sm-4  col-xs-12 col-md-4 hidden-xs" style="padding-right:15px !important;">
            <?= $this->Form->input('SearchText', array(
                'label' => false,
                'name' => $controller .'[search_text]',
                'value' => isset($session['search_text'])?$session['search_text']:"",
                'div' => false,
                'class'=>'form-control txt-search',
                'placeholder' => "Búsqueda por: ".$placeholder
            )); ?>
        </div>
        <!-- Movil -->
        <div class=" col-xs-12 hidden-sm hidden-md hidden-lg" style=" margin-bottom: 5px;padding-right:0px !important;">
            <div class="input-group " >
            <input name="<?= $controller .'[search_text2]' ?>" class="form-control txt-search" style="background-image: none;border: solid 0px #ffffff;z-index: 0;padding-left: 10px;" placeholder="Búsqueda por: <?=$placeholder?>" id="searchtext2" value="<?= isset($session['search_text'])?$session['search_text']:"" ?>" type="text">
            <span   class="input-group-addon " style="background-color: #F2F6E9; color:white;padding: 0px;box-shadow: inset -2px 1px 1px rgba(0,0,0,.075);border-top-right-radius: 50%;border-bottom-right-radius: 50%;border: 0px;">
                <div id="btn-movil" style=" border-radius: 50%;background-color: #a2c54c; height: 30px; width: 30px;    padding-top: 7px;cursor:pointer;">
                          <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </span>
            </div>
        </div>

        <!-- Si se muestra en el filtro el rango de fechas -->
        <?php if($fecha['rangoFecha']) { ?>
            <div class="hidden-xs <?= ($fecha['custom']) ? 'col-md-2' : 'col-md-4' ?> col-sm-6 col-xs-12" style="padding-left: 0;padding-right: 0px;">
                <div class="col-sm-6 col-xs-12" style="<?= ($fecha['custom']) ? 'padding: 0; width:auto;' : '' ?> padding-right:0px !important; ">
                    <?= $this->Form->hidden('fecha', ['value' => $fecha['campo'], 'name' => $controller . '[fecha]']); ?>
                    <?= $this->Form->hidden('fechahora', ['value' => $fecha['datetime'], 'name' => $controller . '[fechahora]']); ?>
                    <div class="form-group search-date" style="margin-bottom: 0 !important;">
                        <!--label>Desde</label-->
                        <div class='input-group date'>
                            <input type='text' id='desde' style="cursor: pointer; padding-right: 0; height:30px; background: #F2F6E9; border: solid 1px #ffffff; color: #555;" class="form-control txt-date" name="<?= $controller .'[desde]' ?>" value="<?= $fdesde ?>" readonly placeholder="<?= isset($fecha['placeholder1']) ? $fecha['placeholder1'] : 'Seleccionar' ?>" />
                            <span class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="clearfix hidden-sm hidden-md hidden-lg " style="margin-bottom: 5px;" ></div>
                <div class="col-sm-6 col-xs-12" style="<?= ($fecha['custom']) ? 'padding: 0 ; display:none;' : '' ?> padding-right:0px !important;">
                    <div class="form-group search-date" style="margin-bottom: 0 !important;">
                        <!--label>Hasta</label-->
                        <div class='input-group date'>
                            <input type='text' id='hasta' style="cursor: pointer; padding-right: 0;z-index: 0; height:30px; background: #F2F6E9; border: solid 1px #ffffff;" class="form-control" name="<?= $controller .'[hasta]' ?>" value="<?= $fhasta ?>" readonly placeholder="<?= isset($fecha['placeholder2']) ? $fecha['placeholder2'] : 'Seleccionar' ?>"/>
                            <span class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="clearfix hidden-md hidden-lg " style="margin-bottom: 5px;" ></div>
            </div>
        <?php } ?>
        <!-- Si existe al menos un select para mostrar en la busqueda -->
        <?php if($selects > 0) { ?>
            <div class="col-select hidden-xs <?= $colDivSelect ?> col-xs-12 lupa-margin-block" style="padding-right:0px !important;">
                <?php $flagSelect = 0; foreach ($select as $key => $value) { ?>
                    <div class="col-select  <?= $colSelect ?> col-xs-12 col-md-2 <?= ($value == end($select)) ? 'lupa-margin' : 'select-margin' ?> <?= ($flagSelect == 0 && $fecha['rangoFecha']) ? 'lupa-margin': '' ?>">
                        <?php if(!isset($value['same_table'])) { ?>
                            <?= $this->Form->input($key, [
                                'name' => $controller .'[select]['. $value['campo'] .']',
                                'empty' => isset($value['empty']) ? $value['empty'] : 'Seleccionar',
                                'label' => false,
                                'options'=> $value['opciones'],
                                'class'=>"form-control list-search ".$key,
                                'value' => isset($select_values[$value['campo']])
                                    ? $select_values[$value['campo']]
                                    : ""
                            ]) ?>
                        <?php } else { ?>
                            <?php $campoArray = explode('_', $key); ?>
                            <?= $this->Form->input($value['campo'], [
                                'name' => $controller .'[select]['. $value['campo'] .']',
                                'empty' => isset($value['empty']) ? $value['empty'] : 'Seleccionar',
                                'label' => false,
                                'options'=> $value['opciones'],
                                'class'=>"form-control list-search ".$key,
                                'value' => isset($select_values[$value['campo']])
                                    ? $select_values[$value['campo']]
                                    : ""
                            ]) ?>
                        <?php } ?>
                    </div>
                    <?php $flagSelect++; } ?>
            </div>

            <div class="hidden-sm hidden-md hidden-lg col-xs-12 lupa-margin-block" style="padding-right:0px !important;">
                <?php $flagSelect = 0; foreach ($select as $key => $value) { ?>
                    <div class="col-xs-12 list-mob">
                        <?php if(!isset($value['same_table'])) { ?>
                            <?= $this->Form->input($key."-mob", [
                                'name' => $controller .'[select]['. $value['campo'] .']',
                                'empty' => isset($value['empty']) ? $value['empty'] : 'Seleccionar',
                                'label' => false,
                                'options'=> $value['opciones'],
                                'class'=>"form-control list-search-mov ".$key,
                                'value' => isset($select_values[$value['campo']])
                                    ? $select_values[$value['campo']]
                                    : ""
                            ]) ?>
                        <?php } else { ?>
                            <?php $campoArray = explode('_', $key); ?>
                            <?= $this->Form->input($value['campo']."-mob", [
                                'name' => $controller .'[select]['. $value['campo'] .']',
                                'empty' => isset($value['empty']) ? $value['empty'] : 'Seleccionar',
                                'label' => false,
                                'options'=> $value['opciones'],
                                'class'=>"form-control list-search-mov ".$key,
                                'value' => isset($select_values[$value['campo']])
                                    ? $select_values[$value['campo']]
                                    : ""
                            ]) ?>
                        <?php } ?>
                    </div>
                    <?php $flagSelect++; } ?>
            </div>

        <?php } ?>
        <!-- Checkbox para mostrar registros tanto activos como inactivos -->
        <?php
        echo $this->Form->input("trash",[
            'type'=>'hidden',
            'value'=>$inactivo,
            'name'=>$controller .'[trash]'
        ]);
        if(count($checkboxelement)>0 ){  ?>
            <?php foreach ($checkboxelement as $key => $value) {?>
                <div class="col-md-2 col-sm-2 col-xs-12" style="width: auto; padding-right: 0;margin-top: -5px;">
                    <div class="form-group">
                        <?= $this->Form->input($value['campo'], [
                            'name' => $controller .'[checkbox]['. $value['campo'] .']',
                            'label' => $value['label'],
                            'type' => 'checkbox',
                            'checked' => $check,
                            'div' =>false
                        ]); ?>
                    </div>
                </div>
            <?php } ?>
        <?php   }        ?>
        <div class="col-sm-2 col-xs-1 col-btn-search   hidden-xs ">
            <button class="btn btn-default btn-search" type="submit"><span><i class="fa fa-search icon-search"></i></span></button>
        </div>

        <!-- Campos de texto ocultos para nombre del controlador y nombre de campos para la busqueda en campo de texto -->
        <?= $this->Form->hidden('campos', ['value' => $campos, 'name' => $controller . '[parametro]']); ?>
        <?= $this->Form->hidden('controller', ['value' => $controller, 'name' => $controller . '[controller]']); ?>
        <?= $this->Form->hidden('hsearch'); ?>

        <?php if (!empty($_SESSION["id"])): ?>
        <?= $this->Form->hidden("id",['value' => $_SESSION["id"]]);?>
        <?php endif; ?>
        <?= $this->Form->end(); ?>
    </div>
    
    <!-- Se muestra el boton, si se ha realizado una busqueda -->

        <div class="col-sm-2 col-btn-limpiar hidden-xs ">
            <?= $this->Html->link($this->Html->tag('span',__(' Todos',true), ['class' => '']),
                [
                'action' => 'indexpublic', 1
                 ], [
                'class' => 'btn clean-search-public pull-left',
                'escape' => false
            ]); ?>
        </div>
        <div class="col-sm-2 col-xs-3 col-btn-limpiar hidden-sm hidden-md hidden-lg">
            <?= $this->Html->link($this->Html->tag('span',__(' Todos',true), ['class' => '']),
                [
                    'action' => 'indexpublic',1
                ], [
                    'class' => 'btn clean-search-public pull-left',
                    'escape' => false,
                    'style'=>'margin-top: 10px;margin-left: 15px; width: 100%; font-weight: 600;',
                ]); ?>
        </div>



</div>
            <div class="clearfix  " style="margin-bottom: 10px;" ></div>
<script>
    var hoy = new Date();
    var fechaFormulario = new Date(hoy);
    $(function () {
        $('#desde').datetimepicker({
            ignoreReadonly: true,
            format: 'DD/MM/YYYY',
            locale: 'es',
            minDate: '01/01/1980',
            maxDate: fechaFormulario,
            icons:{
                previous: 'fa fa-arrow-left',
                next: 'fa fa-arrow-right',
            }
        });

        $('#hasta').datetimepicker({
            ignoreReadonly: true,
            format: 'DD/MM/YYYY',
            locale: 'es',
            useCurrent: false,
            icons:{
                previous: 'fa fa-arrow-left',
                next: 'fa fa-arrow-right',
            }
        });

        $("#desde").on("dp.change", function (e) {
            $('#hasta').data("DateTimePicker").minDate(e.date);
        });
        $("#hasta").on("dp.change", function (e) {
            $('#desde').data("DateTimePicker").maxDate(e.date);
        });
    });

    $(document).on("ready", function()
    {
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