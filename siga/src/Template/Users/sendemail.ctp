
<div class="panel-sendemail">
    <div class="panel-body">
        <div class="form-login">
            <h1 class="title-login text-center">¿OLVIDÓ SU CONTRASEÑA?</h1>
            <?= $this->Form->create() ?>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <label class="text-bold-sendemail">Ingrese su correo electrónico o usuario para resetear contraseña.</label>
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <div class="form-group col-xs-12 col-sm-12 col-md-12">
                    <label class="label-login">Usuario o Correo Electrónico
                    </label>
                    <div class="inner-addon left-addon">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" name="usuario"  class="form-control" placeholder="Usuario o Correo Electrónico" required="required" id="usuario">
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-sm-offset-3 col-sm-6 col-md-offset-3 col-md-6 col-lg-offset-3 col-lg-6 col-xs-offset-3 col-xs-6">
                        <button type="submit" class="btn btn-primary btn-login btn-lg">ENVIAR</button>
                    </div>
                </div>
            </div>
            <?= $this->Form->end() ?>

        </div>
    </div>
    <div class="panel-footer">
        <div class="col-xs-12 col-sm-12 col-md-12 copyright-block text-center">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 copy-block-login">
                <?= $this->Html->link('Iniciar Sesión', ['controller'=>'users','action' => 'login'],['class'=>'back-login']) ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-9 col-lg-9 copy-block-login copyright-text">
                <span class="copy-solicitud-ingreso">&copy; <?= date("Y"); ?> |  Academia Nacional de Seguridad Publica</span>
            </div>
        </div>
    </div>
</div>
