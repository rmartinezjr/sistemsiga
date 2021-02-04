<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Centidadtipo[]|\Cake\Collection\CollectionInterface $centidadtipos
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
            if(isset($_SESSION['ModeloDele'])){
                if($_SESSION['ModeloDele']==1){
                    unset($_SESSION['ModeloDele']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Modelo Eliminado</span>
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
                'controller' => 'ModeloMenu', // Nombre del controlador
                'placeholder' => 'Id, Modelo, Alias', // Placeholder del campo de texto
                // Nombres de campos segun base de datos (Busqueda en campo de texto)
                'campos' => 'id,modelo,alias',
                // Rango de Fechas
                'fecha' => [
                    'rangoFecha' => false, // True: si se muestra rango de fechas. False: No se muestra rango de fechas
                    //'campo' => 'created', // Nombre de campo de busqueda para rango de fecha
                    //'datetime' => '1', // '1': Si la busqueda es fecha y hora. '0': Si la busqueda es solo por fecha.
                ],
                'inactivo'=>0,
                // Combobox a mostrar en la busqueda con sus opciones
                // Si no se muestran combobox colocar array vacio.
                'select' => [
                    'ModeloMenu_alias_menu' => [
                        'opciones' => $menu,
                        'campo' => 'id_menu',
                        'label' => 'Menú',
                        'empty' => 'Menú'
                    ],
                    'Cestados' => [
                        'opciones' => $cestados,
                        'campo' => 'cestado_id',
                        'label' => 'Estados',
                        'empty' => 'Estado'
                    ]
                ],

                'checkbox'=>[array('campo'=>'movil','label'=>'Móvil')],
                // Ancho del contenedor que tiene los select
                //'widthSelect' => 2, // Numero de columnas para el contenedor
            ]) ?>
        </div>
        <div class="centidadtipos index large-9 medium-8 columns content">
            <p class="info-paginacion"><?= $this->Paginator->counter(['format' => __('Página {{page}} de {{pages}}, {{current}} registros de un total de {{count}}, comienza en 1, finalizando en {{current}}')]) ?></p>

            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
        <thead>
            <tr>
                <th scope="col" class="th-id" ><?= $this->Paginator->sort('ModeloMenu.id','Id') ?></th>
                <th scope="col" class="priority-3 th-estado"><?= $this->Paginator->sort('ModeloMenu.cestado_id','Estado') ?></th>
                <th scope="col"><?= $this->Paginator->sort('ModeloMenu.modelo','Modelo') ?></th>
                <th scope="col"><?= $this->Paginator->sort('ModeloMenu.alias','Alias') ?></th>
                <th scope="col"><?= $this->Paginator->sort('ModeloMenu.alias_menu','Menú') ?></th>
                <th scope="col"><?= $this->Paginator->sort('ModeloMenu.movil','Móvil') ?></th>
                <th scope="col" class="actions th-actions" ><?= __('') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modelos as $modelo): ?>
            <tr>
                <td class="text-center"><?= $this->Number->format($modelo->id) ?></td>
                <td  class="priority-3 text-center"><p class="block-status" style="background-color:<?=$modelo->cestado->colorbkg?>; color:<?=$modelo->cestado->colortext?>;"><?= $modelo->cestado->nombre?></p></td>
                <td><?= h($modelo->modelo) ?></td>
                <td><?= h($modelo->alias) ?></td>
                <td><?php
                        echo h($modelo->alias_menu);

                    ?></td>
                <td class="priority-3 text-center"><?= ($this->Number->format($modelo->movil)==1)?'Si':'No'?></td>
                <td class="acciones-tabla text-center" >
                            <span class='lista-acciones'>
                                <div class='div-acciones'>
                                    <span>Acciones</span>
                                    <ul class='lista-herramientas'>
                                        <?php foreach ($herramientas as $key => $herramienta) { ?>
                                            <li data-id="<?= $modelo->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta"><?= $herramienta ?></li>
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