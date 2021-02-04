<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entidad[]|\Cake\Collection\CollectionInterface $entidads
 */

echo $this->Html->script(['lib/bootbox.min.js']);
$real_url = \Cake\Routing\Router::url(['action' => 'index'], true) .'/';
?>
<script>
    function getUrl(){
        return "<?=$real_url?>";
    }
</script>
<div class="work-space entidades-block">
    <div class="row">
        <?php
        if(isset($_SESSION['EntidadDele'])){
            if($_SESSION['EntidadDele']==1){
                unset($_SESSION['EntidadDele']);?>
                <div class="alert alert-success" id="alert" style="display: none;">
                    <span class="icon icon-cross-circled"></span>
                    <span class="message">Entidad Eliminada</span>
                    <button type="button" class="close" data-dismiss="alert"></button>
                </div>
            <?php           }
        }
        ?>
    </div>
    <div class="row">
        <!--  Elemento para mostrar el formulario de busqueda  -->
        <?= $this->element('entidadContactosSearch', [
            'controller' => 'Contactos', // Nombre del controlador
            'formId' => 'entidad-search', // Id del formulario
            'placeholder' => 'Id, Nombre, Doc identidad', // Placeholder del campo de texto
            // Nombres de campos segun base de datos (Busqueda en campo de texto)
            'campos' => 'id,nombres,docid',
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
                <th class="th-id" scope="col"><?= $this->Paginator->sort('Entidads.id', 'Id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Entidads.nombre', 'Nombre') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Entidads.docid', 'Doc Identidad') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Centidadtipo.nombre', 'Tipo') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Centidadrol.rol', 'Rol') ?></th>
                <th scope="col" class="priority-3 th-estado"><?= $this->Paginator->sort('Cestados.nombre','Estado') ?></th>
                <th scope="col" class="actions th-actions"><?= __('') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(count($entidads) > 0) { ?>
                <?php foreach ($entidads as $entidad): ?>
                    <tr data-id="<?= $entidad->id ?>">
                        <td class="text-center"><?= $this->Number->format($entidad->id) ?></td>
                        <td><?= h($entidad->nombre) ?></td>
                        <td><?= h($entidad->docid) ?></td>
                        <td><?= h($entidad->centidadtipo->nombre) ?></td>
                        <td><?= h($entidad->centidadrol->nombre) ?></td>
                        <td class="text-center"><?= h($entidad->cestado->nombre)  ?></td>
                        <td class="acciones-tabla text-center">
                            <span class='lista-acciones'>
                                <div class='div-acciones'>
                                    <span>Acciones</span>
                                    <ul class='lista-herramientas'>
                                        <?php foreach ($herramientas as $key => $herramienta) { ?>
                                            <li data-id="<?= $entidad->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta"><?= $herramienta ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php } else { ?>
                <tr class="no-data-ec" data-id="0">
                    <td class="text-center" colspan="7">No hay entidades</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="paginator">
            <!-- Elemento para mostrar paginado  -->
            <?= $this->element('paginator') ?>
        </div>
    </div>
</div>

