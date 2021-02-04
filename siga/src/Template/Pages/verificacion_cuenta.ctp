<div class="row work-space registro">
    <div class="nav-space">
        <p class="text-left lbl-navegacion">Registro > Datos iniciales</p>
    </div>
    <div class="cont-border">
        <div class="row">
            <div class="col-md-5">
                <h2><?= $title ?></h2>
            </div>
            <div class="col-md-offset-5 col-md-2 ayuda-registro">
                <div class="col-sm-2 col-md-3 col-lg-3 col-xs-2 help-icon">
                    <i class="fa fa-question-circle fa-2x" aria-hidden="true"></i>
                </div>
                <div class="col-sm-10 col-md-9 col-lg-9 col-xs-10 info-proceso-block">
                    <?= $this->Html->link('Información del proceso de Registro', ['action' => 'ayuda_registro'],['class' => 'info-proceso-registro']) ?>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8">
                <p>Se ha podido confirmar la validez de la cuenta de correo utilizada en la primera etapa de registro de su organización. </p>
            </div>
        </div>
        <form id="formulario" class="form-registro" method="post" action="">
            <div class="row">
                <div class="col-xs-12">
                    <span class="tittle-form">Ingresar Contraseña</span>
                    <button class="btn-registro pull-right" type="submit">Enviar</button>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 advertencia">
                    <span class="lbl-advertencia">Advertencias</span><br>
                    <span class="info-advertencia">Por favor ingresar la contraseña de ingreso y presionar </span> <span class="info-advertencia advertencia-bold">Aceptar.</span><br>
                    <span class="info-advertencia">El nombre de usuario es asignado automáticamente, no puede ser modificado. </span>

                    <ul>
                        <li>Guardar contraseña en un lugar seguro y nunca enviarla vía correo electrónico.</li>
                        <li>FIAES nunca envía ni solicita contraseña vía correo electrónico.</li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <table class="table-user">
                        <tr>
                            <td>
                                <p>1. Usuario</p>
                                <div class="col-xs-6 control-block">
                                    <input type="text" name="username"  class="form-control" id="username" disabled>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>2. Ingresar Contraseña</p>
                                <div class="col-xs-6 control-block">
                                    <input type="text" name="password"  class="form-control" placeholder="Ingresar la contraseña" required="required" id="password">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>3. Re-Ingresar Contraseña</p>
                                <div class="col-sm-6 control-block">
                                    <input type="text" name="repassword"  class="form-control" placeholder="Volver a ingresar la contraseña" required="required" id="repassword">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>4. Correo Electrónico</p>
                                <div class="col-sm-6 control-block">
                                    <input type="text" name="email"  class="form-control" id="email" disabled>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    jQuery(function(){

    });
</script>
