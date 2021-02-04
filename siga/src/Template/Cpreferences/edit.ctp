<?php
/**
 * @var \App\View\AppView $this
 */

$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';

echo $this->Html->css(['lib/jsoneditor.css']);
echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js','lib/jquery.jsoneditor.js','lib/jsoneditor.js',  'funciones/validaciones.js','lib/bootbox.min.js']);
?>
<style>


    #legend {
        display: inline;
        margin-left: 30px;
    }
    #legend h2 {
        display: inline;
        font-size: 18px;
        margin-right: 20px;
    }
    #legend a {
        color: white;
        margin-right: 20px;
    }
    #legend span {
        padding: 2px 4px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        text-shadow: 1px 1px 1px black;
        background-color: black;
    }
    #legend .string  { background-color: #009408; }
    #legend .array   { background-color: #2D5B89; }
    #legend .object  { background-color: #E17000; }
    #legend .number  { background-color: #497B8D; }
    #legend .boolean { background-color: #B1C639; }
    #legend .null    { background-color: #B1C639; }

    #expander {
        cursor: pointer;
        margin-right: 20px;

    }

    #footer {
        font-size: 13px;
    }

    #rest {
        margin: 20px 0 20px 30px;
    }
    #rest label {
        font-weight: bold;
    }
    #rest-callback {
        width: 70px;
    }
    #rest-url {
        width: 700px;
    }
    label[for="json"] {
        margin-left: 30px;
        display: block;
    }
    #json-note {
        margin-left: 30px;
        font-size: 12px;
    }

    .addthis_toolbox {
        position: relative;
        top: -10px;
        margin-left: 30px;
    }

    #disqus_thread {
        margin-top: 50px;
        padding-top: 20px;
        padding-bottom: 20px;
        border-top: 1px solid gray;
        border-bottom: 1px solid gray;
    }
    .expander{
        width: 1.5% !important;
    }.property{
         width: 12.5% !important;
     }.value{
          width: 86% !important;
      }
</style>
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
        <?= $this->Form->create($cpreference, ['novalidate', 'id'=>"frm"]) ?>

        <div class="row">
            <div class="col-md-6">
                <h2 class="tittle">Editar <?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
            </div>
            <div class="col-md-6 panel-action">
                <button class="btn btn-sistem btn-save"><span>Guardar</span><i class="fa fa-floppy-o icono"></i></button>
                <?php foreach ($controltools as $btn){
                    if($btn['funcion']==="imprimir"){   ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php }
                    if($btn['funcion']==="index"){   ?>
                        <a class="btn btn-sistem <?=$btn['class']?> btn-editPref" ><span>Cancelar</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php }
                    else { ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php }
                } ?>
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
                <?= $this->Form->control("id");?>

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
                <div class="col-md-2 frm-label">Parámetros</div>
                <div class="col-md-10">
                    <?=$this->Form->control('params',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control ',
                        'value'=>json_encode($cpreference['params'],true),
                        'placeholder'=>'JSON',
                        'id'=>'json',
                        'required',
                        'rows' => 5
                    ]);?>
                </div>

        </div>
    </div>
        <div class="row">
            <div class="col-md-12">
                <div id="legend">
                    <span id="expander">Expandir </span>
                    <span class="array">Arreglo</span>
                    <span class="object">Objeto</span>
                    <span class="string">Cadena</span>
                    <span class="boolean">Boolean</span>
                    <span class="null">Null</span>
                    <span>Eliminar un nombre de propiedad para eliminar el elemento.</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="padding-right: 0px !important;">
                <div id="editor" class="json-editor"></div>
            </div>
        </div>
        <?= $this->Form->end() ?>
</div>
</div>
    <script>
        var json =  <?=json_encode($cpreference['params'],true);?>;

        $('#editor').jsonEditor(json, { change: function() {
            $('#json').html(JSON.stringify(json));
        } });
    </script>
<script>
    jQuery(function(){

        $("#json").keyup(function(e){
            e.preventDefault();
        if(($(this).val().trim()).length > 0){
        $("#legend").removeClass('hidden');
        }
        else {
        $("#legend").addClass('hidden');
        }
    });

        $("#json").blur(function (e) {
            try {
                var c = $.parseJSON($(this).val());
                $("#legend").removeClass('hidden');
            }
            catch (err) {

                $(".message").text('Error de sintaxis. El formato JSON es incorrecto.');
                $("#alert").slideDown();
                setTimeout(function () {
                    $("#alert").slideUp();
                }, 5000);
            }
        });

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
                    'campo': 'json',
                    'label': 'Parámetro',
                    'tipo': 'input'
                },
                1: {
                    'campo': 'nombre',
                    'label': 'nombre',
                    'tipo': 'input'
                }

            };

            if(validarCamp(campos))
            {
                try {
                    var c = $.parseJSON($("#json").val());
                    return true;
                }
                catch (err) {
                    $(".message").text('Error de sintaxis. El formato JSON es incorrecto.');
                    $("#alert").slideDown();
                    setTimeout(function () {
                        $("#alert").slideUp();
                    }, 5000);
                    return false;
                }
            }
            else
            {
                e.preventDefault();
            }
        });

        $(document).on("click", ".btn-editPref", function(){
            var url ="<?= \Cake\Routing\Router::url(['controller' => 'Cpreferences', 'action' => 'editPref'.'/'.$cpreference['id']], true); ?>";
            var url2 ="<?= \Cake\Routing\Router::url(['controller' => 'Cpreferences', 'action' => 'index'], true); ?>";

            var url12 ="<?= \Cake\Routing\Router::url(['controller' => 'Cpreferences', 'action' => 'getPref'], true); ?>";

            try {
                var c = $.parseJSON($("#json").val());
                 $.ajax({
                    data: {pref: <?=$cpreference['id'];?>,pram:$("#json").val()},
                    url: url12,
                    type: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        if(data.error==1)
                        {
                            bootbox.confirm({
                                message: "¿Desea guardar los cambios.?",
                                buttons: {
                                    confirm: {
                                        label: 'Si',
                                        className: 'btn-success'
                                    },
                                    cancel: {
                                        label: 'No',
                                        className: 'btn-danger'
                                    }
                                },
                                callback: function (result) {
                                    if(result) {
                                        $('#frm').attr('action', url);
                                        $('#frm').submit();
                                    }
                                    else
                                    { window.location.assign(url2);

                                    }
                                }
                            });
                        }
                        else   if(data.error==0)
                        {
                            window.location.assign(url2);
                        }

                    }
                });
            }
            catch (err) {
                window.location.assign(url2);
            }





        });
    });
    function validarCamp(campos)
    {
        var  valor=[];
        var val="";
        $.each(campos, function(item, col){
            var band = valRequired(col['campo'], col['label'], col['tipo']);
            if(band=== false){
                valor.push("0");
            }
            else if(band=== true)
            {
                valor.push("1");
            }
        });
        if(! valor.includes("0"))
        {
            val=1;
        }
        return val;
    }
</script>