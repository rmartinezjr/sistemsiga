<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cpreference $cpreference
 */

$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js']);

$parametros = $cpreference->params;
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
            if(isset($_SESSION['cpreference-save'])){
                if($_SESSION['cpreference-save']==1){
                    unset($_SESSION['cpreference-save']);?>
                    <div class="alert alert-success" id="alert" style="display: none;">
                        <span class="icon icon-cross-circled"></span>
                        <span class="message">Preferencia o Configuración Almacenada</span>
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
                           href="<?= $real_url . $btn['funcion'].'/'.$cpreference->id?>"><span><?= $btn['alias'] ?></span><i
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
            <?= $this->element('created_modified_record', ['modelo' => $cpreference]) ?>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12 content-configuracion">
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-3 col-label lbl-border-start">Id</div>
                    <div class="col-md-9 col-xs-9 col-dato dato-border-start"><?=$cpreference->id?></div>
                </div>
                <div class="col-md-12 col-xs-12 fila-data">
                    <div class="col-md-3 col-xs-3 col-label lbl-border-end">Nombre</div>
                    <div class="col-md-9 col-xs-9 col-dato dato-border-end">
                        <?=$cpreference->nombre; ?>
                    </div>
                </div>
            </div>
            <?php if(!is_null($parametros)) { ?>
                <div class="col-md-12 col-xs-12 contenido-parametros">
                    <h4 class="tittle-parametros">Parámetros</h4>
                    <?php foreach ($parametros as $key => $value) {
                        // debug($value);
                        $i = 0;
                        $lblclase = '';
                        $datoclase = '';
                        if($i == 0) {
                            $lblclase = 'lbl-border-start';
                            $datoclase = 'dato-border-start';
                        } elseif($value == end($parametros)) {
                            $lblclase = 'lbl-border-end';
                            $datoclase = 'dato-border-end';
                        }
                        ?>

                        <div class="col-md-12 col-xs-12 fila-data">
                            <div class="col-md-3 col-xs-3 col-label <?= $lblclase ?>"><?= $key ?></div>
                            <div class="col-md-9 col-xs-9 col-dato <?= $datoclase ?>">
                                <?=($key=='filemaxsize')?json_encode($value).' bit ( '.strval(round(json_encode($value)/pow(1024,2), 2)).' MB )':json_encode($value)?>
                            </div>
                        </div>
                        <?php $i++; ?>
                    <?php } ?>
                </div>
            <?php } ?>
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
