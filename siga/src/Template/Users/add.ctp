<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cestado[]|\Cake\Collection\CollectionInterface $cestados
 */
$real_url = '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').'/'.strtolower($this->request->getParam("controller")).'/';
echo $this->Html->script(['lib/jquery-2.2.4.min.js', 'lib/bootstrap.min.js', 'funciones/validaciones.js']);
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
    <?= $this->Form->create($user,['novalidate','id'=>"frm"])?>
        <div class="row">
            <div class="col-md-6">
                <h2 class="tittle">Crear <?php  echo (isset($titulo[0]['alias']))?$titulo[0]['alias']:""; ?></h2>
            </div>
            <div class="col-md-6 panel-action">
                <button class="btn btn-sistem btn-save"><span>Guardar</span><i class="fa fa-floppy-o icono"></i></button>
                <?php foreach ($controltools as $btn){
                    if($btn['funcion']==="imprimir"){   ?>
                        <a class="btn btn-sistem <?=$btn['class']?>" onClick="window.open('<?= $real_url.$btn['funcion'] ?>/','imp','height=500,width=1000,menubar=1,resizable=1,scrollbars=1');"><span>Imprimir</span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                }else{?>
                        <a class="btn btn-sistem <?=$btn['class']?>" href="<?=$real_url.$btn['funcion']?>"><span><?=$btn['alias']?></span><i class="fa <?=$btn['icon']?> icono"></i></a>
                    <?php                   }
                }           ?>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12 content-form">
                <div class="col-md-12">
                    <div class="alert alert-danger no-display" id="alert">

                        <span class="message"></span>
                        <button type="button" class="close" data-dismiss="alert"></button>
                    </div>
                </div>
                <div class="col-md-2 frm-label">Perfil</div>
                <div class="col-md-2">
                    <?=$this->Form->control('perfil_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => $perfils ,
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar',
                        'rows'=>3,
                        'required',
                    ]);?>
                </div>
                <div class="clearfix col-md-12" style="margin: 4px;"></div>
                <div class="col-md-2 frm-label">Contacto</div>
                <div class="col-md-10">

                    <?=$this->Form->control('contacto_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => $contactos,
                        'class'=>'form-control select-control',
                        'empty' => 'Seleccionar',
                        'required',
                        'onChange'=>'ValidContact()'
                    ]);?>
                </div>
                <div class="clearfix col-md-12" style="margin: 4px;"></div>
                <div class="col-md-2 frm-label">Usuario</div>
                <div class="col-md-10">
                    <?= $this->Form->control('username',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'class'=>'form-control ',
                        'placeholder'=>'Usuario',
                        'onchange'=>'ValUniqueType(this.id)',
                        'required'
                    ]);?>
                </div>

                <div class="col-md-2 frm-label">Email</div>
                <div class="col-md-10">
                    <?= $this->Form->control('email',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'style'=>'max-height: 25px;padding-top: 5px;font-size: 12px; color: #929292;',
                        'class'=>'form-control ',
                        'placeholder'=>'correo electrónico',
                        'required'

                    ]);?>
                </div>
                <div class="clearfix col-md-12" style="margin: 4px;"></div>
                <div class="col-md-2 frm-label">Contraseña</div>
                <div class="col-md-10">
                    <?= $this->Form->control('password',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'style'=>'max-height: 25px;padding-top: 5px;font-size: 12px; color: #929292;',
                        'class'=>'form-control ',
                        'placeholder'=>'Contraseña',
                        'required'
                    ]);?>
                </div>
                <div class="clearfix col-md-12" style="margin: 4px;"></div>
                <div class="col-md-2 frm-label">Confirmar contaseña</div>
                <div class="col-md-10">
                    <?= $this->Form->control('password',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'style'=>'max-height: 25px;padding-top: 5px;font-size: 12px; color: #929292;',
                        'class'=>'form-control ',
                        'placeholder'=>'Contraseña',
                        'id'=>'verificarclave',
                        'required'
                    ]);?>
                </div>
                <div class="clearfix col-md-12" style="margin: 4px;"></div>
                <div class="col-md-2 frm-label">Estado</div>
                <div class="col-md-2">
                    <?=$this->Form->control('cestado_id',[
                        'label'=>false,
                        'div'=>['class'=>'form-group'],
                        'options' => $cestados,
                        'class'=>'form-control select-control ',
                        'empty' => 'Seleccionar',
                        'rows'=>3,
                        'required'
                    ]);?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function(){
        $('#email').change(function() {
            // Expresion regular para validar el correo
            if($("#email").val().indexOf('@', 0) == -1 || $("#email").val().indexOf('.', 0) == -1) {
               $(".alert-danger .message").text("Correo electrónico inválido.");
                $(".alert-danger").slideDown();
                setTimeout(function () {
                    $(".alert-danger").slideUp();
                }, 4000);
                $("#email").val('');
               // return false;
            }
            else
            {
                ValUniqueType(this.id);
            }
        });


        $("#frm").submit(function(e){

            var id = 0;
            var nombre = $("#email").val();
            var url = getUrl()+"valunique";

            $.ajax({
                url: url,
                type: 'post',
                data: {campo:nombre,id:id,tipo: tipo},
                dataType: 'json',
                cache:false,
                async:false,
                success:function (resp) {
                  if(resp['error']===1){

                        $("#nombre").val("");
                        $(".message").text(resp["msj"]);
                        $("#alert").slideDown();
                        setTimeout(function () {
                            $("#alert").slideUp();
                        }, 4000);
                    }
                }
            });

            var campos = {
                5: {
                    'campo': 'perfil-id',
                    'label': 'perfil',
                    'tipo': 'select'
                },
                4: {
                    'campo': 'contacto-id',
                    'label': 'contacto',
                    'tipo': 'select'
                },

                3: {
                    'campo': 'username',
                    'label': 'nombre de usuario',
                    'tipo': 'input'
                }, 2: {
                    'campo': 'email',
                    'label': 'correo electrónico',
                    'tipo': 'input'
                },
                1: {
                    'campo': 'password',
                    'label': 'contraseña',
                    'tipo': 'input'
                },
                0: {
                    'campo': 'cestado-id',
                    'label': 'estado',
                    'tipo': 'select'
                }
            };

           // console.log(validarCamp(campos));
if(validarCamp(campos))
{
    var pass = $('#password').val();
    var pass2 = $('#verificarclave').val();
    var correo = $('#email').val();
    var lon = pass.length;

    if (lon < 8) {
        $(".alert-danger .message").text("La contrase\u00f1a debe de tener como minimo 8 caracteres.");
        $(".alert-danger").slideDown();
        setTimeout(function () {
            $(".alert-danger").slideUp();
        }, 4000);
        return false;
    }
    else if (pass != pass2) {

        $(".alert-danger .message").text("Las contrase\u00f1as no coinciden.");
        $(".alert-danger").slideDown();
        setTimeout(function () {
            $(".alert-danger").slideUp();
        }, 4000);
        return false;
    }
    else
    {
        console.log("Enviando...");
       // $("#frm").on("submit");
        return true;
    }

}
else
{
    e.preventDefault();
}
        });
    });
    function ValidContact(){
        var contacto = $("#contacto-id").val();
        var id = 0;
        var url = getUrl()+"validContact";
        if(contacto!=''){
            $.ajax({
                url: url,
                type: 'post',
                data: {contacto:contacto, id:id},
                cache: false,
                async: false,
                success: function (resp) {
                    if(parseInt(resp)==1){
                        $("#contacto-id").val("");
                        $("#alert .message").text("El contacto seleccionado ya fue asignado a otro usuario. Intente con un contacto diferente.");
                        $("#alert").slideDown();
                        setTimeout(function () {
                            $("#alert").slideUp();
                        }, 4000);
                    }
                }
            });
        }
    }
    function validarCamp(campos)
    {
      var  valor=[];
      var val="";
        $.each(campos, function(item, col){
            var band = valRequired(col['campo'], col['label'], col['tipo']);
           if(band=== false){
               valor.push("0");
            }
            else if(band=== true)
            {
                valor.push("1");
            }
        });
       if(! valor.includes("0"))
       {
           val=1;
       }
        return val;
    }

</script>