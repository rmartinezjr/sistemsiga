<?= $this->Form->create('User', ['id'=>'formulario', 'novalidate']) ?>

<div class="panel panel-default panel-contrasenia">
    <div class="panel-body">
        <div class="form-login">
            <h1 class="title-login text-center">Restablecer contraseña</h1>

            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <label class="label-login">Contraseña
                </label>
                <div class="inner-addon left-addon">
                    <i class="fa fa-key" aria-hidden="true"></i>
                    <input type="password" name="password"  class="form-control"  placeholder="Ingresar Contraseña" required="required" id="password">
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <label class="label-login">Repita contraseña
                </label>
                <div class="inner-addon left-addon">
                    <i class="fa fa-key" aria-hidden="true"></i>
                    <input type="password" name="rpassword" class="form-control" placeholder="Ingresar nuevamente la contraseña" required="required" id="rpassword">
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-sm-offset-3 col-sm-6 col-md-offset-3 col-md-6 col-lg-offset-3 col-lg-6 col-xs-offset-3 col-xs-6">
                        <button type="submit" class="btn btn-success button-login">ENVIAR</button>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 copyright-block">
                <div class="col-xs-3 col-sm-3 col-md-3 copy-block-login">
                    <?= $this->Html->link('Iniciar Sesión', ['controller'=>'users','action' => 'login'],['class'=>'back-login']) ?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 copy-block-login copyright-text">
                    <span class="copy-solicitud-ingreso">&copy; <?= date("Y"); ?> ANSP, EL SALVADOR</span>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 copy-block-login">
                    <?= $this->Html->link('Inicio', ['controller'=>'pages','action' => 'home'],['class'=>'back-home']) ?>
                </div>
            </div>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>
<script>
    jQuery(function(){
        $("#formulario").submit(function() {
            var password = $('#password').val();
            var rpassword = $('#rpassword').val();
            var lon = password.length;

            if (lon <7) {
                $(".alert-danger .message").text("La contrase\u00f1a debe de tener como minimo 8 caracteres.");
                $(".alert-danger").slideDown();
                setTimeout(function () {
                    $(".alert-danger").slideUp();
                }, 4000);
                return false;
            }

            if (password != rpassword) {
                $(".alert-danger .message").text("Las contrase\u00f1as no coinciden.");
                $(".alert-danger").slideDown();
                setTimeout(function () {
                    $(".alert-danger").slideUp();
                }, 4000);
                return false;
            }
        });
    });
</script>