<div class="container-fluid work-space">
    <div class="nav-space">

    </div>
    <div class="cont-border">
        <div class="col-md-12 text-center"><br>
            <span class="tituloImp">Catálogo de Perfiles</span>
            <br><br>
        </div>
        <br>
        <div class="perfils index large-9 medium-8 columns content">
            <br>
            <div class="col-md-12">
                <a class="btn btn-sistem btn-print" onClick="window.print()"><span>Imprimir</span><i class="fa fa-print icono"></i></a>
            </div>
            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Administrador</th>
                    <th scope="col">Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($perfils as $perfil): ?>
                    <tr>
                        <td class="text-center"><?= $this->Number->format($perfil->id) ?></td>
                        <td><?= h($perfil->nombre) ?></td>
                        <td><?= h( ($perfil->su==1) ? "Si" : "No") ?></td>
                        <td><?= h($perfil->cestado->nombre) ?></td>
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