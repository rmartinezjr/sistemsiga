var prefijo_perfil="perfil_";
var prefijo_modelf="modelf_";
var prefijo_privm="privm_";
var prefijo_radioadming="name-adming-";
var prefijo_selectCustom="option_custom_";
var option_radioadming=0;//variable que almacena opción del radio de admin general
var option_selectcustom=0;//variable que almacena opción del select de personalización
var option_selectdetailcustom=0;//variable que almacena opción del select de detalle de personalización
var matriz_priv=new Array();
var posicion=0;
var access_edit_priv=1;//variable que permite si se habilita el cambio de privilegios

function seleccionTr(){
    for(var r=0; r<rows.length; r++) removeClass(rows[r], "tr-active");
    addClass(this, "tr-active");
}

function seleccionLi(){
    var id_element=this.id;
    var id_perfil=id_element.split(prefijo_perfil);
    var url = getUrl() + "index/" +id_perfil[1];
    window.location.assign(url);
}

function checkPrivilegios(){
    var find_check_red=0;
    var classNameEditPriv=document.getElementsByClassName('edit-privileges');
    for(r=0; r<editprivs.length; r++){
        if(classNameEditPriv[r].children[0].classList.contains('check-red')) 
            find_check_red=1;
    }
    if(find_check_red && this.children[0].classList.contains('fa-check-square-o')) access_edit_priv=0;
    else access_edit_priv=1;
    if(access_edit_priv){
        var padreTr=this.parentNode.parentNode;
        var childrenTd=padreTr.children;
        if(this.children[0].classList.contains('check-red')){
            var resp=changeprivilegios(JSON.stringify(matriz_priv));
            if(resp['resp']){
                removeClass(this.children[0],'fa-save check-red');
                addClass(this.children[0],'fa-check-square-o');
                for(var c=0; c<childrenTd.length; c++) {
                    if(childrenTd[c].children[0]!=undefined){
                        if(!childrenTd[c].children[0].classList.contains('edit-privileges')){
                            addClass(childrenTd[c].children[0], "not-allowed");
                        }
                    }
                }
                showAlert(resp['msj'],'success');
                if(resp['reload']){
                    window.location.reload(true);
                }
            }else{
                showAlert(resp['msj'],'danger');
            }
        }else{
            access_edit_priv=0;
            removeClass(this.children[0],'fa-check-square-o');
            addClass(this.children[0],'fa-save check-red');
            for(var c=0; c<childrenTd.length; c++) {
                if(childrenTd[c].children[0]!=undefined) removeClass(childrenTd[c].children[0], "not-allowed");
            }
        }
    }else{
        showAlert('Sólo se pueden cambiar los privilegios de la fila que ha seleccionado.','danger');
    }
}

function changePriv(){
    var padreTr=this.parentNode.parentNode;
    var childrenTd=padreTr.children;
    var iTd=childrenTd[childrenTd.length-1].children[0].children[0];
    if(!this.classList.contains('not-allowed') && iTd.classList.contains('check-red')){
        var valor=0;
        var id=this.id;
        var id_modelf=id.split(prefijo_modelf);
        if(this.classList.contains('no-permitido')) valor=1;
        verifyMatrizPriv(id_modelf[1],valor);
        if(valor){
            removeClass(this,'no-permitido');
            addClass(this,'permitido');
        }else{
            removeClass(this,'permitido');
            addClass(this,'no-permitido');
        }
    }
}

function verifyMatrizPriv(id_modelf, valor){
    var posicion_aux=-1;
    if(matriz_priv.length>0){
        for(var m=0; m<matriz_priv.length; m++){
            if(matriz_priv[m][0]==id_modelf){
                posicion_aux=m;
            }
        }
        if(posicion_aux==-1){
            posicion++;
            llenarMatrizPriv(id_modelf, valor, posicion);
        }else{
            matriz_priv[posicion_aux][1]=valor;
        }    
    }else{
        llenarMatrizPriv(id_modelf, valor, posicion);    
    }
}

function llenarMatrizPriv(id_modelf, valor, posicion){
    matriz_priv[posicion]=new Array();
    matriz_priv[posicion][0]=id_modelf;
    matriz_priv[posicion][1]=valor;
}

function viewPrivilegios(){
    var id=this.id;
    var id_privm=id.split(prefijo_privm);
    var url=getUrl()+"view/"+id_privm[1]+"/"+perfil_id;
    window.location.assign(url);

}

function showSection(){
    var c=0;
    if(this.id=="recursos"){
        var classNameDatos=document.getElementsByClassName('seccion-datos');
        for(c=0; c<classNameDatos.length; c++) addClass(classNameDatos[c],'display-none');
        var classNamePriv=document.getElementsByClassName('seccion-priv');
        for(c=0; c<classNamePriv.length; c++) removeClass(classNamePriv[c],'display-none');
        removeClass(document.getElementById('datos'),'activo');
        addClass(document.getElementById('recursos'),'activo');
    }
    else{
        var classNamePriv=document.getElementsByClassName('seccion-priv');
        for(c=0; c<classNamePriv.length; c++) addClass(classNamePriv[c],'display-none');
        var classNameDatos=document.getElementsByClassName('seccion-datos');
        for(c=0; c<classNameDatos.length; c++) removeClass(classNameDatos[c],'display-none');
        removeClass(document.getElementById('recursos'),'activo');
        addClass(document.getElementById('datos'),'activo');
    }
}

function optionRadioAdminG(){
    ocultaDivDetalle();
    option_radioadming=this.value;
    var select=document.getElementsByClassName('select-control');
    for(var s=0; s<select.length; s++) addClass(select[s],'display-none');
    removeClass(document.getElementsByName(prefijo_selectCustom+this.value)[0],'display-none');
}

function optionSelectCustom(){
    ocultaDivDetalle();
    var option=this.value;
    var resp=false;
    if(option!=""){
        option_selectcustom=option;
        if(this.options[this.selectedIndex].text=="Personalizado"){
            resp=getopcionesdetallepersonalizacion(option_radioadming, option_selectcustom);
            if(resp['resp']){
                var padre=document.getElementsByClassName('div-detalle-personalizado');
                while(padre[0].children[1].children[0].firstChild) padre[0].children[1].children[0].removeChild(padre[0].children[1].children[0].firstChild);
                for(var op=0; op<resp['data'].length; op++){
                    var div=document.createElement('DIV');
                    div.className="col-md-2 col-xs-2 col-dato p-l-16";
                    div.style="background-color: #E7fcb7; padding-bottom: 2px;";
                    var radio=document.createElement('INPUT');
                    radio.type="radio";
                    radio.name="name_detalle_per";
                    radio.className="radio_detalle_per";
                    radio.value=resp['data'][op]['id'];
                    radio.id="name-detalle-per-"+resp['data'][op]['id'];
                    div.appendChild(radio);

                    var div2=document.createElement('DIV');
                    div2.className="col-md-10 col-xs-10 col-dato";
                    var span=document.createElement('SPAN');
                    var text=document.createTextNode(resp['data'][op]['nombre']);
                    span.appendChild(text);
                    div2.appendChild(span);
                    padre[0].children[1].children[0].appendChild(div);
                    padre[0].children[1].children[0].appendChild(div2);
                }
                var nameRadioCustom=document.getElementsByClassName('radio_detalle_per');
                for(r=0; r<nameRadioCustom.length; r++) {
                    nameRadioCustom[r].addEventListener('click',optionDetailCustom,false);
                }
                removeClass(padre[0], 'display-none');
            }else{
                showAlert(resp['msj'],'danger');
            }
        }
        else{
            resp=savepersonalizacion(option_radioadming, option_selectcustom);
            if(resp['resp']){
                showAlert(resp['msj'],'success');
                addClass(this, 'display-none');
            }else{
                showAlert(resp['msj'],'danger');
            }
        }
    }
}

function ocultaDivDetalle(){
    var div_detalle=document.getElementsByClassName('div-detalle-personalizado');
    if(!div_detalle[0].classList.contains('display-none')) addClass(div_detalle[0],'display-none');
}

function optionDetailCustom(){
    option_selectdetailcustom=this.value;
    var resp=savedetallepersonalizacion(option_radioadming, option_selectcustom, option_selectdetailcustom);
    if(resp['resp']){
        var div_detalle=document.getElementsByClassName('div-detalle-personalizado');
        addClass(div_detalle[0],'display-none');
        var selects=document.getElementsByClassName('select-control');
        for(var sc=0; sc<selects.length; sc++){
            if(!selects[sc].classList.contains('display-none')) addClass(selects[sc],'display-none');
        }
        showAlert(resp['msj'],'success');
    }else{
        showAlert(resp['msj'],'danger');
    }
}

function closeDivDetail(){
    var div_detalle=document.getElementsByClassName('div-detalle-personalizado');
    addClass(div_detalle[0],'display-none');
}

function showAlert(message, tipo){
    document.getElementById('message-'+tipo).innerHTML=message;
    $("#alert-"+tipo).slideDown();
    setTimeout(function () {
        $("#alert-"+tipo).slideUp();
    }, 4000);
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

var tables = document.getElementsByClassName("table-privilegios");
var rows =tables[0].rows;
for(var r=0; r<rows.length; r++){
    rows[r].addEventListener('dblclick',seleccionTr,false);
}

var uls = document.getElementsByClassName("list-perfils");
var lis =uls[0].children;
for(r=0; r<lis.length; r++){
    lis[r].addEventListener('click',seleccionLi,false);
}

var editprivs = document.getElementsByClassName("edit-privileges");
for(r=0; r<editprivs.length; r++){
    editprivs[r].addEventListener('click',checkPrivilegios,false);
}

var viewprivs = document.getElementsByClassName("view-privileges");
for(r=0; r<viewprivs.length; r++){
    viewprivs[r].addEventListener('click',viewPrivilegios,false);
}

var privs_perm = document.getElementsByClassName("permitido");
for(r=0; r<privs_perm.length; r++){
    privs_perm[r].addEventListener('click',changePriv,false);
}

var privs_noperm = document.getElementsByClassName("no-permitido");
for(r=0; r<privs_noperm.length; r++){
    privs_noperm[r].addEventListener('click',changePriv,false);
}

var nameRadio=document.getElementsByClassName('radio_adming');
for(r=0; r<nameRadio.length; r++){
    nameRadio[r].addEventListener('click',optionRadioAdminG,false);
}

var select=document.getElementsByClassName('select-control');
for(r=0; r<select.length; r++) {
    select[r].addEventListener('change',optionSelectCustom,false);
}

var faclose=document.getElementsByClassName('fa-close');
for(r=0; r<faclose.length; r++) {
    faclose[r].addEventListener('click',closeDivDetail,false);
}
document.getElementById('recursos').addEventListener('click',showSection,false);
document.getElementById('datos').addEventListener('click',showSection,false);
