<?php
/**
 * @var \App\View\AppView $this
 */

$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js', 'lib/jquery.maskedinput.js', 'funciones/validaciones.js', 'funciones/entidadContactos.js']);
?>
<script>
    function getUrl(){
        return "<?=$real_url?>";
    }
</script>
<div class="row work-space">
    <div class="nav-space">
        <?php
        echo $this->element("navegacion",[
            'datos'=>$nav
        ])?>
    </div>
    <div class="cont-border">
        <?= $this->Form->create($contacto, ['novalidate','id'=>"frm"]) ?>
        <div class="row">
            <div class="col-md-6">
                <h2 class="tittle">Editar <?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
            </div>
            <div class="col-md-6 panel-action">
                <button class="btn btn-sistem btn-save"><span>Guardar</span><i class="fa fa-floppy-o icono"></i></button>
                <?php foreach ($controltools as $btn){
                    if($btn['funcion']==="imprimir"){   ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= \Cake\Routing\Router::url(['controller' => $btn['modelo'], 'action' => 'index'], true) .'/'.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                }else{?>
                        <a class="btn btn-sistem <?=$btn['class']?>" href="<?=\Cake\Routing\Router::url(['controller' => $btn['modelo'], 'action' => 'index'], true) .'/'.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                   }
                }           ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 content-form">
                <div class="col-md-12">
                    <?php
                    if(isset($_SESSION['UserRelated'])){
                        if($_SESSION['UserRelated']==1){
                            unset($_SESSION['UserRelated']);?>
                            <div class="alert alert-danger no-display" id="alert-contacto">
                                <span class="icon icon-cross-circled"></span>
                                <span class="message">El contacto no puede estar inactivo. Tiene usuario relacionado.</span>
                                <button type="button" class="close" data-dismiss="alert"></button>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="alert alert-danger no-display" id="alert">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message"></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                </div>
                <?= $this->Form->control("id");?>
                <div class="col-md-2 frm-label">Nombres</div>
                <div class="col-md-5">
                    <?= $this->Form->control('nombres',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Nombres',
                        'required'
                    ]);?>
                </div>
                <div class="col-md-1 frm-label">Apellidos</div>
                <div class="col-md-4">
                    <?= $this->Form->control('apellidos',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Apellidos',
                        'required'
                    ]);?>
                </div>
                <!--div class="clearfix"></div>
                <div class="col-md-2 frm-label">Nacionalidad</div>
                <div class="col-md-10">
                    <?php   /* $this->Form->control('nacional',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Nacionalidad',
                        'type'=>'text',
                        'required'
                    ]);*/?>
                </div -->
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label">Tipo de Contacto</div>
                <div class="col-md-2">
                    <?=$this->Form->control('ccontactotipo_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => $ccontactotipos,
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar'
                    ]);?>
                </div>
                <div class="col-md-1 frm-label">Origen </div>
                <div class="col-md-2">
                    <div class="input select">
                    <select name="nacional" div="form-group" class="form-control select-control" required="required" id="nacional">
                        <option value="">Seleccionar</option>
                        <option  <?=($contacto['nacional']=='Extranjero')?'':'selected';?> value="1">Salvadoreño</option>
                        <option value="0"  <?=($contacto['nacional']=='Extranjero')?'selected':'';?>>Extranjero</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-3 frm-label">Documento Identidad</div>
                <div class="col-md-2">
                    <?= $this->Form->control('docid',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Documento Identidad',
                        'style' => 'font-size: 11px',
                        'onchange'=>'ValUniqueType(this.id)'
                    ]);?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label">Descripción</div>
                <div class="col-md-10">
                    <?=$this->Form->control('descripcion',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-tarea',
                        'placeholder'=>'Descripción',
                        'rows'=>3
                    ]);?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label">Estado</div>
                <div class="col-md-2">
                    <?=$this->Form->control('cestado_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => $cestados,
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar'
                    ]);?>
                </div>
                <div class="col-md-offset-4 col-md-2 frm-label">País</div>
                <div class="col-md-2">
                    <?= $this->Form->control("cpaise_id",[
                        "label"=>false,
                        "div"=>["class"=>"form-group"],
                        "class"=>"form-control select-control",
                        "empty"=>"Seleccionar",
                        "required"
                    ]); ?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label entidadsrelated-label">Entidades Relacionadas</div>
                <div class="col-md-5">
                    <div class="col-md-10 col-sm-8 col-xs-10 entidadsrelated">
                        <?php if(count($entidadcontactos) > 0) { ?>
                            <?php $i = 0 ?>
                            <?php foreach($entidadcontactos as $entidadcontacto) { ?>
                                <div id="bloque-entidad-select-<?= $i ?>" class="col-md-11 col-sm-8 col-xs-8 bloque-entidad-select">
                                    <?=$this->Form->control('entidad_'. $i,[
                                        'label'=>false,
                                        'name' =>'entidad[' . $i . ']',
                                        'div'=>['class'=>'form-group'],
                                        'options' => $entidads,
                                        'data-corr' => $i,
                                        'id' => 'entidad-' . $i,
                                        'value' => $entidadcontacto->entidad_id,
                                        'class'=>'form-control select-control select-entidads',
                                        'empty' => 'Seleccionar'
                                    ]);?>
                                </div>
                                <div id="bloque-entidad-button-<?= $i ?>" class="col-md-1 col-sm-4 col-xs-4 bloque-entidad-button">
                                    <button data-corr="<?= $i ?>" class="btn btn-default btn-remove-row btn-search" type="button"><span><i class="fa fa-trash icon-search"></i></span></button>
                                </div>
                                <?php $i++ ?>
                            <?php } ?>
                        <?php } else { ?>
                            <div id="bloque-entidad-select-0" class="col-md-11 col-sm-8 col-xs-8 bloque-entidad-select">
                                <?=$this->Form->control('entidad_0',[
                                    'label'=>false,
                                    'name' =>'entidad[0]',
                                    'div'=>['class'=>'form-group'],
                                    'options' => $entidads,
                                    'data-corr' => '0',
                                    'id' => 'entidad-0',
                                    'class'=>'form-control select-control select-entidads',
                                    'empty' => 'Seleccionar'
                                ]);?>
                            </div>
                            <div id="bloque-entidad-button-0" class="col-md-1 col-sm-4 col-xs-4 bloque-entidad-button">
                                <button data-corr="0" class="btn btn-default btn-remove-row btn-search" type="button" style="display: none"><span><i class="fa fa-trash icon-search"></i></span></button>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-2 bloque-entidad-plus">
                        <button class="btn btn-default btn-add-row btn-search" type="button"><span><i class="fa fa-plus icon-search"></i></span></button>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function(){
        var doctipo = $("#cdocidtipo-id");
        var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'getTipoDocumento'], true) .'/' ?>';
        // Se obtiene la mascara del tipo de documento seleccionado
        getMascaraDocumento(doctipo.val(), url, $("#docid"), true);

        if(parseInt($('.entidadsrelated .bloque-entidad-select .select select').length) < 2) {
            $('.entidadsrelated .bloque-entidad-button button').css("display", "none");
        }


        if($("#nacional").val() == 1) {
            $("#docid").mask('99999999-9');
            $("#docid").attr("placeholder", "DUI");
        }


        $(document).on('change', '#cestado-id', function(e) {
            var id = $("#id").val();
            var estado_id = $( this ).val();

            $.ajax({
                url: getUrl() + 'existencontactos',
                type: 'post',
                data: { id: id, estado_id: estado_id },
                dataType: 'json',
                cache:false,
                async:false,
                success:function (resp) {
                    if(resp['error']===1){
                        e.preventDefault();
                        $("#cestado-id").val(resp['activo']);
                        $("#cestado-id").focus().select();
                        $(".message").text(resp["msj"]);
                        $("#alert").slideDown();
                        setTimeout(function () {
                            $("#alert").slideUp();
                        }, 4000);
                    }
                }
            });
        });

        // Agrega un listado de entidades
        $(document).on('click', '.btn-add-row', function(event) {
            var totalSelect =parseInt($('.entidadsrelated .bloque-entidad-select .select select').length);
            var elemento = $('.entidadsrelated .bloque-entidad-select:first-child .select select');
            // var correlativo = parseInt(elemento.attr('data-corr')) + 1;
            var correlativo = totalSelect;
            var data_entidads = elemento.html();
            var html = '';

            if(totalSelect >= 1) {
                $('.entidadsrelated .bloque-entidad-button button').css("display", "block");
            }

            html+= '<div id="bloque-entidad-select-' + correlativo + '" class="col-md-11 col-sm-8 col-xs-8 bloque-entidad-select">' +
                '<div class="input select">' +
                '<select name="entidad[' + correlativo + ']" div="form-group" data-corr="' + correlativo + '" id="entidad-' + correlativo + '" class="form-control select-control select-entidads">'
            html+= data_entidads;
            html+= '</select></div></div>';
            html+= '<div id="bloque-entidad-button-' + correlativo + '" class="col-md-1 col-sm-4 col-xs-4 bloque-entidad-button"><button data-corr="' + correlativo + '" class="btn btn-default btn-remove-row btn-search" type="button"><span><i class="fa fa-trash icon-search"></i></span></button></div>';
            $('.entidadsrelated').append(html);
            $('#entidad-' +correlativo).val('');
        });

        // Al momento de hacer click en boton para eliminar listado de entidades
        $(document).on('click', '.btn-remove-row', function(event) {
            var totalSelect =parseInt($('.entidadsrelated .bloque-entidad-select .select select').length);
            var elemento = $( this );
            var correlativo = elemento.attr('data-corr');

            $( '#bloque-entidad-select-' + correlativo ).remove();
            $( '#bloque-entidad-button-' + correlativo ).remove();

            if(totalSelect <= 2) {
                $('.entidadsrelated .bloque-entidad-button button').css("display", "none");
            }
        });

        // Al seleccionar una opcion del listado de entidades
        $(document).on('change', '.select-entidads', function(event) {
            var arrayValores = [];
            var elemento = $( this );
            var valorSeleccionado = elemento.val();

            // Se obtiene la opcion seleccionada en cada select de entidades
            $(".select-entidads").each(function() {
                if($(this).attr('id') !== elemento.attr('id')) {
                    arrayValores.push($(this).val());
                }
            });

            // Verifica si la opcion ha sido seleccionada con anterioridad
            if(jQuery.inArray(valorSeleccionado, arrayValores) !== -1) {
                elemento.val("");
                $(".message").text('La entidad ya ha sido seleccionada');
                $("#alert").slideDown();
                setTimeout(function () {
                    $("#alert").slideUp();
                }, 4000);
            }
        });

        // Al seleccionar una opcion del tipo de documento
        $("#nacional").change(function (e) {
            var id = $(this).val();
            if(id!=0)
            {
                var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'getTipoDocumento'], true) .'/' ?>';
                var elemento = $("#docid");
                var tipo_registro = $('#tipo-registro').val();
                if(tipo_registro !== '1') {
                    getMascaraDocumento(id, url, elemento, false);
                    $("#docid").attr("disabled", false);
                    $("#docid").attr("placeholder", "DUI");
                }
            }
            else
            {   $("#docid").val("");
                $("#docid").unmask();
                $("#docid").attr("placeholder", "Documento");
                $("#docid").attr("disabled", false);
            }
        });

        $("#frm").submit(function(e){
            var listadoEntidades = $(".select-entidads");
            var id = $("#id").val();
            var nombre = $("#nombre").val();
            var url = getUrl()+"valunique";
            var campos = {
                0: {
                    'campo': 'nombres', 'label': 'nombres', 'tipo': 'input'
                },
                1: {
                    'campo': 'apellidos', 'label': 'apellidos', 'tipo': 'input'
                },
                2: {
                    'campo': 'nacional', 'label': 'origen', 'tipo': 'input'
                },
                3: {
                    'campo': 'ccontactotipo-id', 'label': 'tipo de contacto', 'tipo': 'select'
                },
                4: {
                    'campo': 'cdocidtipo-id', 'label': 'tipo de documento', 'tipo': 'select'
                },
                5: {
                    'campo': 'docid', 'label': 'documento identidad', 'tipo': 'input'
                },
                6: {
                    'campo': 'cestado-id', 'label': 'estado', 'tipo': 'select'
                },
                7: {
                    'campo': 'cpaise-id', 'label': 'país', 'tipo': 'select'
                }
            };

            // Verifica si se han ingresado datos en los campos que son requeridos
            $.each(campos, function(item, col){
                var band = valRequired(col['campo'], col['label'], col['tipo']);

                if(band === false){
                    e.preventDefault();
                    return false;
                }
            });

            // Verifica si no se ha seleccionado una opcion en cada select de entidades
            listadoEntidades.each(function() {
                if($(this).val() === '') {
                    $(this).focus().select();

                    $(".message").text('Debe de seleccionar una opción del listado de entidades');
                    $("#alert").slideDown();
                    setTimeout(function () {
                        $("#alert").slideUp();
                    }, 4000);

                    e.preventDefault();
                    return false;
                }
            });

            $.ajax({
                url: url,
                type: 'post',
                data: {campo:nombre,id:id},
                dataType: 'json',
                cache:false,
                async:false,
                success:function (resp) {
                    if(resp['error']===1){
                        e.preventDefault();
                        $("#nombre").val("");
                        $(".message").text(resp["msj"]);
                        $("#alert").slideDown();
                        setTimeout(function () {
                            $("#alert").slideUp();
                        }, 4000);
                    }
                }
            });
        });

        $("#alert-contacto").slideDown();
        setTimeout(function () {
            $("#alert-contacto").slideUp();
        }, 4000);
    });
</script>