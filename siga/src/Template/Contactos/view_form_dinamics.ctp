<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contacto $contacto
 * @var \App\Model\Entity\Formdinamic $formdinamic
 * @var \App\Model\Entity\Entidad[]|\Cake\Collection\CollectionInterface $entidads
 */
echo $this->Html->css(['lib/bootstrap-datetimepicker.min']);
echo $this->Html->script(['lib/bootbox.min.js','lib/moment.min', 'lib/datetimepicker']);
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
            if(isset($_SESSION['wf-save'])){
                if($_SESSION['wf-save']==0){
                    unset($_SESSION['wf-save']);?>
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">No tienes permitido realizar el cambio de etapa seleccionado.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php }elseif($_SESSION['wf-save']==-1){ unset($_SESSION['wf-save']); ?>
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Debes completar todas las preguntas obligatorias para pasar a la siguiente etapa.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php }
            }
            ?>
            <div class="alert alert-danger no-display" id="alert-danger">
                <span class="icon icon-cross-circled"></span>
                <span class="message"></span>
                <button type="button" class="close" data-dismiss="alert"></button>
            </div>
        </div>
        <div class="row">
            <?php if(strlen("Tipo de Contacto: ".$contacto->ccontactotipo->nombre)>20):?>
                <div class="col-md-6">
                    <h2 class="title blue-new m-b-0"><?= "Tipo de Contacto: ".$contacto->ccontactotipo->nombre ?></h2>
                </div>
                <div class="col-md-6 panel-action">
                    <button class="btn btn-sistem btn-save"><span>Guardar</span><i class="fa fa-floppy-o icono"></i></button>
                    <a class="btn btn-sistem btn-detalles" href="<?=$real_url.'view/'.$contacto->id?>"><span><?="Ver Detalles"?></span><i class="fa fa-list icono"></i></a>
                    <?php foreach ($controltools as $btn){
                        if($btn['funcion']==="imprimir"){   ?>
                            <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= \Cake\Routing\Router::url(['controller' => $btn['modelo'], 'action' => 'index'], true) .'/'.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php                }else{?>
                            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=\Cake\Routing\Router::url(['controller' => $btn['modelo'], 'action' => 'index'], true) .'/'.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php                   }
                    }           ?>
                </div>
            <?php else: ?>
                <div class="col-md-4">
                    <h2 class="title blue-new m-b-0"><?= "Tipo de Contacto: ".$contacto->ccontactotipo->nombre ?></h2>
                </div>
                <div class="col-md-8 panel-action">
                    <button class="btn btn-sistem btn-save"><span>Guardar</span><i class="fa fa-floppy-o icono"></i></button>
                    <a class="btn btn-sistem btn-detalles" href="<?=$real_url.'view/'.$contacto->id?>"><span><?="Ver Detalles"?></span><i class="fa fa-list icono"></i></a>
                    <?php foreach ($controltools as $btn){
                        if($btn['funcion']==="imprimir"){   ?>
                            <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= \Cake\Routing\Router::url(['controller' => $btn['modelo'], 'action' => 'index'], true) .'/'.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                        <?php                }else{?>
                            <a class="btn btn-sistem <?=$btn['class']?>" href="<?=\Cake\Routing\Router::url(['controller' => $btn['modelo'], 'action' => 'index'], true) .'/'.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
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
            <?= $this->element('created_modified_record', ['modelo' => $contacto, 'col'=>'col-md-6']) ?>
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
                                                    <?php if($escritura):?>
                                                        <div class="input text">
                                                            <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label><div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><input type="<?=$datos['formrespuestas'][$key->id][$i]['type']?>"
                                                                    name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                    id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                    onblur="setData(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['pattern'].",".$datos['formrespuestas'][$key->id][$i]['msj'].",".$datos['formrespuestas'][$key->id][$i]['id']?>)"
                                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?> value="<?=$datos['formrespuestas'][$key->id][$i]['value']?>" <?php endif; ?>
                                                                    <?php if($key->params['Formpregunta']['items'][0]['value']):?>onkeyup="setRequired(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['id']?>)"<?php endif; ?>
                                                                    placeholder="<?=$datos['formrespuestas'][$key->id][$i]['placeholder']?>"
                                                                    maxlength="<?=$datos['formrespuestas'][$key->id][$i]['longitudmax']?>"/><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                        </div>
                                                    <?php else:?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                            <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                                <div class="input text">
                                                                    <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label><div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><input type="<?=$datos['formrespuestas'][$key->id][$i]['type']?>"
                                                                            name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                            id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                            onblur="setData(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['pattern'].",".$datos['formrespuestas'][$key->id][$i]['msj'].",".$datos['formrespuestas'][$key->id][$i]['id']?>)"
                                                                            <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?> value="<?=$datos['formrespuestas'][$key->id][$i]['value']?>" <?php endif; ?>
                                                                            <?php if($key->params['Formpregunta']['items'][0]['value']):?>onkeyup="setRequired(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['id']?>)"<?php endif; ?>
                                                                            placeholder="<?=$datos['formrespuestas'][$key->id][$i]['placeholder']?>"
                                                                            maxlength="<?=$datos['formrespuestas'][$key->id][$i]['longitudmax']?>"/><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                                </div>
                                                            <?php else: ?>
                                                                <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                        <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                                    <?php else:?>
                                                                        <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                    <?php endif; ?>
                                                                    <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                            <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                    <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                                <?php else:?>
                                                                    <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                <?php endif; ?>
                                                                <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                        <?php endif; ?>
                                                    <?php endif;?>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'select')==0): ?>
                                                    <?php if($escritura):?>
                                                        <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                        <div class="input select">
                                                            <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><select name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                                                                                          id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                                                                                          data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                                                                                          class="form-control select-control">
                                                                    <option value="null">Seleccionar</option>
                                                                    <?php for($opt=0; $opt<count($datos['formrespuestas'][$key->id][$i]['option']); $opt++): ?>
                                                                        <option value="<?=$opt?>" <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): if($datos['formrespuestas'][$key->id][$i]['value']==$opt): ?>selected<?php endif; endif; ?>><?=$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']?></option>
                                                                    <?php endfor; ?>
                                                                </select><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                        </div>
                                                    <?php else:?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                            <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                                <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <div class="input select">
                                                                    <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><select name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                                                                                                  id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                                                                                                  data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                                                                                                  class="form-control select-control">
                                                                            <option value="null">Seleccionar</option>
                                                                            <?php for($opt=0; $opt<count($datos['formrespuestas'][$key->id][$i]['option']); $opt++): ?>
                                                                                <option value="<?=$opt?>" <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): if($datos['formrespuestas'][$key->id][$i]['value']==$opt): ?>selected<?php endif; endif; ?>><?=$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']?></option>
                                                                            <?php endfor; ?>
                                                                        </select><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                                </div>
                                                            <?php else: ?>
                                                                <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                        <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['option'][$datos['formrespuestas'][$key->id][$i]['value']]['label']?></span>
                                                                    <?php else:?>
                                                                        <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                    <?php endif; ?>
                                                                    <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?> </div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                            <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                    <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['option'][$datos['formrespuestas'][$key->id][$i]['value']]['label']?></span>
                                                                <?php else:?>
                                                                    <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                <?php endif; ?>
                                                                <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?> </div>
                                                        <?php endif; ?>
                                                    <?php endif;?>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'radio')==0): ?>
                                                    <?php if($escritura):?>
                                                        <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                        <div class="input radio">
                                                            <div class="content-answer">
                                                                <?php for($opt=0; $opt<count($datos['formrespuestas'][$key->id][$i]['option']); $opt++): ?>
                                                                    <div class="col-md-3">
                                                                        <label for="rad_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">
                                                                            <input type="<?=$datos['formrespuestas'][$key->id][$i]['type']?>" name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                   value="<?=$opt?>"
                                                                                   class="radio-style radio-style-up"
                                                                                   data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                   <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): if($datos['formrespuestas'][$key->id][$i]['value']==$opt): ?>checked<?php endif; endif; ?>/><?=$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']?></label>
                                                                    </div>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>
                                                    <?php else:?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                            <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                                <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <div class="input radio">
                                                                    <div class="content-answer">
                                                                        <?php for($opt=0; $opt<count($datos['formrespuestas'][$key->id][$i]['option']); $opt++): ?>
                                                                            <div class="col-md-3">
                                                                                <label for="rad_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">
                                                                                    <input type="<?=$datos['formrespuestas'][$key->id][$i]['type']?>" name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                           value="<?=$opt?>"
                                                                                           class="radio-style radio-style-up"
                                                                                           data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                           <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): if($datos['formrespuestas'][$key->id][$i]['value']==$opt): ?>checked<?php endif; endif; ?>/><?=$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']?></label>
                                                                            </div>
                                                                        <?php endfor; ?>
                                                                    </div>
                                                                </div>
                                                            <?php else: ?>
                                                                <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <div class="content-answer">
                                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                        <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['option'][$datos['formrespuestas'][$key->id][$i]['value']]['label']?></span>
                                                                    <?php else:?>
                                                                        <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                            <div class="content-answer">
                                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                    <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['option'][$datos['formrespuestas'][$key->id][$i]['value']]['label']?></span>
                                                                <?php else:?>
                                                                    <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif;?>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'checkbox')==0): ?>
                                                    <?php if($escritura):?>
                                                        <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                        <div class="input radio">
                                                            <div class="content-answer">
                                                                <?php for($opt=0; $opt<count($datos['formrespuestas'][$key->id][$i]['option']); $opt++): ?>
                                                                    <div class="col-md-3 p-l-0">
                                                                        <label style="padding-left:0;" for="rad_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">
                                                                            <input type="<?=$datos['formrespuestas'][$key->id][$i]['type']?>" name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                   value="<?=$opt?>"
                                                                                   class="radio-style radio-style-up"
                                                                                   data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['chk'][$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']])):?><?=$datos['formrespuestas'][$key->id][$i]['chk'][$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']]?><?php endif; ?>/> <?=$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']?></label>
                                                                    </div>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>
                                                    <?php else:?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                            <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                                <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <div class="input radio">
                                                                    <div class="content-answer">
                                                                        <?php for($opt=0; $opt<count($datos['formrespuestas'][$key->id][$i]['option']); $opt++): ?>
                                                                            <div class="col-md-3 p-l-0">
                                                                                <label style="padding-left:0;" for="rad_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">
                                                                                    <input type="<?=$datos['formrespuestas'][$key->id][$i]['type']?>" name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                           value="<?=$opt?>"
                                                                                           class="radio-style radio-style-up"
                                                                                           data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                        <?php if(isset($datos['formrespuestas'][$key->id][$i]['chk'][$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']])):?><?=$datos['formrespuestas'][$key->id][$i]['chk'][$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']]?><?php endif; ?>/> <?=$datos['formrespuestas'][$key->id][$i]['option'][$opt]['label']?></label>
                                                                            </div>
                                                                        <?php endfor; ?>
                                                                    </div>
                                                                </div>
                                                            <?php else: ?>
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
                                                            <?php endif; ?>
                                                        <?php else: ?>
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
                                                        <?php endif; ?>
                                                    <?php endif;?>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'text-area')==0):?>
                                                    <?php if($escritura):?>
                                                        <div class="input textarea">
                                                            <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label><div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><textarea name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                   id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                   onblur="setData(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['pattern'].",".$datos['formrespuestas'][$key->id][$i]['msj'].",".$datos['formrespuestas'][$key->id][$i]['id']?>)"
                                                                   <?php if($key->params['Formpregunta']['items'][0]['value']):?>onkeyup="setRequired(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['id']?>)"<?php endif; ?>
                                                                   placeholder="<?=$datos['formrespuestas'][$key->id][$i]['placeholder']?>"
                                                                   maxlength="<?=$datos['formrespuestas'][$key->id][$i]['longitudmax']?>"
                                                                   div="form-group"
                                                                   class="control-tarea"
                                                                   rows="3"><?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?><?=$datos['formrespuestas'][$key->id][$i]['value']?><?php endif; ?></textarea><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                        </div>
                                                    <?php else:?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                            <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                                <div class="input textarea">
                                                                    <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label><div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><textarea name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                           id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                           onblur="setData(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['pattern'].",".$datos['formrespuestas'][$key->id][$i]['msj'].",".$datos['formrespuestas'][$key->id][$i]['id']?>)"
                                                                           <?php if($key->params['Formpregunta']['items'][0]['value']):?>onkeyup="setRequired(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['id']?>)"<?php endif; ?>
                                                                           placeholder="<?=$datos['formrespuestas'][$key->id][$i]['placeholder']?>"
                                                                           maxlength="<?=$datos['formrespuestas'][$key->id][$i]['longitudmax']?>"
                                                                           div="form-group"
                                                                           class="control-tarea"
                                                                           rows="3"><?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?><?=$datos['formrespuestas'][$key->id][$i]['value']?><?php endif; ?></textarea><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                                </div>
                                                            <?php else: ?>
                                                                <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                        <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                                    <?php else:?>
                                                                        <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                    <?php endif; ?>
                                                                    <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                            <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                    <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                                <?php else:?>
                                                                    <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                <?php endif; ?>
                                                                <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                        <?php endif; ?>
                                                    <?php endif;?>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'date')==0 || strcmp($datos['formrespuestas'][$key->id][$i]['type'],'datetime')==0 || strcmp($datos['formrespuestas'][$key->id][$i]['type'],'time')==0): ?>
                                                    <?php if($escritura):?>
                                                        <div class="form-group" style="margin-bottom: 0 !important;">
                                                            <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                            <div class="input-group content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><input type="text" id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" style="<?php if(!empty($datos['formrespuestas'][$key->id][$i]['prefijo']) && !empty($datos['formrespuestas'][$key->id][$i]['sufijo'])):?>width:340px; <?php elseif(!empty($datos['formrespuestas'][$key->id][$i]['prefijo']) || !empty($datos['formrespuestas'][$key->id][$i]['sufijo'])):?>width: 377px;<?php endif; ?>" class="style-datetime txt-date <?=$datos['formrespuestas'][$key->id][$i]['type']?>"
                                                                                                                                                                     name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                                                                                                     onblur="setData(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['pattern'].",".$datos['formrespuestas'][$key->id][$i]['msj'].",".$datos['formrespuestas'][$key->id][$i]['id']?>)"
                                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?> value="<?=$datos['formrespuestas'][$key->id][$i]['value']?>" data-datedb="<?=$datos['formrespuestas'][$key->id][$i]['value']?>" <?php endif; ?>
                                                                                                                                                                     <?php if($key->params['Formpregunta']['items'][0]['value']):?>onkeyup="setRequired(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['id']?>)"<?php endif; ?>
                                                                                                                                                                     placeholder="<?=$datos['formrespuestas'][$key->id][$i]['placeholder']?>"
                                                                                                                                                                     readonly/>
                                                    <span class="input-group-addon">
                                                    <i class="<?php if(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'time')==0): ?>fa fa-clock-o<?php else:?>fa fa-calendar<?php endif;?>"></i>
                                                    </span><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                        </div>
                                                    <?php else:?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                            <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                                <div class="form-group" style="margin-bottom: 0 !important;">
                                                                    <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                    <div class="input-group content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?><input type="text" id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" style="<?php if(!empty($datos['formrespuestas'][$key->id][$i]['prefijo']) && !empty($datos['formrespuestas'][$key->id][$i]['sufijo'])):?>width:340px; <?php elseif(!empty($datos['formrespuestas'][$key->id][$i]['prefijo']) || !empty($datos['formrespuestas'][$key->id][$i]['sufijo'])):?>width: 377px;<?php endif; ?>" class="style-datetime txt-date <?=$datos['formrespuestas'][$key->id][$i]['type']?>"
                                                                                                                                                                             name="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"
                                                                                                                                                                             onblur="setData(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['pattern'].",".$datos['formrespuestas'][$key->id][$i]['msj'].",".$datos['formrespuestas'][$key->id][$i]['id']?>)"
                                                                            <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?> value="<?=$datos['formrespuestas'][$key->id][$i]['value']?>" data-datedb="<?=$datos['formrespuestas'][$key->id][$i]['value']?>" <?php endif; ?>
                                                                                                                                                                             <?php if($key->params['Formpregunta']['items'][0]['value']):?>onkeyup="setRequired(<?=$key->id.",".$datos['formrespuestas'][$key->id][$i]['id']?>)"<?php endif; ?>
                                                                                                                                                                             placeholder="<?=$datos['formrespuestas'][$key->id][$i]['placeholder']?>"
                                                                                                                                                                             readonly/>
                                                    <span class="input-group-addon">
                                                    <i class="<?php if(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'time')==0): ?>fa fa-clock-o<?php else:?>fa fa-calendar<?php endif;?>"></i>
                                                    </span><?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                                </div>
                                                            <?php else: ?>
                                                                <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                                    <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                        <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                                    <?php else:?>
                                                                        <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                    <?php endif; ?>
                                                                    <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                            <div class="content-answer"><?=$datos['formrespuestas'][$key->id][$i]['prefijo']." "?>
                                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>
                                                                    <span class="answer"><?=$datos['formrespuestas'][$key->id][$i]['value']?></span>
                                                                <?php else:?>
                                                                    <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                <?php endif; ?>
                                                                <?=" ".$datos['formrespuestas'][$key->id][$i]['sufijo']?></div>
                                                        <?php endif; ?>
                                                    <?php endif;?>
                                                <?php elseif(strcmp($datos['formrespuestas'][$key->id][$i]['type'],'file')==0): ?>
                                                    <?php if($escritura):?>
                                                        <div id="contenedor_file_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">
                                                            <form enctype="multipart/form-data" id="formFile_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="m-0">
                                                                <div class="input text">
                                                                    <input type="hidden" name="idformpregunta" value="<?=$key->id?>"/>
                                                                    <input type="hidden" name="idformrespuesta" value="<?=$datos['formrespuestas'][$key->id][$i]['id']?>"/>
                                                                    <input type="hidden" name="iddataform" value="<?=$id_dataform?>" class="iddataform"/>
                                                                    <input type="file" name="file" id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="inputfile" />
                                                                    <div class="col-md-8 p-l-0">
                                                                        <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                    </div>
                                                                    <div id="obligatorio_e_p_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="col-md-4 p-l-0 <?php if($key->params['Formpregunta']['items'][0]['value']): ?>obligatorio-e-p<?php else:?>display-none<?php endif; ?> <?php if(!isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>">Obligatorio</div>
                                                                    <div class="m-l-0 col-md-12 p-l-0">
                                                                        <div class="file-style-up content-answer file-style" data-id="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">
                                                                            <i class="fa fa-paperclip" aria-hidden="true"></i><span id="span_file_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>class="a-href"<?php endif; ?>><?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?> <?=$datos['formrespuestas'][$key->id][$i]['value']?> <?php else: ?> Adjuntar Archivo <?php endif; ?></span><button class="pull-right btn-file" type="button">Examinar</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    <?php else:?>
                                                        <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                            <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                                <div id="contenedor_file_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">
                                                                    <form enctype="multipart/form-data" id="formFile_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="m-0">
                                                                        <div class="input text">
                                                                            <input type="hidden" name="idformpregunta" value="<?=$key->id?>"/>
                                                                            <input type="hidden" name="idformrespuesta" value="<?=$datos['formrespuestas'][$key->id][$i]['id']?>"/>
                                                                            <input type="hidden" name="iddataform" value="<?=$id_dataform?>" class="iddataform"/>
                                                                            <input type="file" name="file" id="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="inputfile" />
                                                                            <div class="col-md-8 p-l-0">
                                                                                <label for="input_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                            </div>
                                                                            <div id="obligatorio_e_p_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="col-md-4 p-l-0 <?php if($key->params['Formpregunta']['items'][0]['value']): ?>obligatorio-e-p<?php else:?>display-none<?php endif; ?> <?php if(!isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>">Obligatorio</div>
                                                                            <div class="m-l-0 col-md-12 p-l-0">
                                                                                <div class="file-style-up content-answer file-style" data-id="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">
                                                                                    <i class="fa fa-paperclip" aria-hidden="true"></i><span id="span_file_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>class="a-href"<?php endif; ?>><?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?> <?=$datos['formrespuestas'][$key->id][$i]['value']?> <?php else: ?> Adjuntar Archivo <?php endif; ?></span><button class="pull-right btn-file" type="button">Examinar</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            <?php else: ?>
                                                                <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                                <span class="a-href m-l-20 font-normal pointer value-file <?php if(!isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>"><?=$datos['formrespuestas'][$key->id][$i]['value'];?></span>
                                                                <div class="content-answer <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>">
                                                                    <span class="not-answer">No existe respuesta que mostrar.</span>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <label class="<?php if($i!=0): ?>no-visible<?php endif; ?>"><span class="font-bold"><?=$cont.". "?></span><?=$key->fdpregunta->alias ?></label>
                                                            <span class="a-href m-l-20 font-normal pointer value-file <?php if(!isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>"><?=$datos['formrespuestas'][$key->id][$i]['value'];?></span>
                                                            <div class="content-answer <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>">
                                                                <span class="not-answer">No existe respuesta que mostrar.</span>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif;?>
                                                <?php endif; ?>
                                            </div>
                                            <?php  if($i==0): ?>
                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['observ'])): ?>
                                                    <?php if($datos['formrespuestas'][$key->id][$i]['flag_observ']):?>
                                                        <div class="col-md-2 txt-observ"><?=$datos['formrespuestas'][$key->id][$i]['text_observ']?></div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <div class="col-md-1 <?php if($i!=0): ?>m-t-10<?php endif; ?> <?php if($key->params['Formpregunta']['items'][0]['value']): ?>obligatorio<?php else:?>display-none<?php endif; ?> <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])):?>display-none<?php endif; ?>" id="divrequired_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>">obligatorio</div>
                                            <div class="col-md-3 msjextra <?php if($i!=0): ?>m-t-10<?php endif; ?><?php if(empty($datos['formrespuestas'][$key->id][$i]['descripcion'])):?>display-none<?php endif; ?> <?php if(isset($datos['formrespuestas'][$key->id][$i]['value'])): ?>display-none<?php endif; ?>" id="msjextra_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>"><?=$datos['formrespuestas'][$key->id][$i]['descripcion']?></div>
                                            <div id="div_btn_vw_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="col-md-2 m-b-10 pull-right <?php if(!isset($datos['formrespuestas'][$key->id][$i]['version'])): ?><?php if($i==0):?>m-t-25<?php else: ?>m-t-14<?php endif; ?> display-none<?php endif; ?>
                                            <?php  if($i==0): ?>
                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['observ'])): ?>
                                                    <?php if($datos['formrespuestas'][$key->id][$i]['flag_observ']):?>
                                                        m-t-25
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                    <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                        m-t-14
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>"><a id="ver_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="btn-ver m-r-11" target="_blank" <?php if(isset($datos['formrespuestas'][$key->id][$i]['version'])):?> href="<?=$real_url."showFile/".$datos['formrespuestas'][$key->id][$i]['version'][count($datos['formrespuestas'][$key->id][$i]['version'])-1]['id']?>"<?php else: ?>href="#"<?php endif; ?>>Ver</a><a id="download_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="btn-ver" <?php if(isset($datos['formrespuestas'][$key->id][$i]['version'])):?> href="<?=$real_url."download/".$datos['formrespuestas'][$key->id][$i]['version'][count($datos['formrespuestas'][$key->id][$i]['version'])-1]['id']?>" <?php else: ?>href=""<?php endif; ?>>Descargar</a></div>
                                            <div id="div_version_file_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" class="col-md-2 txt-align-r version-color pull-right <?php if(!isset($datos['formrespuestas'][$key->id][$i]['value'])): ?><?php if($i==0):?>m-t-29<?php else: ?>m-t-17<?php endif; ?> display-none<?php endif; ?>
                                            <?php  if($i==0): ?>
                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['observ'])): ?>
                                                    <?php if($datos['formrespuestas'][$key->id][$i]['flag_observ']):?>
                                                        m-t-29
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if(isset($datos['formrespuestas'][$key->id][0]['observ'])): ?>
                                                    <?php if($datos['formrespuestas'][$key->id][0]['flag_observ']):?>
                                                        m-t-17
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            ">
                                                <?php if(isset($datos['formrespuestas'][$key->id][$i]['version'])): ?>
                                                    <?php for($v=0; $v<count($datos['formrespuestas'][$key->id][$i]['version'])-1; $v++): ?>
                                                        <span class="version-file pointer vf_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" data-idversion="<?=$datos['formrespuestas'][$key->id][$i]['version'][$v]['id']?>">versión <?=$datos['formrespuestas'][$key->id][$i]['version'][$v]['fileversion']?></span><span> - </span>
                                                    <?php endfor;?>
                                                    <span class="version-file font-bold pointer vf_<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" data-attribute="<?=$key->id?>_<?=$datos['formrespuestas'][$key->id][$i]['id']?>" data-idversion="<?=$datos['formrespuestas'][$key->id][$i]['version'][$v]['id']?>">versión <?=$datos['formrespuestas'][$key->id][$i]['version'][$v]['fileversion']?></span>
                                                <?php endif; ?>
                                            </div>
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
<?= $this->Html->script('funciones/cargadatos') ?>
<script>
    var hoy = new Date();
    var fechaFormulario = new Date(hoy);
    var permitido_size=2000000;
    var MB=1000000;
    var permitido_type=['doc','docx','jpg','jpeg','jpe','png','gif','pdf'];
    var msj_size="El archivo excede el tamaño permitido. Tamaño permitido: "+permitido_size/MB+"MB";
    var extension_cadena="";
    for(var j=0; j<permitido_type.length; j++){
        if(j==(permitido_type.length-1)) extension_cadena+=" y "+permitido_type[j]+".";
        else if(j==(permitido_type.length-2)) extension_cadena+=permitido_type[j];
        else extension_cadena+=permitido_type[j]+", ";
    }
    var msj_type="Sólo se permiten subir archivos del tipo: "+extension_cadena;
    var iddataform="<?=$id_dataform?>";

    function cssDatetimePicker(){
        var datepicker = $('body').find('.bootstrap-datetimepicker-widget:last'),
            position = datepicker.offset(),
            parent = datepicker.parent(),
            parentPos = parent.offset(),
            width = datepicker.width(),
            parentWid = parent.width();

        // move datepicker to the exact same place it was but attached to body
        datepicker.appendTo('body');
        datepicker.css({
            position: 'absolute',
            top: position.top,
            bottom: 'auto',
            left: position.left,
            right: 'auto'
        });

        // if datepicker is wider than the thing it is attached to then move it so the centers line up
        if (parentPos.left + parentWid < position.left + width) {
            var newLeft = parentPos.left;
            newLeft += parentWid / 2;
            newLeft -= width / 2;
            //datepicker.css({left: newLeft});
        }
    }

    jQuery(function(){
        $('.fsiguiente').click( function(){
            var idformdinamic="<?=$_SESSION['fd_if_formdinamic']?>";
            var url = getUrl() + "viewFormDinamics/"+"<?=$_SESSION['fd_if_formdinamic']."/".$_SESSION['fd_id_contacto']."/"?>"+iddataform+"/next";
            window.location.assign(url);
        });

        $('.fanterior').click( function(){
            var idformdinamic="<?=$_SESSION['fd_if_formdinamic']?>";
            var url = getUrl() + "viewFormDinamics/"+"<?=$_SESSION['fd_if_formdinamic']."/".$_SESSION['fd_id_contacto']."/"?>"+iddataform+"/previous";
            window.location.assign(url);
        });

        $('.date').datetimepicker({
            ignoreReadonly: true,
            format: 'DD/MM/YYYY',
            locale: 'es'
        }).on('dp.show', function (e) {
            cssDatetimePicker();
        });

        $('.datetime').datetimepicker({
            ignoreReadonly: true,
            format: 'DD/MM/YYYY HH:mm',
            locale: 'es',
            sideBySide: true
        }).on('dp.show', function() {
            if($(this).data('datedb')!=undefined){
                $(this).data('DateTimePicker').date($(this).data('datedb'));
            }
            cssDatetimePicker();
        });

        $('.time').datetimepicker({
            ignoreReadonly: true,
            format: 'LT',
            locale: 'es'
        }).on('dp.show', function (e) {
            cssDatetimePicker();
        });

        $('.btn-save').click( function(){
            var formdinamic_id = "<?=$_SESSION['fd_if_formdinamic']?>";
            var dataform_id = "<?= $id_dataform ?>";
            var url = '<?= \Cake\Routing\Router::url(['controller' => 'contactos', 'action' => 'saveallquestions'], true) ?>';
            var redirect_url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'index'], true)  . '/' ?>';

            $.ajax({
                url: url,
                type: 'post',
                data: {formdinamic_id:formdinamic_id, dataform_id:dataform_id},
                dataType: 'json',
                cache:false,
                async:false,
                success:function (resp) {
                    if(resp.requeridas) {
                        window.location.assign(redirect_url);
                    } else {
                        $("#alert-danger .message").text('Se deben completar todas las preguntas requeridas.');
                        $("#alert-danger").slideDown();
                        setTimeout(function () {
                            $("#alert-danger").slideUp();
                        }, 4000);
                    }
                }
            });
        });

        $("#alert").slideDown();

        setTimeout(function () {
            $("#alert").slideUp();
        }, 4000);

    });
</script>
<?= $this->Html->script('funciones/funciones_cargadatos') ?>

