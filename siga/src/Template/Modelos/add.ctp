<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cestado[]|\Cake\Collection\CollectionInterface $cestados
 */
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js', 'funciones/validaciones.js']);
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

    <?= $this->Form->create($modelo,['novalidate','id'=>"frm"]) ?>
        <div class="row">
            <div class="col-md-6">
                <h2 class="tittle">Crear <?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
            </div>
            <div class="col-md-6 panel-action">
                <button class="btn btn-sistem btn-save"><span>Guardar</span><i class="fa fa-floppy-o icono"></i></button>
                <?php foreach ($controltools as $btn){
                    if($btn['funcion']==="imprimir"){   ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                }else{?>
                        <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                   }
                }           ?>
            </div>
        </div>

        <div class="row">
            <div class="alert alert-danger no-display" id="alert">
                <span class="icon icon-cross-circled"></span>
                <span class="message"></span>
                <button type="button" class="close" data-dismiss="alert"></button>
            </div>
            <div class="col-md-12 content-form">
                <div class="col-md-12">
                    <div class="alert alert-danger no-display" id="alert">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message"></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                </div>
                <div class="col-md-2 frm-label">Modelo</div>
                <div class="col-md-10">

                    <?= $this->Form->control('modelo',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Modelo',
                        'required',
                        'onchange'=>'ValUniqueType(this.id)'
                    ]);?>
                </div>
                <div class="col-md-2 frm-label">Alias</div>
                <div class="col-md-10">
                    <?= $this->Form->control('alias',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Nombre',
                        'required',
                        'onchange'=>'ValUniqueType(this.id)'
                    ]);?>

                </div>
                <div class="col-md-2 frm-label">Menú </div>
                    <div class="col-md-4">
                        <?=$this->Form->control('menu_id',[
                            'label'=>false,
                            'div'=>['class'=>'form-group'],
                            'options' => $menu,
                            'class'=>'form-control select-control',
                            'empty' => 'Seleccionar',
                            'rows'=>3,
                            'required'
                        ]);?>
                    </div>
                    <div class="col-md-2 frm-label">Sub-menú </div>
                    <div class="col-md-4">
                        <?=$this->Form->control('items_id',[
                            'label'=>false,
                            'div'=>['class'=>'form-group'],
                            'class'=>'form-control select-control',
                            'empty' => 'Seleccionar',
                            'rows'=>3,
                            'required'
                        ]);?>
                    </div>

                    <div class="clearfix col-md-12" ></div>
                <div class="col-md-2 frm-label">Descripción</div>
                <div class="col-md-10">
                    <?= $this->Form->control('descripcion',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Descripción',

                    ]);?>
                </div>

                <div class="col-md-2 frm-label">Diseño móvil</div>
                <div class="col-md-2">
                    <?= $this->Form->control('movil',
                        [   'type'=>'checkbox',
                            'label'=>false,
                            'style'=>' margin-top:-10px !important;margin-left:0px !important;',
                            'id'=>'movil'
                        ]);?>
                </div>
                <div class="clearfix col-md-12" ></div>
                <div class="col-md-2 frm-label">Estado</div>
                <div class="col-md-2">
                    <?=$this->Form->control('cestado_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => $cestados,
                        'class'=>'form-control select-control ',
                        'empty' => 'Seleccionar',
                        'rows'=>3,
                        'required'
                    ]);?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function(){
        $("#frm").submit(function(e){
            var id = 0;
            var nombre = $("#modelo").val();
            var url = getUrl()+"valunique";
            $.ajax({
                url: url,
                type: 'post',
                data: {campo:nombre,id:id},
                dataType: 'json',
                cache:false,
                async:false,
                success:function (resp) {
                    console.log(resp['error']);
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
            var campos = {
                0: {
                    'campo': 'modelo',
                    'label': 'Modelo',
                    'tipo': 'input'
                },
                1: {
                    'campo': 'alias',
                    'label': 'alias ',
                    'tipo': 'input'
                },
                2: {
                    'campo': 'menu-id',
                    'label': 'menu',
                    'tipo': 'select'
                },
                3: {
                    'campo': 'items-id',
                    'label': 'sub-menu',
                    'tipo': 'select'
                },
                4: {
                    'campo': 'movil-id',
                    'label': 'movil',
                    'tipo': 'select'
                },
                    5: {
                    'campo': 'cestado-id',
                    'label': 'estado',
                    'tipo': 'select'
                }
            };

            $.each(campos, function(item, col){
                var band = valRequired(col['campo'], col['label'], col['tipo']);
                if(band=== false){
                    e.preventDefault();
                    return false;
                }
            });

        });

        $(document).on("change", "#menu-id", function()
        {
            var url ="<?= \Cake\Routing\Router::url(['controller' => 'Modelos', 'action' => 'getMenuItems'], true); ?>";
            $.ajax({
                data: {mn: this.value},
                url: url,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $("#items-id").empty();
                    var  items='';
                    items = '<option value>Seleccionar</option>';
                    if (data) {

                        $.each(data, function (indice, val) {

                            items += '<option value="' + val.id + '">' + val.alias + '</option>';
                        });
                        $("#items-id").append(items);
                    }
                }
            });
        });

    });


</script>