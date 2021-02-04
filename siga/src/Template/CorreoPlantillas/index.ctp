<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CorreoPlantilla[]|\Cake\Collection\CollectionInterface $correoPlantillas
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
        ])?>
    </div>
    <div class="cont-border">
        <div class="row">
            <?php
            if(isset($_SESSION['correoPlantillaDele'])){
                if($_SESSION['correoPlantillaDele']==1){
                    unset($_SESSION['correoPlantillaDele']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Plantilla de Correo Eliminada</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h2 class="tittle"><?= (isset($titulo[0]['alias'])) ? $titulo[0]['alias'] : 'No tiene modelo asociado' ?></h2>
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
                'controller' => 'CorreoPlantillas', // Nombre del controlador
                'placeholder' => 'Id, Nombre', // Placeholder del campo de texto
                // Nombres de campos segun base de datos (Busqueda en campo de texto)
                'campos' => 'id,nombre',
                // Rango de Fechas
                'fecha' => [
                    'rangoFecha' => false   , // True: si se muestra rango de fechas. False: No se muestra rango de fechas
                ],
                'inactivo' => 0,
                // Combobox a mostrar en la busqueda con sus opciones
                // Si no se muestran combobox colocar array vacio.
                'select' => []
            ]) ?>
        </div>
        <div class="correoPlantillas index large-9 medium-8 columns content">
            <p class="info-paginacion"><?= $this->Paginator->counter(['format' => __('Página {{page}} de {{pages}}, {{current}} registros de un total de {{count}}, comienza en 1, finalizando en {{current}}')]) ?></p>

            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th class="text-center  th-id" scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th class="text-center" scope="col"><?= $this->Paginator->sort('nombre') ?></th>
                    <th class="text-center" scope="col" class="actions"><?= __('') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($correoPlantillas as $correoPlantilla): ?>
                    <tr>
                        <td class="text-center"><?= $this->Number->format($correoPlantilla->id) ?></td>
                        <td><?= h($correoPlantilla->nombre) ?></td>
                        <td class="acciones-tabla text-center">
                            <span class='lista-acciones'>
                                <div class='div-acciones'>
                                    <span>Acciones</span>
                                    <ul class='lista-herramientas'>
                                        <?php foreach ($herramientas as $key => $herramienta) { ?>
                                            <li data-id="<?= $correoPlantilla->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta"><?= $herramienta ?></li>
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
                <ul class="paginate">
                    <?= $this->Paginator->prev('<< ' . __('Anterior')) ?>
                    <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
                    <?= $this->Paginator->next(__('Siguiente') . ' >>') ?>
                </ul>
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