<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cerror $cerror
 */

$real_url = \Cake\Routing\Router::url(['action' => 'index'], true) .'/';
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
            if(isset($_SESSION['cerror-save'])){
                if($_SESSION['cerror-save']==1){
                    unset($_SESSION['cerror-save']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Catalogo de Error Almacenado</span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                <?php           }
            }
            ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h2 class="tittle"><?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
            </div>
            <div class="col-md-6 panel-action">
                <?php foreach ($controltools as $btn) {
                    if ($btn['funcion'] === "imprimir") { ?>
                        <a class="btn btn-sistem <?= $btn['class'] ?>"
                           onClick="window.open('<?= $real_url . $btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i
                                class="fa <?= $btn['icon'] ?> icono"></i></a>
                    <?php } else if ($btn['funcion'] === "edit") { ?>
                        <a class="btn btn-sistem <?= $btn['class'] ?>"
                           href="<?= $real_url . $btn['funcion'].'/'.$cerror->id?>"><span><?= $btn['alias'] ?></span><i
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
            <?= $this->element('created_modified_record', ['modelo' => $cerror]) ?>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12 content-data">
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-start">Estado</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-start"><div class="col-md-2 col-xs-2 text-center state" style="background-color:<?=$cerror->cestado->colorbkg?>; color:<?=$cerror->cestado->colortext?>;"><?=$cerror->cestado->nombre?></div></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-label">Id</div>
                    <div class="col-md-9 col-dato"><?=$cerror->id?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-label">Nombre</div>
                    <div class="col-md-9 col-dato"><?=$cerror->nombre?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-label">Código</div>
                    <div class="col-md-9 col-dato"><?=$cerror->codigo?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-label lbl-border-end">Mensaje de Error</div>
                    <div class="col-md-9 col-dato dato-border-end"><?=$cerror->html?></div>
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
