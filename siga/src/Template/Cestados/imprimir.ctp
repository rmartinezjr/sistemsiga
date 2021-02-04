<div class="container-fluid work-space">
    <div class="nav-space">

    </div>
    <div class="cont-border">
        <div class="col-md-12 text-center"><br>
            <span class="tituloImp">Catálogo de Estados</span>
            <br><br>
        </div>
        <br>
        <div class="cestados index large-9 medium-8 columns content">
            <br>
            <div class="col-md-12">
                <a class="btn btn-sistem btn-print" onClick="window.print()"><span>Imprimir</span><i class="fa fa-print icono"></i></a>
            </div>
            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripción</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cestados as $cestado): ?>
                    <tr>
                        <td class="text-center"><?= $this->Number->format($cestado->id) ?></td>
                        <td><?= h($cestado->nombre) ?></td>
                        <td><?= h($cestado->descripcion) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table><br><br>
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-sistem btn-print" onClick="window.print()"><span>Imprimir</span><i class="fa fa-print icono"></i></a>
                </div>
            </div>
        </div>
    </div>

</div>