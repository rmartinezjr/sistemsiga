<?php
echo $this->Html->script(['lib/bootbox.min.js','lib/moment.min']);
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';

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
            if(isset($_SESSION['wf-save'])){
                if($_SESSION['wf-save']==0){
                    unset($_SESSION['wf-save']);?>
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">No tienes permitido realizar el cambio de etapa seleccionado.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>
        </div>
        <div class="row">
            <?php if(strlen("Tipo de Entidad: ".$entidad->centidadtipo->nombre)>20):?>
                <div class="col-md-6">
                    <h2 class="title blue-new m-b-0"><?= "Tipo de Entidad: ".$entidad->centidadtipo->nombre ?></h2>
                </div>
                <div class="col-md-6 panel-action">
                    <a class="btn btn-sistem btn-detalles" href="<?=$real_url.'view/'.$entidad->id?>"><span><?="Ver Detalles"?></span><i class="fa fa-list icono"></i></a>
                    <?php foreach ($controltools as $btn){
                        if($btn['funcion']==="imprimir"){   ?>
                            <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php }else{ ?>
                            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php                   }
                    }           ?>
                </div>
            <?php else: ?>
                <div class="col-md-4">
                    <h2 class="title blue-new m-b-0"><?= "Tipo de Entidad: ".$entidad->centidadtipo->nombre ?></h2>
                </div>
                <div class="col-md-8 panel-action">
                    <a class="btn btn-sistem btn-detalles" href="<?=$real_url.'view/'.$entidad->id?>"><span><?="Ver Detalles"?></span><i class="fa fa-list icono"></i></a>
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
                <div class="col-md-9 p-l-0">
                    <h2 class="title m-t-0"><?=$formdinamic->alias?></h2>
                </div>
                <div class="col-md-3 state-struct text-center m-t-0" <?php if(!empty($carga)):?> style="background-color:<?=$carga->cestado->colorbkg?>; color:<?=$carga->cestado->colortext?>;"<?php endif; ?>>
                    <?php if(!empty($carga)):?> <?= $carga->cestado->nombre ?> <?php endif; ?>
                </div>
            </div>
            <?= $this->element('created_modified_record', ['modelo' => $entidad, 'col'=>'col-md-6']) ?>
        </div>
        <?php if($datos['resp']):?>
            <div class="row m-b-10">
                <div class="col-md-12 col-xs-12 text-center">
                    <?php if($datos['anterior']): ?><div class="col-md-1 col-xs-3 pull-left btn-anteriorfd pointer fanterior"><i class="fa fa-chevron-left" aria-hidden="true"></i> Anterior</div> <?php endif; ?>
                    <div class="col-md-9 col-xs-10 p-l-0" id="btn_wf_i">
                        <?php foreach($acciones as $key): ?>
                            <div class="col-md-2 col-xs-4 btn-enviarfd pointer" style="background-color:<?=$key['color_fondo']?>; color:<?=$key['color_texo']?>;" onclick="setTypesave(<?=$key['id']?>)" title="Enviar a <?=$key['nombre']?>"><?=$key['nombre']?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php if($datos['siguiente']): ?> <div class="col-md-1 col-xs-3 pull-right btn-anteriorfd pointer fsiguiente">Siguiente <i class="fa fa-chevron-right" aria-hidden="true"></i></div> <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <?php foreach($datos['formpreguntas'] as $key): ?>
                        <?php if($datos['id_aux_formseccion']!=$key->formseccion_id): $datos['id_aux_formseccion']=$key->formseccion_id; $datos['contador_respuesta_individual']=1; ?>
                            <?php if($datos['existe_seccion_hijo']):?><!--Cerra divs de encabezados cuando existen seccion padre-hijo -->
                                </div>
                                </div>
                            <?php endif; ?>
                            <?php if($key->formseccion->formseccion_id!=0): $datos['existe_seccion_hijo']=true;?><!-- Encabezado secciones hijo-->
                                <div class="col-md-12 col-xs-12 fila-data p-0">
                                    <div class="col-md-12 col-xs-4 col-label struct-border-start color-struct-title"><?= $datos['allSecciones_alias'][$key->formseccion->formseccion_id]?></div>
                                </div>
                                <div class="col-md-12 col-xs-12 fila-data p-0">
                                    <div class="col-md-12 col-xs-12 col-label struct-border-end color-struct-contet m-b-10">
                                        <div class="col-md-12 col-xs-12 fila-data p-0 m-t-10">
                                            <div class="col-md-12 col-xs-4 col-label struct-border-start color-struct-title"><?= $key->formseccion->alias?> </div>
                                        </div>
                            <?php else: $datos['existe_seccion_hijo']=false;?><!-- Encabezado secciones padre-->
                                <div class="col-md-12 col-xs-12 fila-data p-0">
                                    <div class="col-md-12 col-xs-4 col-label struct-border-start color-struct-title"><?= $key->formseccion->alias?></div>
                                </div>
                            <?php endif; ?>
                        <?php else: $datos['contador_respuesta_individual']++; ?>
                        <?php endif; ?>
                        <?php if(isset($datos['formrespuestas'][$key->id])): ?>
                            <div class="col-md-12 col-xs-12 fila-data p-0">
                                <div class="col-md-12 col-xs-12 col-label <?php if(($cont%2)!=0):?> color-struct-contet <?php endif; ?> <?php if($datos['array_contador_preguntas'][$key->formseccion_id]==$datos['contador_respuesta_individual']):?> struct-border-end m-b-10 <?php endif;?>">
                                    <div class="alert alert-success display-none" id="alert-savedata-<?=$key->id?>">
                                        <span class="icon icon-cross-circled"></span>
                                        <span class="message font-normal" id="msj-savedata-<?=$key->id?>"></span>
                                        <button type="button" class="close" data-dismiss="alert"></button>
                                    </div>
                                    <?php for($i=0; $i<count($datos['formrespuestas'][$key->id]); $i++): ?>
                                        <div class="col-md-12 p-l-0">
                                            <div class="col-md-6 p-r-0 p-l-0">
                                                <?php if(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'text')==0):?>
                                                    <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                    <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                            <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                        <?php else:?>
                                                            <span class="not-answer">No existe respuesta que mostrar.</span>
                                                        <?php endif; ?>
                                                        <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'select')==0): ?>
                                                    <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                    <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                            <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['option'][$datos['formrespuestas'][$key->id][$i]['value']]['label']?></span>
                                                        <?php else:?>
                                                            <span class="not-answer">No existe respuesta que mostrar.</span>
                                                        <?php endif; ?>
                                                        <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?> </div>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'radio')==0): ?>
                                                    <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                    <div class="content-answer">
                                                        <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                            <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['option'][$datos['formrespuestas'][$key->id][$i]['value']]['label']?></span>
                                                        <?php else:?>
                                                            <span class="not-answer">No existe respuesta que mostrar.</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'checkbox')==0): ?>
                                                    <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                    <div class="content-answer">
                                                        <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'][0]) && $datos['formrespuestas'][$key->id][$i]['value'][0]!=""): ?>
                                                            <ol>
                                                                <?php for($opt=0; $opt<count($datos['formrespuestas'][$key->id][$i]['option']); $opt++): ?>
                                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['chk'][$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']])):?>
                                                                        <li><?=$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']?></li>
                                                                    <?php endif; ?>
                                                                <?php endfor; ?>
                                                            </ol>
                                                        <?php else:?>
                                                            <span class="not-answer">No existe respuesta que mostrar.</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'text-area')==0):?>
                                                    <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                    <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                            <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                        <?php else:?>
                                                            <span class="not-answer">No existe respuesta que mostrar.</span>
                                                        <?php endif; ?>
                                                        <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'date')==0 || strcmp($datos['formrespuestas'][$key->id][$i]['type'],'datetime')==0 || strcmp($datos['formrespuestas'][$key->id][$i]['type'],'time')==0): ?>
                                                    <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                    <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                            <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                        <?php else:?>
                                                            <span class="not-answer">No existe respuesta que mostrar.</span>
                                                        <?php endif; ?>
                                                        <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'file')==0): ?>
                                                    <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                    <span class="a-href m-l-20 font-normal pointer value-file <?php if(!isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>" ><?=$datos['formrespuestas'][$key->id][$i]['value'];?></span>
                                                    <div class="content-answer <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>">
                                                        <span class="not-answer">No existe respuesta que mostrar.</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6 msjextra <?php if($i!=0): ?>m-t-10<?php endif; ?><?php if(empty($datos['formrespuestas'][$key->id][$i]['descripcion'])):?>display-none<?php endif; ?> <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>"><?=$datos['formrespuestas'][$key->id][$i]['descripcion']?></div>
                                            <div class="col-md-4 txt-align-r version-color <?php if(!isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>m-t-29 display-none<?php endif; ?> <?php if($i==0): ?>m-t-20<?php else :?>m-t-5<?php endif; ?>">
                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['version'])): ?>
                                                    <?php for($v=0; $v<count($datos['formrespuestas'][$key->id][$i]['version'])-1; $v++): ?>
                                                        <span class="version-file pointer vf_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" data-idversion="<?=$datos['formrespuestas'][$key->id][$i]['version'][$v]['id']?>">versión <?=$datos['formrespuestas'][$key->id][$i]['version'][$v]['fileversion']?></span><span> - </span>
                                                    <?php endfor;?>
                                                    <span class="version-file font-bold pointer vf_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" data-idversion="<?=$datos['formrespuestas'][$key->id][$i]['version'][$v]['id']?>">versión <?=$datos['formrespuestas'][$key->id][$i]['version'][$v]['fileversion']?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-2 m-b-10 <?php if(!isset($datos['formrespuestas'][$key->id][$i]['version'])): ?>m-t-25 display-none<?php endif; ?> <?php if($i==0): ?>m-t-16<?php else :?>m-t-1<?php endif; ?>"><a id="ver_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="btn-ver m-r-11" target="_blank" <?php if(isset($datos['formrespuestas'][$key->id][$i]['version'])):?> href="<?=$real_url."showFile/".$datos['formrespuestas'][$key->id][$i]['version'][count($datos['formrespuestas'][$key->id][$i]['version'])-1]['id']?>"<?php else: ?>href="#"<?php endif; ?>>Ver</a><a id="download_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="btn-ver" <?php if(isset($datos['formrespuestas'][$key->id][$i]['version'])):?> href="<?=$real_url."download/".$datos['formrespuestas'][$key->id][$i]['version'][count($datos['formrespuestas'][$key->id][$i]['version'])-1]['id']?>" <?php else: ?>href=""<?php endif; ?>>Descargar</a></div>
                                        </div>
                                        <?php $countfp++; endfor; ?>
                                </div>
                            </div>
                            <?php $cont++; endif;?>
                    <?php  endforeach; ?>
                    <?php if($datos['existe_seccion_hijo']):?><!--Cerra divs de encabezados cuando existen seccion padre-hijo -->
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row m-b-30">
                <div class="col-md-12 col-xs-12 text-center">
                    <?php if($datos['anterior']): ?><div class="col-md-1 col-xs-3 pull-left btn-anteriorfd pointer fanterior"><i class="fa fa-chevron-left" aria-hidden="true"></i> Anterior</div><?php endif; ?>
                    <div class="col-md-9 col-xs-10 p-l-0" id="btn_wf_f">
                        <?php foreach($acciones as $key): ?>
                            <div class="col-md-2 col-xs-4 btn-enviarfd pointer" style="background-color:<?=$key['color_fondo']?>; color:<?=$key['color_texo']?>;" onclick="setTypesave(<?=$key['id']?>)" title="Enviar a <?=$key['nombre']?>"><?=$key['nombre']?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php if($datos['siguiente']): ?><div class="col-md-1 col-xs-3 pull-right btn-anteriorfd pointer fsiguiente">Siguiente <i class="fa fa-chevron-right" aria-hidden="true"></i></div><?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    var iddataform="<?=$id_dataform?>";
    var classname_versionfile = document.getElementsByClassName("version-file");//clase para versión de los archivos que se han ingresado
    //función para mostrar div de versiones de archivos
    function myFileVersion(){
        var attribute = this.getAttribute("data-attribute");
        $(".vf_"+attribute).removeClass("font-bold");
        $(this).addClass("font-bold");
        var idversion = this.getAttribute("data-idversion");
        var url=getUrl()+"showFile/"+idversion;
        $("#ver_"+attribute).attr("href",url);
        var url=getUrl()+"download/"+idversion;
        $("#download_"+attribute).attr("href",url);
    }
    for (var i = 0; i < classname_versionfile.length; i++) {
        classname_versionfile[i].addEventListener('click', myFileVersion, false);
    }

    function setTypesave(val){
        var url = getUrl() + "changeStateWf/" +val+"/show";
        window.location.assign(url);
    }

    jQuery(function(){
        $('.fsiguiente').click( function(){
            var idformdinamic="<?=$_SESSION['fd_if_formdinamic']?>";
            var url = getUrl() + "show/"+"<?=$_SESSION['fd_if_formdinamic']."/".$_SESSION['fd_id_entidad']."/"?>"+iddataform+"/next";
            window.location.assign(url);
        });

        $('.fanterior').click( function(){
            var idformdinamic="<?=$_SESSION['fd_if_formdinamic']?>";
            var url = getUrl() + "show/"+"<?=$_SESSION['fd_if_formdinamic']."/".$_SESSION['fd_id_entidad']."/"?>"+iddataform+"/previous";
            window.location.assign(url);
        });

        $("#alert").slideDown();

        setTimeout(function () {
            $("#alert").slideUp();
        }, 4000);
    });
</script>
