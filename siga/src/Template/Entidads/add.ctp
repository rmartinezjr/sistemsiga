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
        <?= $this->Form->create($entidad, ['novalidate','id'=>"frm"]) ?>
        <div class="row">
            <div class="col-md-6">
                <h2 class="tittle">Crear <?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
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
                    <div class="alert alert-danger no-display" id="alert">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message"></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                </div>
                <div class="col-md-2 frm-label">Nombre Corto</div>
                <div class="col-md-10">
                    <?= $this->Form->control('nombre',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Nombre Corto',
                        'required',
                        'onchange'=>'ValUniqueType(this.id)'
                    ]);?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label">Nombre Largo</div>
                <div class="col-md-10">
                    <?= $this->Form->control('nombrelargo',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Nombre Largo',
                        'required'
                    ]);?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label">Código</div>
                <div class="col-md-10">
                    <?= $this->Form->control('codigo',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Código',
                        'required',
                        'onchange'=>'ValUniqueType(this.id)'
                    ]);?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label">Rol de Entidad</div>
                <div class="col-md-2">
                    <?=$this->Form->control('centidadrol_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => $centidadrols,
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar',
                        'required',
                    ]);?>
                </div>
                <div class="col-md-2 frm-label">Tipo de Entidad</div>
                <div class="col-md-2">
                    <?=$this->Form->control('centidadtipo_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => $centidadtipos,
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar',
                        'required',
                    ]);?>
                </div>
                <div class="col-md-2 frm-label no-disponible" style="display: none;">Documento no Disponible</div>
                <div class="col-md-2 no-disponible" style="display: none;">
                    <?=$this->Form->control('docidnull',[
                        'label'=>'',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                        ],
                    ]);?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label">Origen</div>
                <div class="col-md-2">
                    <?=$this->Form->control('nacional',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => ['1'=>'Entidad Salvadoreño', '0'=>'Entidad Extranjero'],
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar',
                        'required',
                    ]);?>
                </div>

                <div class="col-md-2 frm-label">Documento Identidad</div>
                <div class="col-md-2">
                    <?= $this->Form->control('docid',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input doc-id',
                        'placeholder'=>'Documento Identidad',
                        'disabled' => 'disabled',
                        'style' => 'font-size: 11px',
                        'onchange'=>'ValUniqueType(this.id)',
                        'required',
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
                        'empty' => 'Seleccionar',
                        'required',
                    ]);?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label entidadsrelated-label">Entidades Miembros</div>
                <div class="col-md-5">
                    <div class="col-md-10 col-sm-8 col-xs-10 entidadsrelated">
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
        var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidads', 'action' => 'getTipoDocumento'], true) . '/' ?>';
        var docid_requerido = true;

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
        });

        // Al momento de hacer click en boton para eliminar listado de entidades
        $(document).on('click', '.btn-remove-row', function(event) {
            var elemento = $( this );
            var correlativo = elemento.attr('data-corr');
            var totalSelect =parseInt($('.entidadsrelated .bloque-entidad-select .select select').length);

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
                }, 5000);
            }
        });

        $(document).on('change', '#centidadtipo-id', function(e) {
            var urlentidadtipo = '<?= \Cake\Routing\Router::url(['controller' => 'entidads', 'action' => 'verifyrequireddocid'], true)  . '/' ?>';
            var centidadtipo_id = $( this ).val();

            if(centidadtipo_id != '') {
                $.ajax({
                    url: urlentidadtipo,
                    type: 'post',
                    data: {centidadtipo_id:centidadtipo_id},
                    dataType: 'json',
                    cache:false,
                    async:false,
                    success:function (resp) {
                        if(resp.error != 1) {
                            docid_requerido = resp.data.docidreq;

                            if(!docid_requerido) {
                                $('.no-disponible').css("display", "block");
                            } else {
                                $('.no-disponible').css("display", "none");
                                $("#docidnull").attr("checked", false);
                            }
                        } else {

                        }
                    }
                });
            } else {
                $('.no-disponible').css("display", "none");
                $("#docidnull").attr("checked", false);
            }
        });

        $(document).on('change', '#docidnull', function(e) {
            var nacional = $('#nacional').val();
            var elemento = $("#docid");

            if($( this ).is(':checked')) {
                elemento.attr('disabled', true);
                elemento.val('');
            } else {
                if(nacional == '1') {
                    getMascaraDocumento(nacional, url, elemento, false);
                    elemento.attr("disabled", false);
                    elemento.attr("placeholder", "NIT");
                } else if(nacional == '0') {
                    elemento.val("");
                    elemento.unmask();
                    elemento.attr("placeholder", "Documento");
                    elemento.attr("disabled", false);
                }
            }
        });

        $("#nacional").change(function (e) {
            var id = $(this).val();

            if(!$('#docidnull').is(':checked')) {
                if(id!=0) {
                    var elemento = $("#docid");
                    getMascaraDocumento(id, url, elemento, false);
                    $("#docid").attr("disabled", false);
                    $("#docid").attr("placeholder", "NIT");
                }
                else
                {
                    $("#docid").val("");
                    $("#docid").unmask();
                    $("#docid").attr("placeholder", "Documento");
                    $("#docid").attr("disabled", false);
                }
            }
        });
        $(document).on('keyup', '#docid', function(event)
        {
            if($("#nacional").val()==0)
            {
                this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')
            }
        });

        $("#frm").submit(function(e){
            var id = 0;
            var nombre = $("#nombre").val();
            var url = getUrl()+"valunique";
            var campos = {
                0: {
                    'campo': 'nombre', 'label': 'nombre corto', 'tipo': 'input'
                },
                1: {
                    'campo': 'nombrelargo', 'label': 'nombre largo', 'tipo': 'input'
                },
                2: {
                    'campo': 'codigo', 'label': 'código', 'tipo': 'input'
                },
                3: {
                    'campo': 'centidadrol-id', 'label': 'rol', 'tipo': 'select'
                },
                4: {
                    'campo': 'centidadtipo-id', 'label': 'tipo', 'tipo': 'select'
                },
                5: {
                    'campo': 'nacional', 'label': 'origen', 'tipo': 'select'
                },
                6: {
                    'campo': 'docid', 'label': 'documento identidad', 'tipo': 'input'
                },
                7: {
                    'campo': 'cestado-id', 'label': 'estado', 'tipo': 'select'
                }
            };

            $.each(campos, function(item, col){
                if(!(($('#docidnull').is(':checked')) && (col['campo'] == 'docid'))) {
                    var band = valRequired(col['campo'], col['label'], col['tipo']);

                    if(band === false){
                        e.preventDefault();
                        return false;
                    }
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
    });
</script>