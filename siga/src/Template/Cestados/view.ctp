<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cestado[]|\Cake\Collection\CollectionInterface $cestados
 */
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']);
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
            if(isset($_SESSION['cestado-save'])){
                if($_SESSION['cestado-save']==1){
                    unset($_SESSION['cestado-save']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">

                        <span class="message">Estado Almacenado</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h2 class="tittle"><?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
            </div>
            <div class="col-md-8 panel-action">
                <?php foreach ($controltools as $btn) {
                    if ($btn['funcion'] === "imprimir") { ?>
                        <a class="btn btn-sistem <?= $btn['class'] ?>"
                           onClick="window.open('<?= $real_url . $btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i
                                    class="fa <?= $btn['icon'] ?> icono"></i></a>
                    <?php } else if ($btn['funcion'] === "edit") { ?>
                        <a class="btn btn-sistem <?= $btn['class'] ?>"
                           href="<?= $real_url . $btn['funcion'].'/'.$cestado->id?>"><span><?= $btn['alias'] ?></span><i
                                    class="fa <?= $btn['icon'] ?> icono"></i></a>
                    <?php } else {
                        ?>
                        <a class="btn btn-sistem <?= $btn['class'] ?>"
                           href="<?= $real_url . $btn['funcion'] ?>"><span><?= $btn['alias'] ?></span><i
                                    class="fa <?= $btn['icon'] ?> icono"></i></a>
                    <?php }
                }
                ?>
            </div>
        </div>
        <div class="row">
            <?= $this->element('created_modified_record', ['modelo' => $cestado]) ?>
        </div>

        <div class="row">
            <div class="col-md-12 col-xs-12 content-data">
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-start">Id</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-start"><?=$cestado->id?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Nombre</div>
                    <div class="col-md-9 col-xs-8 col-dato"> <p class="block-status" style="margin: 0px;background-color: <?= h($cestado->colorbkg) ?>; color: <?= h($cestado->colortext) ?>; "> <?= h($cestado->nombre) ?></p>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Color de Fondo</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?=$cestado->colorbkg?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Color de Letra</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?=$cestado->colortext?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-end">Descripción</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-end"><?=$cestado->descripcion?></div>
                </div>
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
    });
</script>
