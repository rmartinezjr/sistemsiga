<?php $real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base')."/";  ?>
<div class="content_header">
    <div class="row">
        <div class="col-md-1 col-xs-2 col-lg-1">
            <img src="<?='//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base')?>/img/favicon-ansp.png" class="img-logo img-responsive">
        </div>
        <div class="col-md-8 col-lg-8 col-xs-7 titulo-sistem">
            <h2>Sistema para la gestion de solicitudes</h2>
            <h4>Academia Nacional de Seguridad Publica (ANSP)</h4>
        </div>
        <div class="col-md-3 col-lg-3 col-xs-3">
            <div class="contenidoDiv">
                <div class="cont-span-avatar">
                    <span class="sp-avatar"><i class="fa fa-user fa-3x"  aria-hidden="true"></i></span>
                </div>
                <div class="cont-avatar">
                    <span class="text-avatar"><?= $nombre_usuario ?></span>
                    <span class="perfil-avatar"><?= $perfil_user?></span>
                </div>
                <div style=""  class="cont-btn-salir">
                    <a class="btn btn-primary btn-logout"  href="<?=$real_url?>users/logout">
                        <span >Salir</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>