<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Perfils[]|\Cake\Collection\CollectionInterface $perfil
 */
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']);
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
        <div class="row">
            <?php
            if(isset($_SESSION['perfil-save'])){
                if($_SESSION['perfil-save']==0){
                    unset($_SESSION['perfil-save']);?>
                    <div class="alert alert-danger" id="alert_valid" style="display: none;">

                        <span class="message">No es posible almacenar el registro. Inténtalo nuevamente.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>
        </div>
        <?= $this->Form->create($perfil,['novalidate','id'=>"frm"]) ?>
        <div class="row">
            <div class="col-md-4">

                <h2 class="tittle">Editar <?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
            </div>
            <div class="col-md-8 panel-action">
                <button class="btn btn-sistem btn-save"><span>Guardar</span><i class="fa fa-floppy-o icono"></i></button>
                <?php foreach ($controltools as $btn){
                    if($btn['funcion']==="imprimir"){   ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                }else{?>
                        <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                   }
                }           ?>
            </div>
        </div><br>
        <div class="row">
            <div class="alert alert-danger no-display" id="alert">

                <span class="message"></span>
                <button type="button" class="close" data-dismiss="alert"></button>
            </div>
            <div class="col-md-12 content-form">
                <div class="col-md-2 frm-label">Nombre</div>
                <div class="col-md-10">
                    <?= $this->Form->control("id");?>
                    <?= $this->Form->control('nombre',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Nombre',
                        'required',
                        'onchange'=>'ValUnique(this.id)'
                    ]);?>
                </div>
                <div class="col-md-2 frm-label">Administrador</div>
                <div class="col-md-10">
                    <?=$this->Form->control('su',[
                        'label'=>'',
                        'templates' => [
                            'inputContainer' => '{{content}}'
                        ],
                    ]);?>
                </div>
                <div class="col-md-2 frm-label">Estado</div>
                <div class="col-md-2">
                    <?=$this->Form->control('cestado_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar',
                        'required',
                        'options'=> $tiposestados

                    ]);?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?= $this->Html->script('funciones/validaciones')?>
<script>
    jQuery(function(){
        $("#alert_valid").slideDown();
        setTimeout(function () {
            $("#alert_valid").slideUp();
        }, 4000);

        $("#frm").submit(function(e){
            var id = $("#id").val();
            var nombre = $("#nombre").val();
            var url = getUrl()+"valunique";
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
            var campos = {
                0: {
                    'campo': 'nombre',
                    'label': 'nombre',
                    'tipo': 'input'
                },
                1: {
                    'campo': 'cestado-id',
                    'label': 'estado',
                    'tipo': 'select'
                }
            };
            $.each(campos, function(item, col){
                var band = valRequired(col['campo'], col['label'], col['tipo']);
                if(band === false){
                    e.preventDefault();
                    return false;
                }
            });
        });
    });
</script>