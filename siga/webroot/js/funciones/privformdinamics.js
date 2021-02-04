var prefijo_privformdinamic="privformdinamic_";
function tiposFormDinamics(){
	$("#colecciontiposformdinamics").load(url_formdinamics + this.value, function (){});
}

function selectchk(){
	chk=this.value;
	var url=url_etapas;
	if(chk=='transiciones') url=url_transiciones;
	$("#privformdinamictable").load(url + formdinamic_id, function (){});
}

function addClass(element, classNameNew){
    var classNameActual=element.className;
    element.className=classNameActual+" "+classNameNew;
}

function removeClass(element, classNameRemove){
    var classNameActual=element.className;
    var classSplit=classNameActual.split(classNameRemove);
    element.className=classSplit[0];
}

function showAlert(message, tipo){
    document.getElementById('message-'+tipo).innerHTML=message;
    $("#alert-"+tipo).slideDown();
    setTimeout(function () {
        $("#alert-"+tipo).slideUp();
    }, 4000);
}

// Funcion que valida los campos requeridos
function valRequired(id, label, type){
    //EL AREGLO SE LLENA CON LOS ID DE LOS CAMPOS REQUERIDOS
    var campo = $("#"+id).val().trim();
    if(campo === '' || campo === 0){
        if(type === 'select') {
            showAlert("Debe seleccionar una opción del campo "+label+".",'add-perfil');
        } else {
            showAlert("El campo "+label+" debe ser completado.",'add-perfil');
        }
        return false;
    }
}

function addPerfil(){
    var pass=0;
    var campos = {
        0: {
            'campo': 'select-perfil',
            'label': 'perfiles',
            'tipo': 'select'
        }

    };
    $.each(campos, function(item, col){
        var band = valRequired(col['campo'], col['label'], col['tipo']);
        if(band === false){
            return false;
        }else pass++;
    });
    if(pass==1){
        var perfil_id=document.getElementById('select-perfil').value;
        var resp=ingresarPerfil(perfil_id, formdinamic_id);
        if(resp['resp']){
            closeModal();
            showAlert(resp['msj'],'success');
            window.location.reload(true);
        }else showAlert(resp['msj'],'add-perfil');
    }
}

function despliegueModal(){
    if(formdinamic_id!=0){
        $('#mdl_add_perfil').modal('show');
    }else bootbox.alert('Primero debe seleccionar un formulario dinámico.');             
}

function closeModal(){
    $('#mdl_add_perfil').modal('hide');
    clearModal();
}

function clearModal(){
    document.getElementById('select-perfil').selectedIndex=0;
} 

document.getElementById('tipoformdinamics').addEventListener('change',tiposFormDinamics, false);
document.getElementById('privformdinamicetapas').addEventListener('click',selectchk, false);
document.getElementById('privformdinamictransiciones').addEventListener('click',selectchk, false);
document.getElementById('btn-add-perfil').addEventListener('click',despliegueModal,false);
document.getElementById('cancelar-add-perfil').addEventListener('click',closeModal,false);
document.getElementById('add-perfil').addEventListener('click',addPerfil,false);
