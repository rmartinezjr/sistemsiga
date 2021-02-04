<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cpreference[]|\Cake\Collection\CollectionInterface $cpreferences
 */
?>
<div class="container-fluid work-space">
    <div class="nav-space">

    </div>
    <div class="cont-border">
        <div class="col-md-12 text-center"><br>
            <span class="tituloImp"><?= $titulo[0]['alias'] ?></span>
            <br><br>
        </div>
        <br>
        <div class="cpreferences index large-9 medium-8 columns content">
            <br>
            <div class="col-md-12">
                <a class="btn btn-sistem btn-print" onClick="window.print()"><span>Imprimir</span><i class="fa fa-print icono"></i></a>
            </div>
            <table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nombre</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cpreferences as $cpreference): ?>
                    <tr>
                        <td class="text-center"><?= $this->Number->format($cpreference->id) ?></td>
                        <td><?= h($cpreference->nombre) ?></td>
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