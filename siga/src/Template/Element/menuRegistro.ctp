<div style="
    height: 100%;
    padding-top: 10px;
   ">
    <div style="    width: 73px;
    height: 50%;
    padding-top: 10px;" class="divSubmenu">
        <ul class="sidebar-nav nav-pills nav-stacked" id="menu">
            <li >
                <a style="width: 30px;" href="<?= \Cake\Routing\Router::url(['controller' => 'Pages', 'action' => 'home'], true); ?>"  class="icoIni" id="icoIni"></a>
            </li>
            <li >
                <a style="width: 30px;" href="<?= ($active == 'ayuda') ? \Cake\Routing\Router::url(['controller' => 'Pages', 'action' => 'solicitud_registro'], true) : '' ?>"  class="<?= ($active == 'registro') ? 'icoOrg2' : 'icoOrg' ?>" id="icoOrg"></a>
            </li>
            <li >
                <a style="width: 30px;" href="<?= \Cake\Routing\Router::url(['controller' => 'cuentaregistro', 'action' => 'informacionproceso'], true); ?>"  class="<?= ($active == 'ayuda') ? 'icoHelp2' : 'icoHelp' ?>" id="icoHelp" target="_blank"></a>
            </li>
        </ul>

    </div>
</div>


