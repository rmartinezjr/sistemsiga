<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entidad $entidad
 * @var \App\Model\Entity\Contacto[]|\Cake\Collection\CollectionInterface $contactos
 * @var \App\Model\Entity\Entidadred[]|\Cake\Collection\CollectionInterface $entidades_madre
 */

$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
$url_entidades = \Cake\Routing\Router::url(['controller' => 'entidads', 'action' => 'index'], true) .'/';
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
            if(isset($_SESSION['entidad-save'])){
                if($_SESSION['entidad-save']==1){
                    unset($_SESSION['entidad-save']);?>
                    <div class="col-md-12">
                        <div class="alert alert-success" id="alert" style="display: none;">
                            <span class="icon icon-cross-circled"></span>
                            <span class="message">Entidad Almacenada</span>
                            <button type="button" class="close" data-dismiss="alert"></button>
                        </div>
                    </div>
                <?php           }elseif($_SESSION['entidad-save']==2) {
                    unset($_SESSION['entidad-save']);?>
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
                           href="<?= $real_url . $btn['funcion'].'/'.$entidad->id?>"><span><?= $btn['alias'] ?></span><i
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
            <?= $this->element('created_modified_record', ['modelo' => $entidad]) ?>
        </div>

        <div class="row">
            <div class="col-md-12 col-xs-12 content-data">
                <?php if(count($entidades_madre) > 0) { ?>
                    <div class="col-md-12 col-xs-12 fila-data">
                        <div class="col-md-3 col-xs-4 col-label lbl-border-start" style="background-color: #ebebeb">Miembro de</div>
                        <div class="col-md-9 col-xs-8 col-dato dato-border-start">
                            <?php foreach ($entidades_madre as $entidad_madre) { ?>
                                <?php if(isset($entidads[$entidad_madre->entidadmadre])) { ?>
                                    <div class="col-xs-12" style="padding: 5px 15px; border: solid 1px #E7fcb7">
                                        <?= $this->Html->link($entidads[$entidad_madre->entidadmadre], ['controller' => 'Entidads', 'action' => 'view', $entidad_madre->entidadmadre]) ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php $clase_estado_lbl = (count($entidades_madre) == 0) ? 'lbl-border-start' : '' ?>
                <?php $clase_estado_dato = (count($entidades_madre) == 0) ? 'dato-border-start' : '' ?>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label <?= $clase_estado_lbl ?>">Estado</div>
                    <div class="col-md-9 col-xs-8 col-dato" <?= $clase_estado_dato ?>><div class="col-md-2 col-xs-2 text-center state" style="background-color:<?=$entidad->cestado->colorbkg?>; color:<?=$entidad->cestado->colortext?>;"><?= h($entidad->cestado->nombre) ?></div></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Id</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?=$entidad->id?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Nombre Corto</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= $entidad->nombre ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Nombre Largo</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= $entidad->nombrelargo ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Código</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= $entidad->codigo ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Origen</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= ($entidad->nacional == 1) ? 'Entidad Salvadoreño' : 'Entidad Extranjero' ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Tipo de Documento</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= $entidad->cdocidtipo->nombre ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Documento de Identidad</div>
                    <div class="col-md-9 col-xs-8 col-dato">
                        <?php
                        if(!$entidad->docidnull) {
                            echo ($entidad->cdocidtipo->nombre=='NIT')?substr("$entidad->docid", 0,4).'-'.substr("$entidad->docid", 4,6).'-'.substr("$entidad->docid", 10,3).'-'.substr("$entidad->docid", -1):$entidad->docid;
                        } else {
                            echo 'No disponible';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Tipo de Entidad</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?= $entidad->centidadtipo->nombre; ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Rol de Entidad</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?= $entidad->centidadrol->nombre; ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Descripción</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?= $entidad->descripcion; ?></div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12 related registros-relacionados">
                <?php if (!empty($entidades_hijas)): ?>
                    <h4><?= __('Entidades Miembros') ?></h4>
                    <div class="col-md-12 col-xs-12">
                        <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                            <thead>
                            <tr>
                                <th width="75px" scope="col" class="col-inicio"><?= __('Id') ?></th>
                                <th scope="col" class="th-estado"><?= __('Estado') ?></th>
                                <th scope="col"><?= __('Nombre') ?></th>
                                <th scope="col" class="col-final"><?= __('Tipo de Entidad') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($entidades_hijas as $entidad_hija): ?>
                                <tr>
                                    <td class="text-center"><?= h($entidad_hija->id) ?></td>
                                    <td class="text-center"><p class="block-status" style="background-color:<?=$entidad_hija->cestado->colorbkg?>; color:<?=$entidad_hija->cestado->colortext?>;"><?= h($entidad_hija->cestado->nombre) ?></p></td>
                                    <td class="text-left" style="padding-left: 15px">
                                        <?= $this->Html->link($entidad_hija->nombre, ['controller' => 'Entidads', 'action' => 'view', $entidad_hija->id]) ?>
                                    </td>
                                    <td class="text-center"><?= h($entidad_hija->centidadtipo->nombre) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12 related registros-relacionados">
                <?php if (!empty($entidad->entidadcontactos)): ?>
                    <h4><?= __('Contactos Relacionados') ?></h4>
                    <div class="col-md-12 col-xs-12">
                        <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                            <thead>
                            <tr>
                                <th scope="col" class="col-inicio"><?= __('Id') ?></th>
                                <th scope="col" class="th-estado"><?= __('Estado') ?></th>
                                <th scope="col"><?= __('Nombre') ?></th>
                                <th scope="col"><?= __('Documento Identidad') ?></th>
                                <th scope="col" class="col-final"><?= __('Tipo de Contacto') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($contactos as $contacto): ?>
                                <tr>
                                    <td class="text-center"><?= h($contacto->id) ?></td>
                                    <td class="text-center"><p class="block-status" style="background-color:<?=$contacto->cestado->colorbkg?>; color:<?=$contacto->cestado->colortext?>;"><?= h($contacto->cestado->nombre) ?></p></td>
                                    <td style="padding-left: 15px;"><?= h($contacto->nombres . ' ' .$contacto->apellidos) ?></td>
                                    <td class="text-center"><?= h($contacto->docid) ?></td>
                                    <td class="text-center"><?= h($contacto->ccontactotipo->nombre) ?></td>
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