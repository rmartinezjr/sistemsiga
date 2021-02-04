<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contacto $contacto
 * @var \App\Model\Entity\Entidad[]|\Cake\Collection\CollectionInterface $entidads
 */

$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
$url_contactos = \Cake\Routing\Router::url(['controller' => 'contactos', 'action' => 'index'], true) .'/';
$url_entidadcontactos = \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'index'], true) .'/';

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
            <div class="alert alert-danger" id="alert-vinculo" style="display: none;">
                <span class="icon icon-cross-circled"></span>
                <span class="message" id="span_message_enlace"></span>
                <button type="button" class="close" data-dismiss="alert"></button>
            </div>
            <?php
            if(isset($_SESSION['contacto-save'])){
                if($_SESSION['contacto-save']==1){
                    unset($_SESSION['contacto-save']);?>
                    <div class="col-md-12">
                        <div class="alert alert-success" id="alert" style="display: none;">
                            <span class="icon icon-cross-circled"></span>
                            <span class="message">Contacto Almacenado</span>
                            <button type="button" class="close" data-dismiss="alert"></button>
                        </div>
                    </div>
                <?php           }elseif($_SESSION['contacto-save']==2) {
                    unset($_SESSION['contacto-save']);?>
                    <div class="alert alert-success" id="alert" style="display: none">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Cambio de etapa realizado exitosamente</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h2 class="tittle"><?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
            </div>
            <div class="col-md-8 panel-action">
                <?php foreach ($controltools as $btn) {
                    if ($btn['funcion'] === "imprimir") { ?>
                        <a class="btn btn-sistem <?= $btn['class'] ?>"
                           onClick="window.open('<?= $real_url . $btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i
                                    class="fa <?= $btn['icon'] ?> icono"></i></a>
                    <?php } else if ($btn['funcion'] === "edit") { ?>
                        <a class="btn btn-sistem <?= $btn['class'] ?>"
                           href="<?= $real_url . $btn['funcion'].'/'.$contacto->id?>"><span><?= $btn['alias'] ?></span><i
                                    class="fa <?= $btn['icon'] ?> icono"></i></a>
                    <?php } else {
                        ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" href="<?=\Cake\Routing\Router::url(['controller' => $btn['modelo'], 'action' => 'index'], true) .'/'.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php }
                }
                ?>
            </div>
        </div>
        <div class="row">
            <?= $this->element('created_modified_record', ['modelo' => $contacto]) ?>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12 content-data">
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-start">Estado</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-start"><div class="col-md-2 col-xs-2 text-center state" style="background-color:<?=$contacto->cestado->colorbkg?>; color:<?=$contacto->cestado->colortext?>;"><?= h($contacto->cestado->nombre) ?></div></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Id</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?=$contacto->id?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Nombres</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?=$contacto->nombres ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Apellidos</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?= $contacto->apellidos; ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Nacionalidad</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?= $contacto->nacional; ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Tipo de Contacto</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= $contacto->ccontactotipo->nombre ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Tipo de Documento</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= $contacto->cdocidtipo->nombre ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Documento Identidad</div>
                    <div class="col-md-9 col-xs-8 col-dato"><span id="spDoc">
                            <?= ($contacto->cdocidtipo->nombre=='DUI')?substr("$contacto->docid", 0,-1).'-'.substr("$contacto->docid", -1):$contacto->docid; ?>
                            </span></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">País</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= $contacto->cpaise->nombre ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Descripción</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?= $contacto->descripcion; ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-end">Información Complementaria</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-end">
                        <?php foreach($formasig as $form):?>
                            <?php if(strcmp($arraytipo[$form->id],'styleF1')==0): ?>
                            <div class="col-md-12 p-0">
                                <div class="col-md-2 border-view-carga">
                                    <div class="col-md-12 state-struct text-center" style="margin:0;background-color:<?=$carga[$form->id][$id_dataform[$form->id]]->cestado->colorbkg?>; color:<?=$carga[$form->id][$id_dataform[$form->id]]->cestado->colortext?>;"><?= $carga[$form->id][$id_dataform[$form->id]]->cestado->nombre ?></div>
                                </div>
                                <div class="col-md-10 border-view-carga">
                                    <div class="col-md-12">
                                <?php if(strcmp($functions[$form->id][$id_dataform[$form->id]],'#')==0):?>
                                    <a href="<?=$functions[$form->id][$id_dataform[$form->id]]?>" data-content="<?=$functions[$form->id][$id_dataform[$form->id]]?>" class="styleF1"><?= $form->formdinamic->alias ?></a>
                                <?php elseif(strcmp($functions[$form->id][$id_dataform[$form->id]],'form_archivado')==0): ?>
                                    <a href="<?=$real_url.'show/'.$form->formdinamic->id."/".$contacto->id."/".$arraymulti[$form->id]->id?>" class="<?=$arraytipo[$form->id]?>"><?= $form->formdinamic->alias ?></a>
                                <?php else: ?>
                                    <a href="<?=$real_url.$functions[$form->id][$id_dataform[$form->id]].$form->formdinamic->id."/".$contacto->id."/".$arraymulti[$form->id]->id?>" class="<?=$arraytipo[$form->id]?>"><?= $form->formdinamic->alias ?></a>
                                <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php elseif(strcmp($arraytipo[$form->id],'styleT1')==0): ?>
                                <p class="href-form-asig <?=$arraytipo[$form->id]?>"><?= $form->formdinamic->alias ?></p>
                                    <?php foreach($arraymulti[$form->id] as $multi): ?>
                                    <div class="col-md-12 p-0">
                                        <div class="col-md-2 border-view-carga">
                                            <div class="col-md-12 state-struct text-center" style="margin:0;background-color:<?=$carga[$form->id][$multi->id]->cestado->colorbkg?>; color:<?=$carga[$form->id][$multi->id]->cestado->colortext?>;"><?= $carga[$form->id][$multi->id]->cestado->nombre ?></div>
                                        </div>
                                        <div class="col-md-10 border-view-carga">
                                            <div class="col-md-12">
                                        <?php if(strcmp($functions[$form->id][$multi->id],'#')==0):?>
                                            <a href="<?=$functions[$form->id][$multi->id]?>" data-content="<?=$functions[$form->id][$id_dataform[$form->id]]?>" class="styleF1"><?=$cont_carga.". ".$multi->codigo?></a>
                                        <?php elseif(strcmp($functions[$form->id][$multi->id],'form_archivado')==0): ?>
                                            <a href="<?=$real_url.'show/'.$form->formdinamic->id."/".$contacto->id."/".$multi->id?>" class="styleF1"><?=$cont_carga.". ".$multi->codigo?></a>
                                        <?php else: ?>
                                            <a href="<?=$real_url.$functions[$form->id][$multi->id].$form->formdinamic->id."/".$contacto->id."/".$multi->id?>" class="styleF1"><?=$cont_carga.". ".$multi->codigo?></a>
                                        <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $cont_carga++; endforeach;?>
                                <div class="col-md-12 border-view-carga">
                                    <a <?php if($view_inicial[$form->id]): if($permitir_carga): if(strcmp($functions[$form->id][0],'form_archivado')==0):?> href="#" data-content="<?=$functions[$form->id][0]?>" <?php else: ?> href="<?=$real_url.$function.$form->formdinamic->id."/".$contacto->id."/0"?>" <?php endif; else:?> href="#" data-content="carga_no_finalizada" <?php endif; else:?> href="#" data-content="<?=$functions[$form->id][0]?>" <?php endif;?> class="href-form-asig styleF0">[ + <?= $form->formdinamic->alias ?> ]</a>
                                </div>
                            <?php else: ?>
                                <div class="col-md-12 border-view-carga p-l-0">
                                    <a <?php if($view_inicial[$form->id]): if(strcmp($functions[$form->id][0],'form_archivado')==0):?> href="#" <?php else: ?> href="<?=$real_url.$function.$form->formdinamic->id."/".$contacto->id."/".$id_dataform[$form->id]?>" <?php endif; else:?> href="#" <?php endif;?> class="href-form-asig <?=$arraytipo[$form->id]?>" data-content="<?=$functions[$form->id][0]?>">[ + <?= $form->formdinamic->alias ?> ]</a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12 related registros-relacionados">
                <?php if (!empty($contacto->entidadcontactos)): ?>
                    <h4><?= __('Entidades Relacionadas') ?></h4>
                    <div class="col-md-12 col-xs-12">
                        <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                            <thead>
                            <tr>
                                <th scope="col" class="col-inicio"><?= __('Id') ?></th>
                                <th scope="col"><?= __('Nombre') ?></th>
                                <th scope="col"><?= __('Documento Identidad') ?></th>
                                <th scope="col"><?= __('Tipo de Entidad') ?></th>
                                <th scope="col" class="col-final"><?= __('Estado') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($entidads as $entidad): ?>
                                <tr>
                                    <td class="text-center"><?= h($entidad->id) ?></td>
                                    <td><?= h($entidad->nombre) ?></td>
                                    <td class="text-center"><?= h($entidad->docid) ?></td>
                                    <td class="text-center"><?= h($entidad->centidadtipo->nombre) ?></td>
                                    <td class="text-center"><?= h($entidad->cestado->nombre) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function(){
        $("#alert").slideDown();

        setTimeout(function () {
            $("#alert").slideUp();
        }, 4000);


    });
</script>
<?= $this->Html->script('funciones/msj_cargadatos') ?>