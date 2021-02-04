<div class="row work-space registro">
    <div class="nav-space">
        <p class="text-left lbl-navegacion">Registro > Datos iniciales</p>
    </div>
    <div class="cont-border">
        <div class="row">
            <div class="col-sm-5">
                <h2><?= $title ?></h2>
            </div>
            <div class="col-sm-offset-5 col-sm-2 ayuda-registro">
                <div class="col-sm-2 col-md-3 col-lg-3 col-xs-7 help-icon">
                    <div class="icoHelp3 pull-right"></div>
                </div>
                <div class="col-sm-10 col-md-9 col-lg-9 col-xs-5 info-proceso-block">
                    <?= $this->Html->link('Información del proceso de Registro', ['controller' => 'cuentaregistro', 'action' => 'informacionproceso'],['class' => 'info-proceso-registro', 'target' => '_blank']) ?>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p>Ingresar la siguiente información, todos los campos son requeridos. </p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?= $this->Flash->render(); ?>
                <div class="alert alert-danger no-display" id="alert">
                    <span class="icon icon-cross-circled"></span>
                    <span class="message"></span>
                    <button type="button" class="close" data-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <form id="formulario" class="form-registro" method="post" action="<?= \Cake\Routing\Router::url(['action' => 'registro_organizaciones'], true); ?>">
            <input type="hidden" id="tipo-registro" name="tipo_registro" value="<?= $tipo_registro; ?>">
            <input type="hidden" id="existe-entidad" name="Entidads[existe]">
            <input type="hidden" id="existe-contacto" name="Contactos[existe]">
            <div class="row">
                <div class="col-xs-12">
                    <span class="tittle-form">Organización</span>
                    <!--button class="btn-registro pull-right" type="submit">Enviar</button -->
                    <input class="btn-registro pull-right" type="submit" value="Enviar">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <table class="table-org">
                        <tr>
                            <td>
                                <p>1. NIT o número de Documento de la Organización</p>
                                <div class="col-sm-6 lbl-required"><span class="pull-right">Obligatorio</span></div>
                                <div class="clearfix"></div>
                                <div class="col-sm-2 control-block">
                                    <?=$this->Form->control('nacional',[
                                        'label'=>false,
                                        'name' => 'Entidads[nacional]',
                                        'div'=>['class'=>'form-group'],
                                        'options' => ['1'=>'Salvadoreño', '0'=>'Extranjero'],
                                        'class'=>'form-control select-control',
                                        'empty' => 'Origen',
                                        'id'=>'nacional',
                                        'required'
                                    ]);?>
                                </div>
                                <!-- div class="col-sm-2 control-block">
                                    <?php /*$this->Form->control('cdocidtipoid_org',[
                                        'label'=>false,
                                        'name' => 'Entidads[cdocidtipo_id]',
                                        'div'=>['class'=>'form-group'],
                                        'options' => $cdocidtiposorg,
                                        'class'=>'form-control select-control',
                                        'empty' => 'Tipo de documento',
                                        'required'
                                    ]);*/?>
                                </div-->
                                <div class="col-sm-4 control-block">
                                    <input type="text" name="Entidads[docid]"  class="form-control" placeholder="Ingresar NIT o documento único de identidad" required="required" id="docid-org" <?php if($tipo_registro == 1) { ?> value="<?= $docid_org ?>" readonly <?php } else { ?> disabled <?php } ?>>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>2. Nombre</p>
                                <div class="col-sm-6 lbl-required"><span class="pull-right">Obligatorio</span></div>
                                <div class="clearfix"></div>
                                <div class="col-xs-6 control-block">
                                    <input type="text" name="Entidads[nombre]"  class="form-control is-unique" placeholder="Ingresar nombre de la organización" required="required" id="nombre-org" data-modelo="Entidads" data-campo="nombre" data-label="nombre de la organizacion">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>3. Nombre Completo</p>
                                <div class="col-sm-6 lbl-required"><span class="pull-right">Obligatorio</span></div>
                                <div class="clearfix"></div>
                                <div class="col-xs-6 control-block">
                                    <input type="text" name="Entidads[nombrelargo]"  class="form-control" placeholder="Ingresar nombre de la organización" required="required" id="nombrelargo-org">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>4. Tipo y Rol de la Organización</p>
                                <div class="col-sm-6 lbl-required"><span class="pull-right">Obligatorio</span></div>
                                <div class="clearfix"></div>
                                <div class="col-sm-2 control-block">
                                    <?=$this->Form->control('centidadtipo_id',[
                                        'label'=>false,
                                        'name' => 'Entidads[centidadtipo_id]',
                                        'div'=>['class'=>'form-group'],
                                        'options' => $centidadtipos,
                                        'class'=>'form-control select-control',
                                        'empty' => 'Tipo de organización',
                                        'required'
                                    ]);?>
                                </div>
                                <div class="col-sm-2 control-block">
                                    <?=$this->Form->control('centidadrol_id',[
                                        'label'=>false,
                                        'name' => 'Entidads[centidadrol_id]',
                                        'div'=>['class'=>'form-group'],
                                        'options' => $centidadrols,
                                        'class'=>'form-control select-control',
                                        'empty' => 'Rol de Organización',
                                        'required'
                                    ]);?>
                                </div>
                                <!-- div class="col-sm-2 control-block">
                                    <?php /*$this->Form->control('nacional',[
                                        'label'=>false,
                                        'name' => 'Entidads[nacional]',
                                        'div'=>['class'=>'form-group'],
                                        'options' => ['1'=>'Salvadoreña', '0'=>'Extranjera'],
                                        'class'=>'form-control select-control',
                                        'empty' => 'Origen',
                                        'required'
                                    ]);*/?>
                                </div -->
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <span class="tittle-form">Persona Contacto</span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <table class="table-contacto">
                        <tr>
                            <td>
                                <p>1. Nombres</p>
                                <div class="col-sm-6 lbl-required"><span class="pull-right">Obligatorio</span></div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6 control-block">
                                    <input type="text" name="Contactos[nombres]"  class="form-control" placeholder="Ingresar el nombre de la persona de contacto" required="required" id="nombres-contacto">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>2. Apellidos</p>
                                <div class="col-sm-6 lbl-required"><span class="pull-right">Obligatorio</span></div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6 control-block">
                                    <input type="text" name="Contactos[apellidos]"  class="form-control" placeholder="Ingresar el apellido de la persona de contacto" required="required" id="apellidos-contacto">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>3. Correo Electrónico y Nacionalidad</p>
                                <div class="col-sm-6 lbl-required"><span class="pull-right">Obligatorio</span></div>
                                <div class="clearfix"></div>
                                <div class="col-sm-3 control-block ">
                                    <input type="text" name="Contactos[email]"  class="form-control is-unique" placeholder="Ingresar el correo electrónico de la persona de contacto" required="required" id="correo-contacto" data-modelo="Contactos" data-campo="email" data-label="correo electrónico de la persona">
                                </div>
                                <!--div class="col-sm-3 control-block">
                                    <input type="text" name="Contactos[nacional]"  class="form-control" placeholder="Ingresar la nacionalidad de la persona" required="required" id="nacional-contacto">
                                </div-->
                                <div class="col-sm-2 control-block">
                                    <?=$this->Form->control('nacional2',[
                                        'label'=>false,
                                        'name' => 'Contactos[nacional2]',
                                        'div'=>['class'=>'form-group'],
                                        'options' => ['1'=>'Salvadoreño', '0'=>'Extranjero'],
                                        'class'=>'form-control select-control',
                                        'empty' => 'Origen',
                                        'id'=>'nacional2',
                                        'required'
                                    ]);?>
                                </div>
                                <div class="col-sm-6 description-block">
                                    <span>Debe ser una cuenta de correo de correo electrónico válida. Se solicitará responder correo de verificación para completar el proceso de registro.</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>4. Tipo de Contacto y Documento de Identidad</p>
                                <div class="col-sm-6 lbl-required"></div>
                                <div class="clearfix"></div>
                                <div class="col-sm-2 control-block">
                                    <?=$this->Form->control('ccontactotipoid_contacto',[
                                        'label'=>false,
                                        'name' => 'Contactos[ccontactotipo_id]',
                                        'div'=>['class'=>'form-group'],
                                        'options' => $ccontactotipos,
                                        'class'=>'form-control select-control',
                                        'empty' => 'Tipo de contacto',
                                        'required'
                                    ]);?>
                                </div>
                                <!-- div class="col-sm-2 control-block">
                                    <?php /*$this->Form->control('cdocidtipoid_contacto',[
                                        'label'=>false,
                                        'name' => 'Contactos[cdocidtipo_id]',
                                        'div'=>['class'=>'form-group'],
                                        'options' => $cdocidtipospersona,
                                        'class'=>'form-control select-control',
                                        'empty' => 'Tipo de documento',
                                        'required'
                                    ]);*/?>
                                </div -->

                                <div class="col-sm-2 control-block">
                                    <input type="text" name="Contactos[docid]"  class="form-control " placeholder="Ingresar DUI o Pasaporte" required="required" id="docid-contacto"  <?php if($tipo_registro == 2) { ?> value="<?= $docid_persona ?>" readonly <?php } else { ?> disabled <?php } ?> >
                                </div>
                                <!--div class="col-sm-6 description-block">
                                    <span>Personas nacidas en El Salvador seleccionar DUI. Extranjeros proporcionar Pasaporte.</span>
                                </div-->
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
        // Al seleccionar una opcion del tipo de documento
        if($('#tipo-registro').val()==2)
        {
            deshabilitar();
        }

        $("#cdocidtipoid-contacto").change(function (e) {
            var id = $(this).val();
            var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'getTipoDocumento'], true) .'/' ?>';
            var elemento = $("#docid-contacto");
            var tipo_registro = $('#tipo-registro').val();

            if(tipo_registro !== '2') {
                getMascaraDocumento(id, url, elemento, false);
            }
        });

        $(document).on('change', '#docid-org', function(event) {
            var tipo_registro = $('#tipo-registro').val();
            if(tipo_registro !== '1') {

                var modelo = 'Entidads';
                var docid = $(this).val();
                var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'getentidadcontacto'], true) . '/' ?>';

                $.ajax({
                    url: url,
                    type: 'post',
                    data: {modelo: modelo, docid: docid,nac: $("#nacional").val()},
                    dataType: 'json',
                    cache: false,
                    async: false,
                    success: function (resp) {
                        if (resp['data'] === 1) {
                            if (resp['registro'].cestado_id != resp['verificacion']) {
                                $('#existe-entidad').val(1);

                                $('#nombre-org').val(resp['registro'].nombre);
                                $("#nombre-org").prop("readOnly", true);
                                $('#nombrelargo-org').val(resp['registro'].nombrelargo);
                                $("#nombrelargo-org").prop("readOnly", true);
                                $('#centidadtipo-id').val(resp['registro'].centidadtipo_id);
                                $("#centidadtipo-id").prop("disabled", true);
                                $('#centidadrol-id').val(resp['registro'].centidadrol_id);
                                $("#centidadrol-id").prop("disabled", true);

                                /* if(resp['registro'].nacional) {
                                 $('#nacional').val(1);
                                 } else {
                                 $('#nacional').val(0);
                                 }*/
                                $("#nacional").prop("disabled", true);
                            } else {
                                $("#nacional").prop("disabled", false);
                                $("#docid-org").val('');
    
                                limpiar();
                                $(".alert-danger .message").text("El número de documento ingresado pertenece a una organización que se encuentra en espera de verificación.");
                                $(".alert-danger").slideDown();
                                setTimeout(function () {
                                    $(".alert-danger").slideUp();
                                }, 5000);
                            }
                        } else if(tipo_registro !== '2'){

                            $('#existe-entidad').val('');
                            $('#nombre-org').val('');
                            $('#nombrelargo-org').val('');
                            $('#centidadtipo-id').val('');
                            $('#centidadrol-id').val('');
                            // $('#nacional').val('');

                            $("#nombre-org").prop("readOnly", false);
                            $("#nombrelargo-org").prop("readOnly", false);
                            $("#centidadtipo-id").prop("disabled", false);
                            $("#centidadrol-id").prop("disabled", false);
                            $("#nacional").prop("disabled", false);
                        }
                        else{
                            $("#nacional").prop("disabled", false);

                            limpiar();
                            $(".alert-danger .message").text("El número de documento ingresado no coincide con ninguna organización registrada.");
                            $(".alert-danger").slideDown();
                            setTimeout(function () {
                                $(".alert-danger").slideUp();
                            }, 5000);

                        }
                    }
                });
            }
        });


        $("#nacional").change(function (e) {
            var id = $(this).val();
            if(id!=0) {
                var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'enttipodocument'], true);?>';
                var elemento = $("#docid-org");
                if ($("#docid-org").val() !== '') {


                    if (isNaN($("#docid-org").val())) {
                        $(".alert-danger .message").text("El formato del NIT es incorrecto.");
                        $(".alert-danger").slideDown();
                        setTimeout(function () {
                            $(".alert-danger").slideUp();
                        }, 5000);

                        validardocumento(id, url, elemento);
                    }

                    else {
                        var leng = lengthdocumento(id, 'Entidads');
                        if (($("#docid-org").val()).length == leng) {
                            validardocumento(id, url, elemento);
                            $("#docid-org").attr("disabled", false);
                            $("#docid-org").attr("readonly", false);
                        }
                        else if ((($("#docid-org").val()).length < leng) || (($("#docid-org").val()).length > leng) ){
                            validardocumento(id, url, elemento);
                            $("#docid-org").val("");
                            $("#docid-org").attr("disabled", false);
                            $("#docid-org").attr("readonly", false);
                            $(".alert-danger .message").text("El NIT es incorrecto.");
                            $(".alert-danger").slideDown();
                            setTimeout(function () {
                                $(".alert-danger").slideUp();
                            }, 5000);
                        }
                    }
                }
                else
                {
                    validardocumento(id, url, elemento);
                    $("#docid-org").attr("disabled", false);
                    $("#docid-org").attr("readonly", false);
                }
            }
            else
            {
                $("#docid-org").unmask();
                if($("#docid-org").val()!="")
                {
                    var string = $("#docid-org").val().split("-").join("");
                    $("#docid-org").val(string);
                    $("#docid-org").attr("placeholder", "Documento");
                    $("#docid-org").attr("disabled", false);
                    $("#docid-org").attr("readonly", false);
                }
                else
                {
                    $("#docid-org").attr("disabled", false);
                    $("#docid-org").attr("readonly", false);
                    $("#docid-org").attr("placeholder", "Documento");
                    $("#docid-org").attr("disabled", false);
                }



            }
        });

        $("#docid-org").blur(function() {
            var tipo_registro = $('#tipo-registro').val();
            if(tipo_registro !== '2') {
            var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'isUnique'], true) . '/' ?>';
                var docid = $(this).val().trim();
            $.ajax({
                url: url,
                type: 'post',
                data: {valor: docid, modelo: 'Entidads', campo: 'docid', label: 'NIT o número de Documento de la Organización'},
                dataType: 'json',
                cache: false,
                async: false,
                success: function (resp) {
                    if (resp['error'] === 1) {

                        $("#docid-org").val("");
                        $(".alert-danger .message").text(resp["msj"]);
                        $(".alert-danger").slideDown();
                        setTimeout(function () {
                            $(".alert-danger").slideUp();
                        }, 5000);

                        // $('html, body').animate({scrollTop: 0}, 1500);

                    }
                }
            });
        }
        });

        $("#nacional2").change(function (e) {
            var id = $(this).val();

                var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'TipoDocumentolcount'], true) .'/' ?>';
                var elemento = $("#docid-contacto");
                var tipo_registro = $('#tipo-registro').val();


            if(id!=0) {
                //    if(tipo_registro !== '2') {
                  if($("#docid-contacto").val()!=='') {

                if (isNaN($("#docid-contacto").val().replace('-',''))) {
                    $(".alert-danger .message").text("El formato del DUI es incorrecto.");
                    $(".alert-danger").slideDown();
                    setTimeout(function () {
                        $(".alert-danger").slideUp();
                    }, 5000);

                    validardocumento(id, url, elemento);
                    $('#docid-contacto').attr("placeholder", "DUI");
                    // $('html, body').animate({scrollTop: 0}, 1500);
                }
                /* getMascaraDocumento(id, url, elemento, false);
                 $('#docid-contacto').attr( "placeholder", "DUI");*/
                //  }

                else {
                    var leng = lengthdocumento(id, 'Contactos');
                    if (($("#docid-contacto").val().replace('-','')).length == leng) {
                        validardocumento(id, url, elemento);
                        $("#docid-contacto").attr("disabled", false);
                        $("#docid-contacto").attr("readonly", false);
                    }
                    else if ((($("#docid-contacto").val().replace('-','')).length < leng) || (($("#docid-contacto").val().replace('-','')).length > leng)) {
                        validardocumento(id, url, elemento);
                        $("#docid-contacto").val("");
                        $("#docid-contacto").attr("disabled", false);
                        $("#docid-contacto").attr("readonly", false);
                        $('#docid-contacto').attr("placeholder", "DUI");
                        $(".alert-danger .message").text("El DUI es incorrecto.");
                        // $('html, body').animate({scrollTop: 0}, 1500);
                        $(".alert-danger").slideDown();
                        setTimeout(function () {
                            $(".alert-danger").slideUp();
                        }, 5000);
                    }
                }
            }
            else{

                      validardocumento(id, url, elemento);
                      $("#docid-contacto").attr("disabled", false);
                      $("#docid-contacto").attr("readonly", false);
                      $('#docid-contacto').attr("placeholder", "DUI");
                  }
            }
            else
            {
                $('#docid-contacto').unmask({placeholder:" "});

                if($("#docid-contacto").val()=='')
                {
                    $('#docid-contacto').attr( "placeholder","Pasaporte" );
                    $("#docid-contacto").attr("disabled", false);
                    $("#docid-contacto").attr("readonly", false);

                }
                else {
                    $.ajax({
                        url: url,
                        type: 'post',
                        data: {id: id},
                        dataType: 'json',
                        cache: false,
                        async: false,
                        success: function (resp) {
                            if (resp.error == '0') {

                                $('#docid-contacto').unmask();
                                   $('#docid-contacto').attr( "placeholder","Pasaporte" );
                                if (resp.data.mascara != '') {
                                    $('#docid-contacto').val("");
                                    $('#docid-contacto').mask(resp.data.mascara);
                                }
                                else {
                                    $('#docid-contacto').unmask();
                                    $('#docid-contacto').val($("#docid-contacto").val().replace('-',''));
                                    $("#docid-contacto").attr("disabled", false);
                                    $("#docid-contacto").attr("readonly", false);
                                }
                            }
                        }
                    });
                }
            }
        });

        $("#docid-contacto").blur(function() {
            var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'isUnique'], true) .'/' ?>';
            var docid = $( this ).val().trim();
            $.ajax({
                url: url,
                type: 'post',
                data: {valor:docid, modelo:'Contactos', campo:'docid', label:'Documento de Identidad'},
                dataType: 'json',
                cache:false,
                async:false,
                success:function (resp) {
                    if(resp['error']===1){

                        $("#docid-contacto").val("");
                        $(".alert-danger .message").text(resp["msj"]);
                        $(".alert-danger").slideDown();
                        setTimeout(function () {
                            $(".alert-danger").slideUp();
                        }, 5000);
                        // $('html, body').animate( {scrollTop : 0}, 1500 );
                    }
                }
            });
        });

        $('#correo-contacto').change(function() {
            // Expresion regular para validar el correo
            if($("#correo-contacto").val().indexOf('@', 0) == -1 || $("#correo-contacto").val().indexOf('.', 0) == -1) {
                $(".alert-danger .message").text("Correo electrónico inválido.");
                $(".alert-danger").slideDown();
                setTimeout(function () {
                    $(".alert-danger").slideUp();
                }, 5000);
                $("#correo-contacto").val('');
            }
        });

        $(document).on('change', '.is-unique', function(e) {
            var tipo_registro = $('#tipo-registro').val();
            var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'isUnique'], true) ?>';
            var elemento = $( this );
            var id = elemento.attr('id');
            var modelo = elemento.attr('data-modelo');
            var campo = elemento.attr('data-campo');
            var label = elemento.attr('data-label');
            var valor = elemento.val();

            $.ajax({
                url: url,
                type: 'post',
                data: {valor:valor, modelo:modelo, campo:campo, label:label},
                dataType: 'json',
                cache:false,
                async:false,
                success:function (resp) {
                    if(resp['error']===1){
                        e.preventDefault();
                        $( "#" + id ).val("");
                        $(".message").text(resp["msj"]);
                        // $('html, body').animate( {scrollTop : 0}, 1500 );
                            $("#alert").slideDown()


                        setTimeout(function () {
                            $("#alert").slideUp();
                        }, 5000);
                    }
                }
            });

        });


        $(document).on('keyup', '#docid-org', function(event)
        {
            if($("#nacional").val()==0)
            {
                this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')
            }
        });

        $(document).on('keyup', '#docid-contacto', function(event)
        {
            if($("#nacional2").val()==0)
            {
                this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
            }
        });

        $(document).on('click', '.btn-registro', function(e) {
            e.preventDefault();

            var id = 0;
            var nombre = $("#nombre").val();
            var band = true;

            var campos = {
                0: {
                    'campo': 'nacional', 'label': 'origen', 'tipo': 'select'
                },
                1: {
                    'campo': 'docid-org', 'label': 'documento de identidad de la organización', 'tipo': 'input'
                },
                2: {
                    'campo': 'nombre-org', 'label': 'nombre de la organización', 'tipo': 'input'
                },
                3: {
                    'campo': 'nombrelargo-org', 'label': 'nombre completo de la organización', 'tipo': 'input'
                },
                4: {
                    'campo': 'centidadtipo-id', 'label': 'tipo de organización', 'tipo': 'select'
                },
                5: {
                    'campo': 'centidadrol-id', 'label': 'rol de la organización', 'tipo': 'select'
                },
              /*  6: {
                    'campo': 'nacional', 'label': 'origen', 'tipo': 'select'
                },*/
                6: {
                    'campo': 'nombres-contacto', 'label': 'nombres del contacto', 'tipo': 'input'
                },
                7: {
                    'campo': 'apellidos-contacto', 'label': 'apellidos del contacto', 'tipo': 'input'
                },
                8: {
                    'campo': 'correo-contacto', 'label': 'correo electrónico del contacto', 'tipo': 'input'
                },
              9: {
                    'campo': 'nacional2', 'label': 'nacionalidad del contacto', 'tipo': 'select'
                },
                 10: {
                    'campo': 'ccontactotipoid-contacto', 'label': 'tipo de contacto', 'tipo': 'select'
                },
                11: {
                    'campo': 'docid-contacto', 'label': 'documento de identidad del contacto', 'tipo': 'input'
                }
            };

            if($('#centidadtipo-id').is(':disabled')) {
                $( "#centidadtipo-id" ).prop( "disabled", false );
            }

            if($('#docid-contacto').is(':disabled')) {
                $( "#docid-contacto" ).prop( "disabled", false );
            }
            if($('#docid-org').is(':disabled')) {
                $( "#docid-org" ).prop( "disabled", false );
            }

            if($('#centidadrol-id').is(':disabled')) {
                $( "#centidadrol-id" ).prop( "disabled", false );
            }

            if($('#nacional').is(':disabled')) {
                $( "#nacional" ).prop( "disabled", false );
            }


            $.each(campos, function(item, col){
                band = valRequired(col['campo'], col['label'], col['tipo']);

                if(band === false){
                    e.preventDefault();
                    return false;
                }
            });


if(band !== false) {
    // Expresion regular para validar el correo
    if($("#correo-contacto").val().indexOf('@', 0) == -1 || $("#correo-contacto").val().indexOf('.', 0) == -1) {
        band = false;

        $(".alert-danger .message").text("Correo electrónico inválido.");
        $(".alert-danger").slideDown();
        setTimeout(function () {
            $(".alert-danger").slideUp();
        }, 5000);
        $("#correo-contacto").val('');
    }
    $(".is-unique").each(function () {
        var url = '<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'isUnique'], true) ?>';
        var id = this.id;
        var elemento = $('#' + id);
        var modelo = elemento.attr('data-modelo');
        var existe = (modelo === 'Entidads') ? $('#existe-entidad').val() : $('#existe-contacto').val();
        var campo = elemento.attr('data-campo');
        var label = elemento.attr('data-label');
        var valor = elemento.val();

        if (existe === '') {
            $.ajax({
                url: url,
                type: 'post',
                data: {valor: valor, modelo: modelo, campo: campo, label: label},
                dataType: 'json',
                cache: false,
                async: false,
                success: function (resp) {
                    if (resp['error'] === 1) {
                        elemento.val("");
                        band = false;

                        $(".message").text(resp["msj"]);
                        $("#alert").slideDown();

                        setTimeout(function () {
                            $("#alert").slideUp();
                        }, 5000);

                        return false;
                    }
                }
            });
        }

        if (band === false) {
            e.preventDefault();
            return false;
        }
    });
}
            if(band !== false) {

                $('.btn-registro').prop( "disabled", true);
                $('#formulario').submit();
            } else {
                e.preventDefault();
                return false;
            }
        });
    });

    function validardocumento(id, url, elemento)
    {
        $.ajax({
            url: url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            cache:false,
            async:false,
            success:function (resp) {
                if(resp.error == '0') {
                    elemento.attr("disabled", false);
                    elemento.attr("readonly", false);
                    elemento.attr( "placeholder", "NIT");
                    elemento.mask(resp.data.mascara);
                }
            }
        });
    }

    function lengthdocumento(id, enty)
    {
        var url2 = "<?= \Cake\Routing\Router::url(['controller' => 'entidadcontactos', 'action' => 'getCountDocument'], true);?>";
        var cont=0;
        $.ajax({
            url: url2,
            type: 'POST',
            data: {id: id,mod:enty},
            dataType: 'json',
            cache:false,
            async:false,
            success:function (resp) {
                if(resp.error == '0') {
                    cont=resp.data;
                }
            }
        });
        return cont;
    }



    function deshabilitar()
    {
       /// $('#nombre-org').val(resp['registr);
        $("#nombre-org").prop("readOnly", true);
       /// $('#nombrelargo-org').val(resp['registro'].nombrelargo);
        $("#nombrelargo-org").prop("readOnly", true);
      //  $('#centidadtipo-id').val(resp['registro'].centidadtipo_id);
        $("#centidadtipo-id").prop("disabled", true);
      //  $('#centidadrol-id').val(resp['registro'].centidadrol_id);
        $("#centidadrol-id").prop("disabled", true);
    }
    function limpiar()
    {
         $('#nombre-org').val("");
         $('#nombrelargo-org').val("");
         $('#centidadtipo-id').val("");
         $('#centidadrol-id').val("");

    }
</script>
