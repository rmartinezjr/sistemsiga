<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entidad $entidad
 * @var \App\Model\Entity\Formdinamic $formdinamic
 * @var \App\Model\Entity\Contacto[]|\Cake\Collection\CollectionInterface $contactos
 */

echo $this->Html->script(['lib/bootbox.min.js']);
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
//echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']);
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
            if(isset($_SESSION['answerformdinamic-save'])){
                if($_SESSION['answerformdinamic-save']==1){
                    unset($_SESSION['answerformdinamic-save']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Respuestas Guardadas Exitosamente</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>
            <div class="alert alert-success" id="alert-savedata" style="display: none;">
                <span class="icon icon-cross-circled"></span>
                <span class="message" id="msj-savedata"></span>
                <button type="button" class="close" data-dismiss="alert"></button>
            </div>
        </div>
        <div class="row">
            <?php if(strlen("Tipo de Entidad: ".$entidad->centidadtipo->nombre)>20):?>
                <div class="col-md-6">
                    <div class="col-md-9 p-l-0">
                        <h2 class="title blue-new m-b-0"><?= "Tipo de Entidad: ".$entidad->centidadtipo->nombre ?></h2>
                    </div>
                    <div class="col-md-3 state-struct text-center" style="background-color:<?php $entidad->cestado->colorbkg?>; color:<?php $entidad->cestado->colortext?>;">
                        <?= $entidad->cestado->nombre ?>
                    </div>
                </div>
                <div class="col-md-6 panel-action">
                    <?php foreach ($controltools as $btn){
                        if($btn['funcion']==="imprimir"){   ?>
                            <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php }else{?>
                            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php                   }
                    }           ?>
                </div>
            <?php else: ?>
                <div class="col-md-4">
                    <div class="col-md-8 p-l-0">
                        <h2 class="title blue-new m-b-0"><?= "Tipo de Entidad: ".$entidad->centidadtipo->nombre ?></h2>
                    </div>
                    <div class="col-md-4 state-struct text-center" style="background-color:<?php $entidad->cestado->colorbkg?>; color:<?php $entidad->cestado->colortext?>;">
                        <?= $entidad->cestado->nombre ?>
                    </div>
                </div>
                <div class="col-md-8 panel-action">
                    <?php foreach ($controltools as $btn){
                        if($btn['funcion']==="imprimir"){   ?>
                            <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php }else{?>
                            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php                   }
                    }           ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="row m-b-20">
            <div class="col-md-6 info-log-2">
                <h2 class="title m-t-0"><?=$formdinamic->alias?></h2>
            </div>
            <div class="col-md-6 info-log">
                <span class="lbl">Creado:</span><span class="lbl-data"><?=$entidad->usuario." (".date("d-m-Y H:i:s", strtotime($entidad->created)).")"?></span>
                <?php if($entidad->usuariomodif!=''){ ?>
                    <br>
                    <span class="lbl">Última Modificación:</span><span class="lbl-data"><?=$entidad->usuariomodif." (".date("d-m-Y H:i:s", strtotime($entidad->modified)).")"?></span>
                <?php          }   ?>
            </div>
        </div>
        <?php if($datos['resp']):?>
            <div class="row m-b-10">
                <div class="col-md-12 col-xs-12 text-center">
                    <div class="col-md-1 col-xs-3 pull-left btn-anteriorfd pointer fanterior"><i class="fa fa-chevron-left" aria-hidden="true"></i> Anterior</div>
                    <div class="col-md-1 col-xs-3 btn-enviarfd pointer">Enviar</div>
                    <div class="col-md-1 col-xs-3 pull-right btn-anteriorfd pointer fsiguiente">Siguiente <i class="fa fa-chevron-right" aria-hidden="true"></i></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12 m-b-20">
                    <?php if(count($datos['seccionHijo'])>0): ?>
                        <div class="col-md-12 col-xs-12 fila-data p-0 ">
                            <div class="col-md-12 col-xs-4 col-label struct-border-start color-struct-title"><?= $datos['seccionPadre'][0]->alias?></div>
                        </div>
                        <div class="col-md-12 col-xs-12 fila-data p-0">
                            <div class="col-md-12 col-xs-12 col-label struct-border-end color-struct-contet">
                                <div class="col-md-12 col-xs-12 fila-data p-0 m-t-10">
                                    <div class="col-md-12 col-xs-4 col-label struct-border-start color-struct-title"><?= $datos['seccionHijo'][0]->alias?> </div>
                                </div>
                                <?php foreach($datos['formpreguntas'] as $key): ?>
                                    <div class="col-md-12 col-xs-12 fila-data p-0">
                                        <div class="col-md-12 col-xs-12 col-label <?php if(($cont%2)!=0):?> color-struct-contet <?php endif; ?> <?php if(count($datos['formpreguntas'])==($countfp)):?> struct-border-end m-b-10 <?php endif;?>">
                                            <?php for($i=0; $i<count($datos['formrespuestas'][$key->id]); $i++): ?>
                                                <div class="col-md-6 p-r-0">
                                                    <div class="input text">
                                                        <label class="block" for="input_1"><?=$cont.". ".$key->fdpregunta->alias ?></label><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><input type="<?=$datos['formrespuestas'][$key->id][$i]['type']?>"
                                                                                                                                                                                                            name="input_<?=$key->id?>"
                                                                                                                                                                                                            id="input_<?=$key->id?>"
                                                                                                                                                                                                            onblur="setData(<?=$key->id?>)"
                                                            <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?> value="<?=$datos['formrespuestas'][$key->id][$i]['value']?>" <?php endif; ?>
                                                                                                                                                                                                            <?php if($key->params['Formpregunta']['items'][0]['value']):?>onkeyup="setRequired(<?=$key->id?>)"<?php endif; ?>
                                                                                                                                                                                                            placeholder="<?=$datos['formrespuestas'][$key->id][$i]['placeholder']?>"
                                                                                                                                                                                                            maxlength="<?=$datos['formrespuestas'][$key->id][$i]['longitudmax']?>"><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                </div>
                                            <?php endfor; ?>
                                            <div class="col-md-1 <?php if($key->params['Formpregunta']['items'][0]['value']): ?>obligatorio<?php else:?>display-none<?php endif; ?> " id="divrequired<?=$key->id?>" >obligatorio</div>
                                            <div class="col-md-5 msjextra display-none">Mensaje extra</div>
                                        </div>
                                    </div>
                                    <?php $cont++; endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-md-12 col-xs-12 fila-data p-0 ">
                            <div class="col-md-12 col-xs-4 col-label struct-border-start color-struct-title"><?= $datos['seccionPadre'][0]->alias?></div>
                        </div>
                        <?php foreach($datos['formpreguntas'] as $key): ?>
                            <div class="col-md-12 col-xs-12 fila-data p-0">
                                <div class="col-md-12 col-xs-12 col-label <?php if(($cont%2)!=0):?> color-struct-contet <?php endif; ?> <?php if(count($datos['formpreguntas'])==($cont+1)):?> struct-border-end <?php endif;?>">
                                    <?php for($i=0; $i<count($datos['formrespuestas'][$key->id]); $i++): ?>
                                        <div class="col-md-6 p-r-0">
                                            <div class="input text">
                                                <label class="block" for="input_1"><?=$cont.". ".$key->fdpregunta->alias ?></label><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><input type="<?=$datos['formrespuestas'][$key->id][$i]['type']?>"
                                                                                                                                                                                                    name="input_<?=$key->id?>"
                                                                                                                                                                                                    id="input_<?=$key->id?>"
                                                                                                                                                                                                    onblur="setData(<?=$key->id?>)"
                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?> value="<?=$datos['formrespuestas'][$key->id][$i]['value']?>" <?php endif; ?>
                                                                                                                                                                                                    <?php if($key->params['Formpregunta']['items'][0]['value']):?>onkeyup="setRequired(<?=$key->id?>)"<?php endif; ?>
                                                                                                                                                                                                    placeholder="<?=$datos['formrespuestas'][$key->id][$i]['placeholder']?>"
                                                                                                                                                                                                    maxlength="<?=$datos['formrespuestas'][$key->id][$i]['longitudmax']?>"><?=" ".$datos['formrespuestas'][$key->id][$i]['prefijo']?></div>
                                        </div>
                                    <?php endfor; ?>
                                    <div class="col-md-1 <?php if($key->params['Formpregunta']['items'][0]['value']): ?>obligatorio<?php else:?>display-none<?php endif; ?> " id="divrequired<?=$key->id?>" >obligatorio</div>
                                    <div class="col-md-5 msjextra display-none">Mensaje extra</div>
                                </div>
                            </div>
                            <?php $cont++; endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row m-b-30">
                <div class="col-md-12 col-xs-12 text-center">
                    <div class="col-md-1 col-xs-3 pull-left btn-anteriorfd pointer fanterior"><i class="fa fa-chevron-left" aria-hidden="true"></i> Anterior</div>
                    <div class="col-md-1 col-xs-3 btn-enviarfd pointer">Enviar</div>
                    <div class="col-md-1 col-xs-3 pull-right btn-anteriorfd pointer fsiguiente">Siguiente <i class="fa fa-chevron-right" aria-hidden="true"></i></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->Html->script('funciones/cargadatos') ?>
<script>
    function setRequired(val) {
        if($("#input_"+val).val()!=""){
            $("#divrequired"+val).addClass('display-none');
        }else{
            $("#divrequired"+val).removeClass('display-none');
        }
    }

    function setData(val) {
        if($("#input_"+val).val()!=""){
            var iddataform="<?=$_SESSION['fd_id_dataform']?>";
            var res=savedata(val,$("#input_"+val).val(),iddataform);
            if(res){
                $("#alert-savedata").removeClass("alert-danger");
                $("#alert-savedata").addClass("alert-success");
                $("#msj-savedata").text("Respuesta Guardada Exitosamente");
            }
            else{
                $("#alert-savedata").removeClass("alert-success");
                $("#alert-savedata").addClass("alert-danger");
                $("#msj-savedata").text("No se ha podido guardar la respuesta");
            }
            $("#alert-savedata").slideDown();

            setTimeout(function () {
                $("#alert-savedata").slideUp();
            }, 4000);
        }
    }
    jQuery(function(){
        $("#alert").slideDown();

        setTimeout(function () {
            $("#alert").slideUp();
        }, 4000);

    });
</script>
