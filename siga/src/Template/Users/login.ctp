<?= $this->Flash->render('auth') ?>
<?= $this->Form->create() ?>
<br>
<div class="panel panel-default panel-login">
    <div class="panel-heading">
        <img src="../img/user-icon.png" style="display: block;width: 100px;margin: auto;">
    </div>
    <div class="panel-body">
        <div class="form-login">
            <h1 class="title-login text-center">INICIAR SESIÓN</h1>

            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label for="UserUsername" class="text-uppercase text-main"><b>Usuario</b></label>
                    <div class="icon-input">
                        <div class="icon-container">
                            <span class="icon icon-user"></span>
                        </div>
                        <?= $this->Form->input('username', [
                            'label' => false,
                            'div' => false,
                            'class' => 'user-input form-control validate[required]',
                            'placeholder' => 'Usuario',
                            'required'
                        ]); ?>
                    </div>
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label for="UserPassword" class="text-uppercase text-main"><b>Contraseña</b></label>
                    <div class="icon-input">
                        <div class="icon-container">
                            <span class="icon icon-key"></span>
                        </div>
                        <?= $this->Form->input('password', [
                            'label' => false,
                            'div' => false,
                            'class' => 'password-input form-control validate[required]',
                            'placeholder' => 'Contraseña',
                            'required'
                        ]); ?>
                    </div>
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <div class="row block-actions-login">
                    <div class="col-md-offset-2 col-sm-5 col-md-6 col-lg-6 col-xs-5 block-button-login">
                        <button type="submit" class="btn btn-primary btn-login btn-lg">
                            <i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Iniciar Sesion
                        </button>
                    </div>
                    <div class="col-sm-7 col-md-6 col-lg-6 col-xs-7 forgot-pw-blk">
                        <div class="form-group">
                            <div class="forgot-pwd-block">
                                <i class="fa fa-question-circle forgot-pwd" aria-hidden="true"></i>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?= $this->Form->end() ?>
    </div>
    <div class="panel-footer">
        <div class="col-xs-12 col-sm-12 col-md-12 copyright-block text-center">
            <?= $this->Html->link('¿Olvidó su contraseña?', ['action' => 'sendemail'],['class'=>'forgot-pwd']) ?>
        </div>
    </div>
</div>

