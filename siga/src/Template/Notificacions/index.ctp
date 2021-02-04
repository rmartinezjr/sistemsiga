<?php
echo $this->Html->script(['lib/bootbox.min.js']);
$real_url = \Cake\Routing\Router::url(['action' => 'index'], true) .'/'
?>
<script>
    function getUrl(){
        return "<?=$real_url?>";
    }
</script>
<div class="row work-space">
    <div class="nav-space">
        <p class="text-left lbl-navegacion"><?=$nav?></p>
    </div>
    <div class="cont-border">
        <div class="row">
            <!-- Divs para mensajes alert -->
        </div>
        <div class="row">
            <div class="col-md-6">
                <h2 class="tittle"><?= $titulo[0]['alias'] ?></h2>
            </div>
            <div class="col-md-6 panel-action">
                <!-- Elemento para mostrar botones  -->
            </div>
        </div><br>
        <div class="row">
            <!--  Elemento para mostrar el formulario de busqueda  -->
            <?php
            $perfiles = [1=>"Admin",2=>"Normal"];
            echo    $this->element('search', [
                'controller' => 'Notificacioncolas', // Nombre del controlador
                'placeholder' => 'Mensaje', // Placeholder del campo de texto
                // Nombres de campos segun base de datos (Busqueda en campo de texto)
                'campos' => 'Notificacions.mensaje',
                // Rango de Fechas
                'fecha' => [
                    'rangoFecha' => true, // True: si se muestra rango de fechas. False: No se muestra rango de fechas
                    'placeholder1'=>'Fecha Desde',
                    'placeholder2'=>'Fecha Hasta',
                    'custom'=>0,
                    'campo' => 'created', // Nombre de campo de busqueda para rango de fecha
                    'datetime' => '1', // '1': Si la busqueda es fecha y hora. '0': Si la busqueda es solo por fecha.
                ],
                'inactivo'=>0,
                // Combobox a mostrar en la busqueda con sus opciones
                // Si no se muestran combobox colocar array vacio.
                'select' => []
            ]) ?>
        </div>
        <div class="notificacion index large-9 medium-8 columns content">
            <p class="info-paginacion"><?= $this->Paginator->counter(['format' => __('Página {{page}} de {{pages}}, {{current}} registros de un total de {{count}}, comienza en 1, finalizando en {{current}}')]) ?></p>

            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th class="th-id" scope="col"><?= $this->Paginator->sort('Notificacions.id', 'Id', ['direction'=>$dirdefault]) ?></th>
                    <th scope="col" class="priority-3"><?= $this->Paginator->sort('Notificacions.mensaje','Mensaje', ['direction'=>$dirdefault]) ?></th>
                    <th width="350px" scope="col"><?= $this->Paginator->sort('Notificacioncolas.created','Fecha de Notificación') ?></th>
                    <th scope="col" class="priority-3"><?= $this->Paginator->sort('Notificacioncolas.visto','Notificación Leída') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($notificacions as $notificacion): ?>
                    <tr>
                        <td class="text-center"><?= $this->Number->format($notificacion->notificacion->id) ?></td>
                        <td <?php if(!$notificacion->visto):?> class="font-bold" <?php endif; ?>><a href="<?=$notificacion->notificacion->url?>" id="ahref-notify-index-<?=$notificacion->notificacion->id?>" <?php if(!$notificacion->visto):?>data-notify="<?=$notificacion->notificacion->id?>" data-username="<?=$userName?>" onclick="viewNotify(this.id); return false;" <?php endif; ?>><?= h($notificacion->notificacion->mensaje) ?></a></td>
                        <td class="text-center"><?= $notificacion->created->Format('d')." ".$meses[$notificacion->created->Format('m')-1]." ".$notificacion->created->Format('Y') ?></td>
                        <td class="text-center <?php if(!$notificacion->visto):?>font-bold<?php endif; ?>"><?= ($notificacion->visto) ? "Si" : "No" ?></td>
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