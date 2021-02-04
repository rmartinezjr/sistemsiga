<?= $this->Form->create('solicitud_registro', ['novalidate', 'id' => 'frm']) ?>

<div class="panel panel-default panel-solicitud">
    <div class="panel-body" >
        <div class="form-login">
            <h1 class="title-login text-center">SOLICITAR REGISTRO</h1>

            <div class="form-group col-xs-12 col-sm-12 col-md-12 blk-label-solicitud">
                <label class="text-bold-solicitud">Seleccionar entre las siguientes:</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-md-12 div-nit-solicitud">
                <label>
                    <span class="text-bold-solicitud">1. </span>
                    <span class="label-solicitud">NIT o número de documento único de la organización</span>
                </label>
                <div class="inner-addon left-addon">
                    <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                        <input type="text" name="docorganizacion"  class="form-control" placeholder="Ingresar documento único" id="docorganizacion" onkeyup="javascript:this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');">
                </div>
            </div>
            <div class="label-o-solicitud col-xs-12 col-sm-12 col-md-12">
                <p class="text-center">o</p>
            </div>
            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <label>
                    <span class="text-bold-solicitud">2. </span>
                    <span class="label-solicitud">DUI o número de documento único si es registro individual</span>
                </label>
                <div class="inner-addon left-addon">
                    <i class="fa fa-id-card" aria-hidden="true"></i>
                    <input type="text" name="docindividual"  class="form-control"  placeholder="Ingresar documento único" id="docindividual" onkeyup="javascript:this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');">
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-sm-5 col-md-4 col-lg-4 col-xs-5">
                        <button type="submit" class="btn btn-success button-solicitud" id="registro-org">SOLICITAR</button>
                    </div>
                    <div class="informacion-proceso-registro">
                        <div class="col-sm-2 col-md-3 col-lg-3 col-xs-2 block-info-solicitud">
                            <i class="fa fa-question-circle fa-2x forgot-pwd" aria-hidden="true"></i>
                        </div>
                        <div class="col-sm-5 col-md-4 col-lg-4 col-xs-5 block-info-solicitud">
                            <?= $this->Html->link('Información del proceso de Registro', ['controller' => 'cuentaregistro', 'action' => 'informacionproceso'],['style'=>'color: #FFF;!important; text-decoration: underline;']) ?>
                        </div>
                    </div>
                    <div class="block-advertencia-documento-existe" style="display: none">
                        <div class="col-sm-1 col-md-1 col-lg-1 col-xs-1 block-triangulo-advertencia">
                            <div class="triangulo-advertencia"><span>!</span></div>
                        </div>
                        <div class="col-sm-6 col-md-7 col-lg-7 col-xs-6 block-msj-advertencia-documento">
                            <span>El número de documento ingresado ya existe, utilizar usuario registrado.<br />Si olvidó su contraseña puede solicitar su reestablecimiento desde <a href="<?= \Cake\Routing\Router::url(['controller' => 'users', 'action' => 'sendemail'], true) ?>">aquí.</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 copyright-block">
                <div class="col-xs-3 col-sm-3 col-md-3 copy-block-login">
                    <?= $this->Html->link('Iniciar Sesión', ['controller'=>'users','action' => 'login'],['class'=>'back-login']) ?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 copy-block-login copyright-text">
                    <span class="copy-solicitud-ingreso">&copy; <?= date("Y"); ?> FIAES, EL SALVADOR</span>
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
        $(document).on('change', '#docorganizacion', function(e) {
            var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'verifydocid'], true) .'/' ?>';
            var docid = $( this ).val().trim();
            if(docid  !== '') {
                $( "#docindividual" ).prop( "disabled", true );

                $.ajax({
                    url: url,
                    type: 'post',
                    data: {docid: docid, modelo: 'Entidads'},
                    dataType: 'json',
                    cache:false,
                    async:false,
                    success:function (resp) {
                        if(resp['error']===1){
                            e.preventDefault();
                            $( "#docindividual" ).prop( "disabled", false );
                            $("#docorganizacion").val("");

                            $(".informacion-proceso-registro").css("display", "none");
                            $(".block-advertencia-documento-existe").css("display", "block");
                        } else {
                            $(".informacion-proceso-registro").css("display", "block");
                            $(".block-advertencia-documento-existe").css("display", "none");
                        }
                    }
                });
            } else {
                $( "#docindividual" ).prop( "disabled", false );
            }
        });

        $(document).on('change', '#docindividual', function(e) {
            var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'verifydocid'], true) .'/' ?>';
            var docid = $( this ).val().trim();
            if(docid  !== '') {
                $( "#docorganizacion" ).prop( "disabled", true );

                $.ajax({
                    url: url,
                    type: 'post',
                    data: {docid: docid, modelo: 'Contactos'},
                    dataType: 'json',
                    cache:false,
                    async:false,
                    success:function (resp) {
                        if(resp['error']===1){
                            $( "#docorganizacion" ).prop( "disabled", false );
                            e.preventDefault();
                            $("#docindividual").val("");

                            $(".informacion-proceso-registro").css("display", "none");
                            $(".block-advertencia-documento-existe").css("display", "block");
                        } else {
                            $(".informacion-proceso-registro").css("display", "block");
                            $(".block-advertencia-documento-existe").css("display", "none");
                        }
                    }
                });
            } else {
                $( "#docorganizacion" ).prop( "disabled", false );
            }
        });

        $(document).on('click', '#registro-org', function(e) {
            e.preventDefault();

            var url = '<?= \Cake\Routing\Router::url(['controller' => 'pages', 'action' => 'registro_organizaciones'], true) ?>';
            var docidOrg = $( "#docorganizacion" ).val().trim();
            var docidIndividual = $( "#docindividual" ).val().trim();
            var form = $('#frm');

            if(docidOrg !== '' || docidIndividual !== '') {
                form.attr('action', url);
                form.submit();
            } else {
                $(".message").text("Se debe de ingresar un número de documento, ya sea de la organizacion o de la persona.");
                $("#alert").slideDown();
                setTimeout(function () {
                    $("#alert").slideUp();
                }, 4000);
            }
        });

        setTimeout(function () {
            $(".alert").slideUp();
        }, 4000);
    });
</script>
