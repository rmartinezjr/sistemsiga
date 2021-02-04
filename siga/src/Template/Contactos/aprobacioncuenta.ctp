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
        <p class="text-left lbl-navegacion">Registro > Datos iniciales > Aprobación de cuenta </p>
    </div>
    <div class="cont-border">
        <div class="row">
            <?php if(isset($_SESSION['fallo-aprobacion'])){
                $mensaje = $_SESSION['fallo-aprobacion'];
                unset($_SESSION['fallo-aprobacion']);?>
                <div class="col-md-12">
                    <div class="alert alert-danger alerta-aprobacion" id="exito-aprobacion">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message"><?= $mensaje ?></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($_SESSION['exito-aprobacion'])){
                $mensaje = $_SESSION['exito-aprobacion'];
                unset($_SESSION['exito-aprobacion']);?>
                <div class="col-md-12">
                    <div class="alert alert-success alerta-aprobacion" id="fallo-aprobacion">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message"><?= $mensaje ?></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                </div>
            <?php } ?>
            <?php
            if(isset($_SESSION['aprobacion-solicitud'])){
                if($_SESSION['aprobacion-solicitud']==1){
                    unset($_SESSION['aprobacion-solicitud']);?>
                    <div class="col-md-12">
                        <div class="alert alert-success alerta-aprobacion" id="alert" style="display: none;">
                            <span class="icon icon-cross-circled"></span>
                            <span class="message">La solicitd de registro de la cuenta ha sido aprobada</span>
                            <button type="button" class="close" data-dismiss="alert"></button>
                        </div>
                    </div>
                <?php           }
            }
            ?>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h2 class="tittle">Aprobación de cuenta</h2>
            </div>
            <div class="col-md-8 panel-action">
                <?php if($estadoEsperaAprobacion == $entidadcontacto['Contactos']['cestado_id']) { ?>
                    <a href="<?= \Cake\Routing\Router::url(['controller' => 'cuentaregistro','action' => 'aprobarsolicitud', $entidadcontacto['Entidads']['id'], $entidadcontacto['Contactos']['id']], true) ?>" class="btn btn-sistem btn-save"><span>Aprobar</span><i class="fa fa-check icono"></i></a>
                    <a href="<?= \Cake\Routing\Router::url(['controller' => 'cuentaregistro','action' => 'rechazarsolicitud', $entidadcontacto['Entidads']['id'], $entidadcontacto['Contactos']['id']], true) ?>" class="btn btn-sistem btn-danger"><span>Rechazar</span><i class="fa fa-remove icono"></i></a>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 info-log">
                <span class="lbl">Fecha de Solicitud de Registro:</span><span class="lbl-data"><?= date("d-m-Y H:i:s", strtotime($entidadcontacto['Contactos']['created'])) ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-xs-12 related registros-relacionados">
                <h4><?= __('Información del Contacto') ?></h4>
            </div>

            <div class="col-md-12 col-xs-12 content-data">
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-start">Estado</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-start">
                        <div class="col-md-2 col-xs-2 text-center state" style="background-color:<?=$entidadcontacto['Contactos']['cestado']['colorbkg']?>; color:<?=$entidadcontacto['Contactos']['cestado']['colortext']?>;"><?= $entidadcontacto['Contactos']['cestado']['nombre'] ?></div>
                    </div>
                </div>
                <?php if ($entidadcontacto['Contactos']['cestado']['id'] == $estadoEsperaAprobacion && 1 == 2) { ?>
                    <div class="col-md-12 col-xs-12 fila-data">
                        <div class="col-md-3 col-xs-4 col-label">Perfil del contacto</div>
                        <div class="col-md-9 col-xs-8 col-dato">
                            <div class="col-md-3" style="padding: 0">
                                <?=$this->Form->control('nacional',[
                                    'label'=>false,
                                    'div'=>['class'=>'form-group'],
                                    'options' => $perfilesentidadcontacto,
                                    'class'=>'form-control select-control',
                                    'style' => 'font-size: 12px; padding: 3px 12px; height: 28px'
                                ]);?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Nombre del contacto</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?=$entidadcontacto['Contactos']['nombre_completo'] ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Tipo de Documento</div>
                    <div class="col-md-9 col-xs-8 
                    col-dato"><?= $docidtiposcontactos[$entidadcontacto['Contactos']['cdocidtipo_id']] ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Número de Documento</div>
                    <div class="col-md-9 col-xs-8 col-dato">
                        <span id="spDoc">
                            <?php // $entidadcontacto['Contactos']['id'] = 18; debug((-1)*(strlen('-'.$entidadcontacto['Contactos']['id']) - 1));
                            if ($entidadcontacto['Contactos']['cestado']['id'] != $estadoContactorechazado) {
                                echo ($docidtiposcontactos[$entidadcontacto['Contactos']['cdocidtipo_id']] == 'DUI')
                                    ? substr("" . $entidadcontacto['Contactos']['docid'], 0,-1).'-'.substr("" . $entidadcontacto['Contactos']['docid'], -1)
                                    : "" . $entidadcontacto['Contactos']['docid'];
                            } else {
                             /*   echo ($docidtiposcontactos[$entidadcontacto['Contactos']['cdocidtipo_id']] == 'DUI')
                                    ? substr("" . $entidadcontacto['Contactos']['docid'], 0, (-1)*(strlen('-'.$entidadcontacto['Contactos']['id']) + 1)).'-'.substr("" . $entidadcontacto['Contactos']['docid'], (-1)*(strlen('-'.$entidadcontacto['Contactos']['id']) - 1), (-1)*(strlen('-'.$entidadcontacto['Contactos']['id']) - 2))
                                    : "" . $entidadcontacto['Contactos']['docid'];*/
                            }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Tipo de Contacto</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?= $entidadcontacto['Contactos']['ccontactotipo']['nombre']; ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Correo Electrónico</div>
                    <div class="col-md-9 col-xs-8 col-dato ">
                        <?php
                        if ($entidadcontacto['Contactos']['cestado']['id'] != $estadoContactorechazado) {
                            echo $entidadcontacto['Contactos']['email'];
                        } else {
                            // echo substr("" . $entidadcontacto['Contactos']['email'], 0,-2);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-xs-12 related registros-relacionados">
                <h4><?= __('Información de la Organización') ?></h4>
            </div>

            <div class="col-md-12 col-xs-12 content-data">
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-start">Nombre de la organización</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-start"><?=$entidadcontacto['Entidads']['nombre'] ?></div>
                </div><div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-start">Nombre completo de la organización</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-start"><?=$entidadcontacto['Entidads']['nombrelargo'] ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Tipo de Documento</div>
                    <div class="col-md-9 col-xs-8 
                    col-dato"><?= $docidtiposentidades[$entidadcontacto['Entidads']['cdocidtipo_id']] ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Número de Documento</div>
                    <div class="col-md-9 col-xs-8 col-dato">
                        <?php
                            echo ($docidtiposentidades[$entidadcontacto['Entidads']['cdocidtipo_id']] == 'NIT')
                                ? substr("" . $entidadcontacto['Entidads']['docid'], 0,4).'-'.substr("" . $entidadcontacto['Entidads']['docid'], 4,6).'-'.substr("" . $entidadcontacto['Entidads']['docid'], 10,3).'-'.substr("" . $entidadcontacto['Entidads']['docid'], -1)
                                : $entidadcontacto['Entidads']['docid'];
                        ?>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Tipo de Organización</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?= $entidadcontacto['Entidads']['centidadtipo']['nombre']; ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Rol de la Organización</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= $entidadcontacto['Entidads']['centidadrol']['nombre']; ?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label ">Origen</div>
                    <div class="col-md-9 col-xs-8 col-dato "><?= ($entidadcontacto['Entidads']['nacional'] == 1) ? 'Organización Salvadoreña' : 'Organización Extranjera' ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function(){
        $(".alerta-aprobacion").slideDown();

        setTimeout(function () {
            $(".alerta-aprobacion").slideUp();
        }, 4000);
    });
</script>