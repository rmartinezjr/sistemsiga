<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CorreoPlantilla[]|\Cake\Collection\CollectionInterface $correoPlantillas
 */
$real_url = \Cake\Routing\Router::url(['action' => 'index'], true) .'/';

echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']);
echo $this->Html->script(['lib/ckeditor/ckeditor', 'funciones/validaciones.js']);
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
        ])
        ?>
    </div>
    <div class="cont-border">
        <?= $this->Form->create($correoPlantilla, ['id'=>'formulario', 'novalidate']) ?>

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
        </div><br>

        <div class="row">
            <div class="col-md-12 content-form">
                <div class="col-md-12">
                    <div class="alert alert-danger no-display" id="alert">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message"></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                </div>

                <div class="col-md-2 frm-label">Nombre</div>
                <div class="col-md-10">
                    <?= $this->Form->control('nombre',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Nombre',
                        'required',
                        'onchange'=>'ValUnique(this.id)'
                    ]);?>
                </div>
                <div class="col-md-2 frm-label">Contenido</div>
                <div class="col-md-10">
                    <?= $this->Form->control('contenido', [
                        'id' => 'ckeditor',
                        'label'=>false,
                        'class' => 'ckeditor',
                        'required'
                    ]); ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script data-sample="1">
    $(document).ready(function(){
        var editor = CKEDITOR.replace( 'ckeditor', {
            height: 390,
            toolbarGroups: [
                {"name":"document","groups":["mode"]},
                {"name":"styles","groups":["styles"]},
                {"name":"basicstyles","groups":["basicstyles"]},
                {"name":"colors"},
                {"name":"paragraph","groups":["blocks","align","list"]},
                {"name":"links","groups":["links"]},
                {"name":"insert","groups":["insert"]},
                {"name":"about","groups":["about"]},
                {"name":"texttransform","groups":["TransformTextToUppercase","TransformTextToLowercase","TransformTextCapitalize","TransformTextSwitcher"]}
            ],
            removeButtons: 'Strike,Subscript,Superscript,Anchor,Specialchar,Flash,Iframe,Save,Print,NewPage,Preview,Smiley,PageBreak'
        });
    });

    jQuery(function(){
        $("#formulario").submit(function(e){
            var id = 0;
            var nombre = $("#nombre").val();
            var contenido = CKEDITOR.instances.ckeditor.document.getBody().getChild(0).getText() ;
            var url = getUrl()+"valunique";

            var campos =
            {
                0: {
                    'campo': 'nombre',
                    'label': 'nombre'
                }
            };
            $.each(campos, function(item, col){
                var band = valRequired(col['campo'], col['label']);
                if(band === false){
                    e.preventDefault();
                }
            });

            if(contenido.trim() === '') {
                $(".message").text("El contenido de la plantilla de correo debe ser completado.");
                $("#alert").slideDown();
                setTimeout(function () {
                    $("#alert").slideUp();
                }, 4000);

                e.preventDefault();
            }

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