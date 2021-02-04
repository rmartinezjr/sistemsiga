<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cindicadorambito[]|\Cake\Collection\CollectionInterface $cindicadorambito
 */
echo $this->Html->script(['lib/bootbox.min.js']);
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
?>
<style>
    .checkbox{
        margin-top: 5px;
    }
</style>
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
            if(isset($_SESSION['perfilsDele'])){
                if($_SESSION['perfilsDele']==1){
                    unset($_SESSION['perfilsDele']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Perfil Eliminado</span>
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
                'controller' => 'Perfils', // Nombre del controlador
                'placeholder' => 'Id, Nombre', // Placeholder del campo de texto
                // Nombres de campos segun base de datos (Busqueda en campo de texto)
                'campos' => 'id,nombre',
                // Rango de Fechas
                'fecha' => [
                    'rangoFecha' => false   , // True: si se muestra rango de fechas. False: No se muestra rango de fechas
                ],
                'inactivo'=>0,
                // Combobox a mostrar en la busqueda con sus opciones
                // Si no se muestran combobox colocar array vacio.
                'select' => [array('campo'=>'cestado_id', 'opciones'=>$tiposestados,'empty' => 'Estado')],
                'checkbox'=>[array('campo'=>'su','label'=>'Administrador')]
            ]) ?>
        </div>
        <div class="perfils index large-9 medium-8 columns content">
            <p class="info-paginacion"><?= $this->Paginator->counter(['format' => __('Página {{page}} de {{pages}}, {{current}} registros de un total de {{count}}, comienza en 1, finalizando en {{current}}')]) ?></p>

            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th scope="col" class="th-id"><?= $this->Paginator->sort('Perfils.id','Id') ?></th>
                    <th scope="col" class="priority-3 th-estado"><?= $this->Paginator->sort('Cestados.nombre','Estado',['direction'=>$dirdefault]) ?></th>
                    <th scope="col"><?= $this->Paginator->sort('Perfils.nombre','Nombre') ?></th>
                    <th scope="col" class="priority-3"><?= $this->Paginator->sort('Perfils.su','Administrador') ?></th>
                    <th scope="col" class="actions th-actions"><?= __('') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($perfils as $perfil): ?>
                    <tr>
                        <td class="text-center"><?= $this->Number->format($perfil->id) ?></td>
                        <td class="priority-3 text-center"><p class="block-status" style="background-color:<?=$perfil->cestado->colorbkg?>; color:<?=$perfil->cestado->colortext?>;"><?= h($perfil->cestado->nombre) ?></p></td>
                        <td><?= h($perfil->nombre) ?></td>
                        <td class="priority-3"><?= h(($perfil->su == 1) ? "Si" : "No") ?></td>
                        <td class="acciones-tabla text-center">
                            <span class='lista-acciones'>
                                <div class='div-acciones'>
                                    <span>Acciones</span>
                                    <ul class='lista-herramientas'>
                                        <?php foreach ($herramientas as $key => $herramienta) { ?>
                                            <li data-id="<?= $perfil->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta"><?= $herramienta ?></li>
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
                <?php
                if ($this->Paginator->counter(['format' => __('{{pages}}')])>1){
                    ?>
                    <ul class="paginate">
                        <?php // $this->Paginator->first('<< ' . __('first')) ?>
                        <?= $this->Paginator->prev('<< ' . __('Anterior')) ?>
                        <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
                        <?= $this->Paginator->next(__('Siguiente') . ' >>') ?>
                        <?php // $this->Paginator->last(__('last') . ' >>') ?>
                    </ul>
                <?php           }else{  ?>
                    <ul class="paginate2">
                        <?php // $this->Paginator->first('<< ' . __('first')) ?>
                        <?= $this->Paginator->prev('<< ' . __('Anterior')) ?>
                        <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
                        <?= $this->Paginator->next(__('Siguiente') . ' >>') ?>
                        <?php // $this->Paginator->last(__('last') . ' >>') ?>
                    </ul>
                <?php                } ?>
            </div>
        </div>
    </div>

</div>
<script>
    function action(id){
        if(id!== ''){
            var option = $("#action"+id).val();
            if(option !== ''){
                window.location=getUrl() + option + '/' + id;
            }
        }
    }
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