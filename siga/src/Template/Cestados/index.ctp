<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cestado[]|\Cake\Collection\CollectionInterface $cestados
 */
echo $this->Html->script(['lib/bootbox.min.js']);

$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
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
            if(isset($_SESSION['cestadoDele'])){
                if($_SESSION['cestadoDele']==1){
                    unset($_SESSION['cestadoDele']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">

                        <span class="message">Estado Eliminado</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>

            <?php
            if(isset($_SESSION['error_eliminarcestado'])){
                if($_SESSION['error_eliminarcestado']==1){
                    unset($_SESSION['error_eliminarcestado']);?>
                    <div class="alert alert-danger" id="alert" style="display: none;">

                        <span class="message">El estado no puede eliminarse, está siendo utilizado en <?= $_SESSION["error_eliminarsmj"];?>.</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>

            <?php
            if(isset($_SESSION['error_editarcestado'])){
                if($_SESSION['error_editarcestado']==1){
                    unset($_SESSION['error_editarcestado']);?>
                    <div class="alert alert-danger" id="alert" style="display: none;">

                        <span class="message">El estado no puede editarse, está siendo utilizado en <?= $_SESSION["error_editarmsjcestado"];?>.</span>
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
                echo $this->element('search', [
                    'controller' => 'Cestados', // Nombre del controlador
                    'placeholder' => 'Id, Nombre, Descripción', // Placeholder del campo de texto
                    // Nombres de campos segun base de datos (Busqueda en campo de texto)
                    'campos' => 'id,nombre,descripcion',
                    // Rango de Fechas
                    'fecha' => [
                        'rangoFecha' => false   , // True: si se muestra rango de fechas. False: No se muestra rango de fechas
                    ],
                    'inactivo'=>0,
                    // Combobox a mostrar en la busqueda con sus opciones
                    // Si no se muestran combobox colocar array vacio.
                    'select' => []
                ]) ?>
        </div>
        <div class="cestados index large-9 medium-8 columns content">
            <p class="info-paginacion"><?= $this->Paginator->counter(['format' => __('Página {{page}} de {{pages}}, {{current}} registros de un total de {{count}}, comienza en 1, finalizando en {{current}}')]) ?></p>

            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th class="th-id" scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('nombre') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('colorbkg','Color de Fondo') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('colortext','Color de Letra') ?></th>
                    <th scope="col" class="priority-3"><?= $this->Paginator->sort('descripcion','Descripción') ?></th>
                    <th scope="col" class="actions th-actions"><?= __('') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cestados as $cestado): ?>
                    <tr>
                        <td class="text-center"><?= $this->Number->format($cestado->id) ?></td>
                        <td>
                            <p class="block-status" style="word-wrap: break-word;background-color: <?= h($cestado->colorbkg) ?>; color: <?= h($cestado->colortext) ?>;"> <?= h($cestado->nombre) ?></p>
                        </td>
                        <td>
                            <?= h($cestado->colorbkg) ?>
                        </td>
                        <td>
                            <?= h($cestado->colortext) ?>
                        </td>
                        <td class=" priority-3"><?= h((strlen($cestado->descripcion) > 110) ? substr($cestado->descripcion,0,110) . "..." : $cestado->descripcion) ?></td>
                        <td class="acciones-tabla text-center">
                            <span class='lista-acciones'>
                                <div class='div-acciones'>
                                    <span>Acciones</span>
                                    <ul class='lista-herramientas'>
                                        <?php foreach ($herramientas as $key => $herramienta) { ?>
                                            <li data-id="<?= $cestado->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta"><?= $herramienta ?></li>
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