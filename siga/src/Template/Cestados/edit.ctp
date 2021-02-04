<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cestado[]|\Cake\Collection\CollectionInterface $cestados
 */
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
echo $this->Html->css(['lib/bootstrap-colorpicker.css']);
echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js', 'lib/bootstrap-colorpicker.js']);
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
        <?= $this->Form->create($cestado,['novalidate','id'=>"frm"]) ?>
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
                <div class="col-md-2 frm-label">Color de Fondo</div>
                <div class="col-md-3">
                    <input type="hidden" name="colorbkg" id="colorbkg" value="<?=(empty($cestado->colorbkg))? '#ffffff' : $cestado->colorbkg;?>">
                    <div id="cp1" class="input-group colorpicker-component">
                        <input type="text" name="bkg" id="bkg" value="<?=(empty($cestado->colorbkg))? '#ffffff' : $cestado->colorbkg;?>" class="form-control" style="margin-bottom: inherit;"/>
                        <span class="input-group-addon" style="padding: 0px 10px;"><i id="icp1"></i></span>
                    </div>
                </div>
                <div class="col-md-2 frm-label" >Color de Letra</div>
                <div class="col-md-3">
                    <div id="cp2" class="input-group colorpicker-component">
                        <input type="text" name="colortext" id="colortext" value="<?=(empty($cestado->colortext))? '#000000' : $cestado->colortext;?>" class="form-control" style="margin-bottom: inherit;"/>
                        <span class="input-group-addon" style="padding: 0px 10px;"><i></i></span>
                    </div>
                </div>
                <div class="clearfix col-md-12" style="margin: 4px;"></div>
                <div class="col-md-2 frm-label">Vista previa</div>
                <div class="col-md-2">
                    <div class="cjestado" style="padding: 3px 10px; border-radius: 3px;width: 95px;
                            text-align: center;min-height: 25px; word-wrap: break-word; background-color:<?=(empty($cestado->colorbkg))? '#ffffff' : $cestado->colorbkg;?>;"><span id="spestado" style="font-size: 12px;color:<?=(empty($cestado->colortext))? '#000000' : $cestado->colortext;?>;"><?=$cestado->nombre;?></span></div>
                </div>
                <div class="clearfix col-md-12" style="margin: 4px;"></div>
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
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<script>

    $(function () {
        $('#cp1').colorpicker({
            colorSelectors: {
                'Aprobado':'#cddc39',
                'Borrador': '#929292',
                'Evaluacion': '#80cbc4',
                'Ejecución':'#ffd54f',
                'Publicado':'#15943d',
                'Corregir':'#e57373',
                'Success': '#5cb85c',
                'Info': '#5bc0de',
                'Warning': '#f0ad4e',
                'Red': '#d9534f'
            }
        }).on('changeColor', function(e) {
            $(".cjestado").css("backgroundColor",e.color.toString('rgba'));

            $("#spestado").text(formato($("#nombre").val().split(" ")));
            $("#colorbkg").val(e.color.toString('rgba'));

        });

        $('#cp2').colorpicker({
        }).on('changeColor', function(e) {

            $("#spestado").css("color",e.color.toString('rgba'));
        });

        $(document).on("keyup", "#nombre", function()
        {
            $(this).keyup(function (){
                $("#spestado").text(formato($("#nombre").val().split(" ")));
            });
        });
    });
</script>
<script>
    function  ValUnique(val) {
        var id = $("#id").val();
        var url = getUrl()+"valunique";
        var estado = $("#"+val).val();
        $.ajax({
            url: url,
            type: 'post',
            data: {estado:estado,id:id},
            dataType: 'json',
            cache:false,
            success:function (resp) {
                if(resp['error']===1){
                    $("#"+val).val("");
                    $(".message").text(resp["msj"]);
                    $("#alert").slideDown();
                    setTimeout(function () {
                        $("#alert").slideUp();
                    }, 4000);
                }
            }
        });
    }
    function valRequired(id, label){
        //EL AREGLO SE LLENA CON LOS ID DE LOS CAMPOS REQUERIDOS
        var campo = $("#"+id).val();
        if(campo === '' || campo === 0){
            $(".message").text("El campo "+label+" debe ser completado.");
            $("#alert").slideDown();
            setTimeout(function () {
                $("#alert").slideUp();
            }, 4000);
            return false;
        }
    }

    function formato(a){
        var cade="";
        a.forEach(function(element) {
            if(element.length>11)
            {
                var inicio=element.substring(0,10)
                var fin=element.substring(10)
                element=inicio+'- '+fin
            }
            cade=cade+' '+element
        });
        return cade;
    }

    jQuery(function(){
        $("#frm").submit(function(e){
            var id = $("#id").val();
            var estado = $("#nombre").val();
            var url = getUrl()+"valunique";
            $.ajax({
                url: url,
                type: 'post',
                data: {estado:estado,id:id},
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
            var campos = {0: {
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
        });
    });
</script>