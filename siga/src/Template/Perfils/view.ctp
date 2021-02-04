<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Perfils[]|\Cake\Collection\CollectionInterface $perfil
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
            if(isset($_SESSION['perfil-save'])){
                if($_SESSION['perfil-save']==1){
                    unset($_SESSION['perfil-save']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">

                        <span class="message">Perfil Almacenado</span>
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
                           href="<?= $real_url . $btn['funcion'].'/'.$perfil->id?>"><span><?= $btn['alias'] ?></span><i
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
            <?= $this->element('created_modified_record', ['modelo' => $perfil]) ?>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12 content-data">
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-start">Estado</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-start"><div class="col-md-2 col-xs-2 text-center state" style="background-color:<?=$perfil->cestado->colorbkg?>; color:<?=$perfil->cestado->colortext?>;"><?=$perfil->cestado->nombre?></div></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Id</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?=$perfil->id?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label">Nombre</div>
                    <div class="col-md-9 col-xs-8 col-dato"><?=$perfil->nombre?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-4 col-label lbl-border-end">Administrador</div>
                    <div class="col-md-9 col-xs-8 col-dato dato-border-end"><?=($perfil->su==1) ? "Si" : "No"?></div>
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
