<?php if(isset($modelo)): if(is_object($modelo)): ?>
    <div class="<?php if(isset($col)):?><?=$col?><?php else: ?>col-md-12<?php endif; ?> info-log">
        <?php if($modelo->usuariomodif!=''): ?>
            <span class="lbl">Última Actualización:</span><span class="lbl-data"><?=$modelo->modified->Format('d-m-Y H:i:s')?></span>
        <?php endif; if(isset($lastlogin)):?>
            <span class="lbl">Último Ingreso:</span><span class="lbl-data"><?php if(!empty($modelo->lastlogin)): ?><?=$modelo->lastlogin->Format('d-m-Y H:i:s')?><?php else: ?>El usuario aún no ha iniciado sesión<?php endif; ?></span>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="<?php if(isset($col)):?><?=$col?><?php else: ?>col-md-12<?php endif; ?> info-log">
        <?php if($modelo["usuariomodif"]!=''): ?>
            <span class="lbl">Última Actualización:</span><span class="lbl-data"><?=$data["modified"]->Format('d-m-Y H:i:s')?></span>
        <?php endif; if(isset($lastlogin)):?>
            <span class="lbl">Último Ingreso:</span><span class="lbl-data"><?php if(!empty($modelo->lastlogin)): ?><?=$modelo->lastlogin->Format('d-m-Y H:i:s')?><?php else: ?>El usuario aún no ha iniciado sesión<?php endif; ?></span>
        <?php endif; ?>
    </div>
<?php endif; endif;?>
