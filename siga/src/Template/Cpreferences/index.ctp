<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cpreference[]|\Cake\Collection\CollectionInterface $cpreferences
 */

echo $this->Html->script(['lib/bootbox.min.js']);
$real_url = \Cake\Routing\Router::url(['action' => 'index'], true) .'/';
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
        ])
        ?>
    </div>
    <div class="cont-border">
        <div class="row">
            <?php
            if(isset($_SESSION['CpreferenceDele'])){
                if($_SESSION['CpreferenceDele']==1){
                    unset($_SESSION['CpreferenceDele']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Preferencia o Configuración Eliminada</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }

            if(isset($_SESSION['cpreference-edit'])){
                if($_SESSION['cpreference-edit']==1){
                    unset($_SESSION['cpreference-edit']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Los cambios realizados se han almacenado.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h2 class="tittle"><?= $titulo[0]['alias'] ?></h2>
            </div>
            <div class="col-md-8 panel-action">
                <!-- Elemento para mostrar botones  -->
                <?= $this->element('controltools', ['controltools' => $controltools]) ?>
            </div>
        </div><br>
        <div class="row">
            <!--  Elemento para mostrar el formulario de busqueda  -->
            <?php

            $perfiles = [1=>"Admin",2=>"Normal"];
            echo    $this->element('search', [
                'controller' => 'Cpreferences', // Nombre del controlador
                'placeholder' => 'Id, Nombre', // Placeholder del campo de texto
                // Nombres de campos segun base de datos (Busqueda en campo de texto)
                'campos' => 'id,nombre',
                // Rango de Fechas
                'fecha' => [
                    'rangoFecha' => false, // True: si se muestra rango de fechas. False: No se muestra rango de fechas
                    //'campo' => 'created', // Nombre de campo de busqueda para rango de fecha
                    //'datetime' => '1', // '1': Si la busqueda es fecha y hora. '0': Si la busqueda es solo por fecha.
                ],
                'inactivo' => 0,
                // Combobox a mostrar en la busqueda con sus opciones
                // Si no se muestran combobox colocar array vacio.
                'select' => []
            ]) ?>
        </div>
        <div class="cpreferences index large-9 medium-8 columns content">
            <p class="info-paginacion"><?= $this->Paginator->counter(['format' => __('Página {{page}} de {{pages}}, {{current}} registros de un total de {{count}}, comienza en 1, finalizando en {{current}}')]) ?></p>

            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th class="th-id" scope="col"><?= $this->Paginator->sort('Cpreferences.id', 'Id') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('Cpreferences.nombre','Nombre') ?></th>
                    <th scope="col" class="actions th-actions"><?= __('') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cpreferences as $cpreference): ?>
                    <tr>
                        <td class="text-center"><?= $this->Number->format($cpreference->id) ?></td>
                        <td><?= h($cpreference->nombre) ?></td>
                        <td class="acciones-tabla text-center">
                            <span class='lista-acciones'>
                                <div class='div-acciones'>
                                    <span>Acciones</span>
                                    <ul class='lista-herramientas'>
                                        <?php foreach ($herramientas as $key => $herramienta) { ?>
                                            <li data-id="<?= $cpreference->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta"><?= $herramienta ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="paginator">
                <!-- Elemento para mostrar paginado  -->
                <?= $this->element('paginator') ?>
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

        $('.accion-herramienta').click( function(){
            var id = $(this).attr('data-id');
            var funcion = $(this).attr('data-funcion');
            var url = getUrl() + funcion + '/' + id;

            if(funcion === 'delete') {
                bootbox.confirm({
                    message: "¿Está seguro de eliminar el registro?",
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
    });
</script>