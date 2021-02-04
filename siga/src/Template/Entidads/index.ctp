<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entidad[]|\Cake\Collection\CollectionInterface $entidads
 */

echo $this->Html->script(['lib/bootbox.min.js']);
$real_url_entidad = \Cake\Routing\Router::url(['controller' => 'entidads', 'action' => 'index'], true) .'/';
// debug($real_url);
?>
<script>
    function getUrlEntidad(){
        return "<?=$real_url_entidad?>";
    }
</script>
<div class="work-space entidades-block">
    <div class="row">
        <?php
        if(isset($_SESSION['EntidadDele'])){
            if($_SESSION['EntidadDele']==1){
                unset($_SESSION['EntidadDele']);?>
                <div class="alert alert-success alerta-entidad-contacto" id="alert" >
                    <span class="icon icon-cross-circled"></span>
                    <span class="message">Entidad Eliminada</span>
                    <button type="button" class="close" data-dismiss="alert"></button>
                </div>
            <?php           }
        } elseif(isset($_SESSION['ExiteContactos'])){
            if($_SESSION['ExiteContactos']==1){
                unset($_SESSION['ExiteContactos']);?>
                <div class="alert alert-danger alerta-entidad-contacto" id="alert" >
                    <span class="icon icon-cross-circled"></span>
                    <span class="message">La entidad no puede eliminarse porque tiene contactos relacionados.</span>
                    <button type="button" class="close" data-dismiss="alert"></button>
                </div>
            <?php           }
        }

        if(isset($_SESSION['formcompleted'])) {
            if($_SESSION['formcompleted']==1) {
                unset($_SESSION['formcompleted']);?>
                <div class="alert alert-success" id="alert" style="display: none;">
                    <span class="icon icon-cross-circled"></span>
                    <span class="message">Información alamacenada correctamente.</span>
                    <button type="button" class="close" data-dismiss="alert"></button>
                </div>
            <?php }
        }
        ?>
    </div>
    <div class="row">
        <!--  Elemento para mostrar el formulario de busqueda  -->
        <?= $this->element('entidadContactosSearch', [
            'controller' => 'Entidads', // Nombre del controlador
            'formId' => 'entidad-search', // Id del formulario
            'placeholder' => 'Id, Nombre, Doc identidad', // Placeholder del campo de texto
            // Nombres de campos segun base de datos (Busqueda en campo de texto)
            'campos' => 'id,nombre,docid',
            'trash' => 0,
            // Combobox a mostrar en la busqueda con sus opciones
            'select' => [
                'Cestados' => [
                    'opciones' => $cestados,
                    'campo' => 'cestado_id',
                    'label' => 'Estados',
                    'empty' => 'Estado'
                ]
            ]
        ]) ?>
    </div>
    <div class="entidadcontactos index large-9 medium-8 columns content">
        <p class="info-paginacion"><?= $this->Paginator->counter(['format' => __('Página {{page}} de {{pages}}, {{current}} registros de un total de {{count}}, comienza en 1, finalizando en {{current}}')]) ?></p>

        <table cellpadding="0" cellspacing="0" id="table-entidads" class="table table-entidads table-condensed table-striped">
            <thead>
            <tr>
                <th id="entidad-id" data-campo="Entidads.id" data-tipoorden="asc" class="th-id order-campo" scope="col">Id</th>
                <th id="entidad-cestado" data-campo="Cestados.nombre" data-tipoorden="asc" scope="col" class="priority-3 th-estado order-campo">Estado</th>
                <th id="entidad-nombre" data-campo="Entidads.nombre" data-tipoorden="asc" class="order-campo" scope="col">Nombre</th>
                <th id="entidad-docid" data-campo="Entidads.docid" data-tipoorden="asc" class="order-campo priority-6 col-docid" scope="col">Doc Identidad</th>
                <th id="entidad-centidadtipo" data-campo="Centidadtipos.nombre" data-tipoorden="asc" class="order-campo priority-3 priority-6" scope="col">Tipo</th>
                <th id="entidad-centidadrol" data-campo="Centidadrols.nombre" data-tipoorden="asc" class="order-campo priority-3 priority-6" scope="col">Rol</th>
                <th scope="col" class="actions th-actions"><?= __('') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($entidads as $entidad): ?>

                <tr data-id="<?= $entidad->id ?>">
                    <td class="text-center"><?= $this->Number->format($entidad->id) ?></td>
                    <td class="text-center priority-3"><p class="block-status" style="background-color:<?=$entidad->cestado->colorbkg?>; color:<?=$entidad->cestado->colortext?>; width: 81px;"><?= h($entidad->cestado->nombre)  ?></p></td>
                    <td><?= h($entidad->nombre) ?></td>
                    <td class="priority-6">
                        <?php
                        if(!$entidad->docidnull) {
                            echo ($entidad->cdocidtipo->nombre=='NIT')?substr("$entidad->docid", 0,4).'-'.substr("$entidad->docid", 4,6).'-'.substr("$entidad->docid", 10,3).'-'.substr("$entidad->docid", -1):h((strlen($entidad->docid) > 17) ? substr($entidad->docid,0,17) . "..." :$entidad->docid);
                        } else {
                            echo '';
                        }
                        ?>
                    </td>
                    <td class="priority-3 priority-6"><?= h($entidad->centidadtipo->nombre) ?></td>
                    <td class="priority-3 priority-6">
                        <?= h((strlen($entidad->centidadrol->nombre) > 17) ? substr($entidad->centidadrol->nombre,0,17) . "..." :$entidad->centidadrol->nombre); ?></td>
                    <td class="acciones-tabla text-center">
                        <span class='lista-acciones'>
                            <div class='div-acciones'>
                                <div class="span-acciones">
                                    <span>Acciones</span>
                                </div>
                                <ul class='lista-herramientas'>
                                    <?php foreach ($herramientas as $key => $herramienta) { ?>
                                        <li data-id="<?= $entidad->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta entidad-herramienta"><?= $herramienta ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginator" data-paginator="Entidads">
            <!-- Elemento para mostrar paginado  -->
            <?= $this->element('paginator') ?>
        </div>
    </div>
</div>
<script>
    jQuery(function(){
        $('.entidad-herramienta').click( function(){
            var id = $(this).attr('data-id');
            var funcion = $(this).attr('data-funcion');
            var url = getUrlEntidad() + funcion + '/' + id;

            if(funcion === 'delete') {
                bootbox.confirm({
                    message: "¿Está seguro de eliminar la entidad?",
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
                            window.location.assign(url);
                        }
                    }
                });
            } else {
                window.location.assign(url);
            }
        });

        $("#alert").slideDown();

        setTimeout(function () {
            $("#alert").slideUp();
        }, 4000);
    });
</script>

