<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contacto[]|\Cake\Collection\CollectionInterface $contactos
 */

echo $this->Html->script(['lib/bootbox.min.js']);
$real_url = \Cake\Routing\Router::url(['action' => 'index'], true) .'/';
?>
<script>
    function getUrl(){
        return "<?=$real_url?>";
    }
</script>
<div class="work-space contactos-block">
    <div class="row">
        <?php
        if(isset($_SESSION['ContactoDele'])){
            if($_SESSION['ContactoDele']==1){
                unset($_SESSION['ContactoDele']);?>
                <div class="alert alert-success alerta-entidad-contacto" id="alert">
                    <span class="icon icon-cross-circled"></span>
                    <span class="message">Contacto Eliminado</span>
                    <button type="button" class="close" data-dismiss="alert"></button>
                </div>
            <?php           }
        }elseif(isset($_SESSION['ExiteEntidades'])){
            if($_SESSION['ExiteEntidades']==1){
                unset($_SESSION['ExiteEntidades']);?>
                <div class="alert alert-danger alerta-entidad-contacto" id="alert">
                    <span class="icon icon-cross-circled"></span>
                    <span class="message">El contacto no puede eliminarse porque tiene más de 1 entidad relacionada.</span>
                    <button type="button" class="close" data-dismiss="alert"></button>
                </div>
            <?php           }
        }elseif(isset($_SESSION['ExiteUsuario'])){
            if($_SESSION['ExiteUsuario']==1){
                unset($_SESSION['ExiteUsuario']);?>
                <div class="alert alert-danger alerta-entidad-contacto" id="alert">
                    <span class="icon icon-cross-circled"></span>
                    <span class="message">El contacto no puede eliminarse porque tiene un usuario relacionado.</span>
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
            'controller' => 'Contactos', // Nombre del controlador
            'formId' => 'contacto-search', // Id del formulario
            'placeholder' => 'Id, Nombre, Apellidos, Doc identidad', // Placeholder del campo de texto
            // Nombres de campos segun base de datos (Busqueda en campo de texto)
            'campos' => 'id,nombres,apellidos,docid',
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

        <table cellpadding="0" cellspacing="0" id="table-contactos" class="table table-contactos table-condensed table-striped">
            <thead>
            <tr>
                <th id="contacto-id" data-campo="Contactos.id" class="th-id order-campo" scope="col">Id</th>
                <th id="contacto-cestado"data-campo="Cestados.nombre" scope="col" class="priority-3 th-estado order-campo">Estado</th>
                <th id="contacto-nombres" data-campo="Contactos.nombres" class="order-campo" scope="col">Nombre</th>
                <th id="contacto-docid" data-campo="Contactos.docid" class="order-campo" scope="col">Doc Identidad</th>
                <th id="contacto-ccontactotipo" data-campo="Ccontactotipos.nombre" class="order-campo priority-3 priority-6" scope="col">Tipo</th>
                <th id="contacto-nacional" data-campo="Contactos.nacional" class="order-campo priority-3 priority-6" scope="col">Origen</th>
                <th scope="col" class="actions th-actions"><?= __('') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contactos as $contacto): ?>
                <tr data-id="<?= $contacto->id ?>">
                    <td class="text-center"><?= $this->Number->format($contacto->id) ?></td>
                    <td class="text-center priority-3"><p class="block-status" style="background-color:<?=$contacto->cestado->colorbkg?>; color:<?=$contacto->cestado->colortext?>; width: 81px;"><?= h($contacto->cestado->nombre)  ?></p></td>
                    <td><?= h($contacto->nombres . ' ' . $contacto->apellidos) ?></td>
                    <td>
                        <?= ($contacto->cdocidtipo->nombre=='DUI')?substr("$contacto->docid", 0,-1).'-'.substr("$contacto->docid", -1):h((strlen($contacto->docid) > 16) ? substr($contacto->docid,0,16) . "..." : $contacto->docid); ?>
                    </td>
                    <td class="priority-3 priority-6"><?= h($contacto->ccontactotipo->nombre) ?></td>
                    <td class="priority-3 priority-6"><?= h($contacto->nacional) ?></td>
                    <td class="acciones-tabla text-center">
                            <span class='lista-acciones'>
                                <div class='div-acciones'>
                                    <div class="span-acciones">
                                        <span>Acciones</span>
                                    </div>
                                    <ul class='lista-herramientas'>
                                        <?php foreach ($herramientas as $key => $herramienta) { ?>
                                            <li data-id="<?= $contacto->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta contacto-herramienta"><?= $herramienta ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginator" data-paginator="Contactos">
            <!-- Elemento para mostrar paginado  -->
            <?= $this->element('paginator') ?>
        </div>
    </div>
</div>
<script>
    jQuery(function(){
        $('.contacto-herramienta').click( function(){
            var id = $(this).attr('data-id');
            var funcion = $(this).attr('data-funcion');
            var url = getUrl() + funcion + '/' + id;

            if(funcion === 'delete') {
                bootbox.confirm({
                    message: "¿Está seguro de eliminar el contacto?",
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

