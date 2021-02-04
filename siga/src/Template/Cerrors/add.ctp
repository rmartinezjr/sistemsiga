<?php
/**
 * @var \App\View\AppView $this
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
        <div class="row">
            <?php
            if(isset($_SESSION['cerror-save'])){
                if($_SESSION['cerror-save']==0){
                    unset($_SESSION['cerror-save']);?>
                    <div class="alert alert-danger" id="alert_valid" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">No es posible almacenar el registro. Inténtalo nuevamente.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php }
            }
            ?>
        </div>
        <?= $this->Form->create($cerror, ['id'=>'formulario', 'novalidate']) ?>

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
            <div class="alert alert-danger no-display" id="alert">
                <span class="icon icon-cross-circled"></span>
                <span class="message"></span>
                <button type="button" class="close" data-dismiss="alert"></button>
            </div>
            <div class="col-md-12 content-form">
                <div class="col-md-2 frm-label">Nombre</div>
                <div class="col-md-10">
                    <?= $this->Form->control('nombre',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control control-input',
                        'placeholder'=>'Nombre',
                        'required',
                        'onchange'=>'ValUniqueType(this.id)'
                    ]);?>
                </div>
                <div class="col-md-2 frm-label">Código de error</div>
                <div class="col-md-2">
                    <?= $this->Form->control('codigo',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'type'=>'text',
                        'class'=>'form-control control-input',
                        'placeholder'=>'Código de error',
                        'required',
                        'onchange'=>'ValUniqueType(this.id)'
                    ]);?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 frm-label">Mensaje de error</div>
                <div class="col-md-10">
                            <?= $this->Form->control('html', [
                        'id' => 'ckeditor',
                        'label'=>false,
                        'class' => 'ckeditor',
                        'required'
                    ]); ?>
                </div>
                <div class="col-md-2 frm-label" style="margin-top: 10px">Estado</div>
                <div class="col-md-2" style="margin-top: 10px">
                    <?=$this->Form->control('cestado_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar',
                        'required',
                        'options'=> $cestados

                    ]);?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script data-sample="1">
    $(document).ready(function(){
        var editor = CKEDITOR.replace( 'ckeditor', {
            height: 150,
            toolbarGroups: [
                {"name":"document","groups":["mode"]},
                {"name":"styles","groups":["styles"]},
                {"name":"basicstyles","groups":["basicstyles"]},
                {"name":"colors"},
            ],
            removeButtons: 'Strike,Subscript,Superscript,Anchor,Specialchar,Flash,Iframe,Save,Print,NewPage,Preview,Smiley,PageBreak,Format,Styles,BGColor'
        });
    });

    jQuery(function(){
        //Validando numeros enteros
        $("#codigo").keydown(function (e) {
            // Codigo de caracteres permitidos, sin tomar en cuenta numeros y letras.
            var keycodes = [];
            ValCaracteresValidos(e, keycodes, true, false);
        });

        $("#formulario").submit(function(e){
            var id = 0;
            var nombre = $("#nombre").val();
            var contenido = CKEDITOR.instances.ckeditor.document.getBody().getChild(0).getText() ;
            var url = getUrl()+"valunique";

            var campos =
            {
                0: {
                    'campo': 'nombre',
                    'label': 'nombre',
                    'tipo': 'input'
                },
                1: {
                    'campo': 'codigo',
                    'label': 'código',
                    'tipo': 'input'
                },
                2: {
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

            if(contenido.trim() === '') {
                $(".message").text("El contenido del campo mensaje de error debe ser completado.");
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