var instance="";
var initNode="";
var obj_conecction=new Array();//array de objetos con sus respectivas conecciones
//var object=new Object();
jsPlumb.ready(function () {

    // setup some defaults for jsPlumb.
    instance = jsPlumb.getInstance({
        Endpoint: ["Dot", {radius: 2}],
        Connector:"StateMachine",
        HoverPaintStyle: {stroke: "#1e8151", strokeWidth: 2 },
        ConnectionOverlays: [
            [ "Arrow", {
                location: 1,
                id: "arrow",
                length: 14,
                foldback: 0.8
            } ],
            [ "Label", { label: "Transición", id: "label", cssClass: "aLabel" }]
        ],
        Container: "canvas"
    });

    instance.registerConnectionType("basic", { anchor:"Continuous", connector:"StateMachine" });

    window.jsp = instance;

    var canvas = document.getElementById("canvas");
    var windows = jsPlumb.getSelector(".statemachine-demo .w");
    var nombre_transicion_DB=null;

    //bind al clickear una conexión. muestra un modal con las acciones a realizar, sólo si la transición es diferente a una transición fin
    if(access){
        instance.bind("click", function (c) {
            var targetId=c.targetId.split(prefijo);
            if(targetId[1]!='fin'){
                toolsClick(c);
            }
         });
    }

    // bind a connection listener. note that the parameter passed to this function contains more than
    // just the new connection - see the documentation for a full list of what is included in 'info'.
    // this listener sets the connection's internal
    // id as the label overlay's text.
    instance.bind("connection", function (info) {
        var sourceId=info.connection.sourceId;
        var targetId=info.connection.targetId;
        var find=0;
        var index=0;
        var find_fin=false;
        var string=$("#"+sourceId).data('content')+"_"+$("#"+targetId).data('content');//nombre provisional de la transición
        var fin=$("#"+sourceId).data('fin');
        if(fin_action_modify) fin=fin_action_modify;
        if(nombre_transicion_DB!=null)string=nombre_transicion_DB;
        if(sourceId==prefijo+'inicio' && targetId==prefijo+'fin'){
            instance.deleteConnection(info.connection);//eliminando transición de inicio a fin
            bootbox.alert("El elemento Inicio no puede poseer una transición directa el elemento Fin.");
        }else{
            if(targetId==prefijo+'fin'){
                if(fin==true){
                    if(obj_conecction[sourceId]==undefined)obj_conecction[sourceId] = new Array();//si el objeto de conexión no se encuentra en el array, se genera su índice
                    if(obj_conecction[targetId]==undefined) obj_conecction[targetId]=new Array();//objeto de flecha de entrada
                    for(index=0; index<obj_conecction[targetId].length; index++){//se recorre el array y verificar si ya se ingreso dicha transición
                        if(obj_conecction[targetId][index].targetId==targetId) find_fin=true;
                    }
                    if(!find_fin){
                        obj_conecction[sourceId].push(info.connection);
                        obj_conecction[targetId].push(info.connection);
                        info.connection.getOverlay("label").setLabel(string);
                        nombre_transicion_DB=null;
                    }else{
                        instance.deleteConnection(info.connection);//eliminando transición como destino a etapa inicio
                        bootbox.alert("Sólo puede existir una transición al elemento Fin.");
                    }
                }else{
                    instance.deleteConnection(info.connection);//eliminando transición que no sea una etapa fin y que este enlazada al elemento fin
                    bootbox.alert("Sólo una etapa fin puede poseer una transición al elemento Fin.");
                }
            }else{
                if(targetId!=prefijo+'inicio'){
                    if(sourceId!=targetId){
                        if(obj_conecction[sourceId]==undefined)obj_conecction[sourceId] = new Array();//si el objeto de conexión no se encuentra en el array, se genera su índice
                        if(obj_conecction[targetId]==undefined) obj_conecction[targetId]=new Array();//objeto de flecha de entrada

                        for(index=0; index<obj_conecction[sourceId].length; index++){//se recorre el array y verificar si ya se ingreso dicha transición
                            if(obj_conecction[sourceId][index].targetId==targetId) find=1;
                        }

                        for(index=0; index<obj_conecction[targetId].length; index++){//se recorre el array y verificar si ya se ingreso dicha transición
                            if(obj_conecction[targetId][index].sourceId==sourceId) find=2;
                        }

                        if(find!=2){
                            info.connection.getOverlay("label").setLabel(string);//set de nombre default
                            if(nombre_transicion_DB==null){//si es null es porque es nueva transición sin alamcenar en DB
                                bootbox.prompt({
                                    title: "Ingresa el nombre de la transición: ",
                                    buttons: {
                                        confirm: {
                                            label: "Guardar",
                                            className: 'btn-sistem btn-save btn-save-i-custom',
                                            callback: function(){
                                            }
                                        },
                                        cancel: {
                                            label: "Cancelar",
                                            className: 'btn-sistem btn-exit float-right btn-exit-i-custom',
                                            callback: function(){
                                            }
                                        }
                                    },
                                    callback: function (result) {
                                        if(result!=null){//si da cancel ó da ok sin escribir nada se deja el nombre default
                                            if(result.trim().length>0) string=result.trim();//si ha escrito algún texto en el prompt
                                        }
                                        if(saveTransicion(sourceId, targetId,string)){
                                            obj_conecction[sourceId].push(info.connection);
                                            obj_conecction[targetId].push(info.connection);
                                            info.connection.getOverlay("label").setLabel(string);
                                        }else {
                                            instance.deleteConnection(info.connection);//eliminando transición si ha ocurrido error al insertar en la DB
                                        }
                                    }
                                });
                                $(".btn-save-i-custom").empty().append("<span style='vertical-align: middle;'>Guardar</span><i class='fa fa-floppy-o icono'></i>");
                                $(".btn-exit-i-custom").empty().append("<span style='vertical-align: middle;'>Cancelar</span><i class='fa fa-times icono'></i>");
                            }else{
                                obj_conecction[sourceId].push(info.connection);
                                obj_conecction[targetId].push(info.connection);
                                nombre_transicion_DB=null;
                            }
                        }
                        else{
                            instance.deleteConnection(info.connection);//eliminando transición repetida entre ambos objectos
                        }
                    }else{
                        instance.deleteConnection(info.connection);//eliminando transición hacia ella misma
                    }
                }else{
                    instance.deleteConnection(info.connection);//eliminando transición como destino a etapa inicio
                    bootbox.alert("El elemento Inicio no puede poseer una transición fin.");
                }
            }
        }
    });

    // bind a double click listener to "canvas"; add new node when this occurs.
    /*jsPlumb.on(canvas, "dblclick", function(e) {
        newNode(e.offsetX, e.offsetY);
    });*/

    //
    // initialise element as connection targets and source.
    //
    initNode = function(el) {
        var obj=el.id.split(prefijo);
        var fin=$("#"+el.id).data('fin');//data para conocer si la etapa es fin y setear sus máximas conexiones a 1
        // initialise draggable elements.
        if(obj[1]!='inicio' && obj[1]!='fin'){//evitar que se puedan mover en el lienzo objetos inicio y fin
            if(access) instance.draggable(el);//sólo si el estado del workflow y acción son permitidos para el movimiento de los bloques
        }

        instance.makeSource(el, {
            filter: ".ep",
            anchor: "Continuous",
            connectorStyle: { stroke: "#5c96bc", strokeWidth: 2, outlineStroke: "transparent", outlineWidth: 4 },
            connectionType:"basic",
            extract:{
                "action":"the-action"
            },
            maxConnections: (el.id==prefijo+'inicio' || fin)?1:5,
            onMaxConnections: function (info, e) {
                bootbox.alert("Conexiones máximas permitidas: " + info.maxConnections);
            }
        });

        instance.makeTarget(el, {
            dropOptions: { hoverClass: "dragHover" },
            anchor: "Continuous",
            allowLoopback: true
        });

        // this is not part of the core demo functionality; it is a means for the Toolkit edition's wrapped
        // version of this demo to find out about new nodes being added.
        //
        instance.fire("jsPlumbDemoNodeAdded", el);
    };

    if(wfetapas_id.length>0){
        for(var st=0; st<wfetapas_id.length; st++){
            var d = document.createElement("div");
            var id = prefijo+wfetapas_id[st];
            var div_transicion="";
            var div_delete="";
            var div_edit="";
            if(access){
                div_transicion="<div class='ep1' title='Crear transición'><i class='fa fa-arrow-circle-right ep' aria-hidden='true'></i></div>";
                div_delete="<div class='delete' title='Borrar' onclick=deleteBlock('"+id+"')><i class='fa fa-trash' aria-hidden='true'></i></div>";
                div_edit="<div class='edit' title='Editar' onclick=modifyState('"+id+"')><i class='fa fa-pencil' aria-hidden='true'></i></div>";
            }

            if(access) d.className="w cursor-move";
            else  d.className = "w";
            d.id = id;
            d.setAttribute('data-content', wfetapas_nombre[st]);
            d.setAttribute('data-fin', wfetapas_fin[st]);
            d.innerHTML = wfetapas_nombre[st] + div_delete + div_edit + div_transicion;
            d.style.left = positionleft[st] + "px";
            d.style.top = positiontop[st] + "px";
            d.style.backgroundColor=colorbkg[st];
            d.style.color=colortext[st];
            d.style.borderColor=colorbkg[st];
            instance.getContainer().appendChild(d);
            initNode(d);
        }
    }

    // suspend drawing and initialise.
    instance.batch(function () {
        for (var i = 0; i < windows.length; i++) {
            initNode(windows[i], true);
        }

        if(windows.length>0){
            if(source.length>0){
                for(var st=0; st<source.length; st++){
                    nombre_transicion_DB=nombre_transicion[st];
                    instance.connect({
                        source:source[st],
                        target:target[st],
                        type:"basic"
                    });
                }
            }
        }
    });
    jsPlumb.fire("jsPlumbDemoLoaded", instance);
});
