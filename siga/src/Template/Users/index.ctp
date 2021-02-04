<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Centidadtipo[]|\Cake\Collection\CollectionInterface $centidadtipos
 */

echo $this->Html->script(['lib/bootbox.min.js','lib/jquery.blockUI.js']);

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
            if(isset($_SESSION['UsersDele'])){
                if($_SESSION['UsersDele']==1){
                    unset($_SESSION['UsersDele']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Usuario Eliminado</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>

            <div  id="displayEnv" style="display: none;">
                <i class="fa fa-spinner fa-pulse fa-2x fa-fw"  style="margin-top: 5px;"></i>
                <span >Enviando...</span>
            </div>



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
                'controller' => 'Users', // Nombre del controlador
                'placeholder' => 'Id, Usuario, E-mail, Contacto', // Placeholder del campo de texto
                // Nombres de campos segun base de datos (Busqueda en campo de texto)
                'campos' => 'id,username,email,cestado_id,Contactos.nombres,Contactos.apellidos',
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
                    'Perfils' => [
                        'opciones' => $perfils,
                        'campo' => 'perfil_id',
                        'label' => 'Perfiles',
                        'empty' => 'Perfil'
                    ],

                    'Cestados' => [
                        'opciones' => $cestados,
                        'campo' => 'cestado_id',
                        'label' => 'Estados',
                        'empty' => 'Estado'
                    ],


                ]
                // Ancho del contenedor que tiene los select
                //'widthSelect' => 2, // Numero de columnas para el contenedor
            ]) ?>
        </div>
        <div class="centidadtipos index large-9 medium-8 columns content">
            <p class="info-paginacion"><?= $this->Paginator->counter(['format' => __('Página {{page}} de {{pages}}, {{current}} registros de un total de {{count}}, comienza en 1, finalizando en {{current}}')]) ?></p>

            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
        <thead>
            <tr>
                <th class="th-id"  scope="col"><?= $this->Paginator->sort('Users.id','Id') ?></th>
                <th scope="col" class="priority-3 th-estado"><?= $this->Paginator->sort('Cestados.nombre','Estado',['direction'=>$dirdefault] ) ?></th>
                <th scope="col"><?= $this->Paginator->sort('Users.username','Usuario') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Users.email','E-mail') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Perfils.nombre','Perfil',['direction'=>$dirdefault] ) ?></th>
                <th scope="col"><?= $this->Paginator->sort('Contactos.nombres','Contacto',['direction'=>$dirdefault] ) ?></th>
                <th scope="col" class="actions th-actions" ><?= __('') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $cestados=$cestados->toArray();
            foreach ($users as $user): ?>
            <tr>
                <td class="text-center"><?= $this->Number->format($user->id) ?></td>
                <td class="priority-3 text-center"><p class="block-status" style="background-color:<?=$user->cestado->colorbkg?>; color:<?=$user->cestado->colortext?>;"><?= h( $cestados[$user->cestado_id]) ?></p></td>
                <td><?= h($user->username) ?></td>
                <td><?= h($user->email) ?></td>
                <td><?= $this->Html->link(__( $user->perfil->nombre), ['controller' => 'Perfils', 'action' => 'view', $user->perfil->id],['class'=>'a-underline']) ?></td>
                <td><?= $this->Html->link(__( h($user->contacto->nombres).' '.h($user->contacto->apellidos)), ['controller' => 'Contactos', 'action' => 'view', $user->contacto->id],['class'=>'a-underline']) ?></td>
                <td class="acciones-tabla text-center">
                            <span class='lista-acciones'>
                                <div class='div-acciones'>
                                    <span>Acciones</span>
                                    <ul class='lista-herramientas'>
                                        <?php foreach ($herramientas as $key => $herramienta) { ?>
                                            <li data-id="<?= $user->id ?>" data-funcion="<?= $key ?>" class="accion-herramienta"><?= $herramienta ?></li>
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


if($(this).attr('data-funcion')=="reset")
{
    $.blockUI({
        message:$('#displayEnv'),
        css: {
            top:  ($(window).height() - 220) /2 + 'px',
            left: ($(window).width() - 130) /2 + 'px',
            border: '2px solid #d4d4d4',
            width: '130px',
            height:'40px'
        }
    });
}


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
