var express = require("express");//framework de NodeJs
var session = require('express-session');//sessiones para almacenar notificaciones mostradas
var mysql = require("mysql");//para conexión a base de datos
var http = require("http");//protocolo
var socket = require("socket.io");//socket io para enviar y recibir data en tiempo real

var app = express();
var server = http.createServer(app);//creando servidor
var io = socket.listen(server);//creando io
var userId=0;
var username=null;
var id_notify=0;
var count_notifys=0;
var pool  = mysql.createPool({
    connectionLimit : 20,
    host            : 'localhost',
    user            : 'ftransac',
    password        : 'zVjd73@1',
    database        : 'datatransac'
});

//objeto a almacenar resultados
/*
 * obj.rows: true -> si hay notificación para mostrar
 * obj.content -> contenido de la notificación
 * obj.count: true -> si hay notificaciones sin ver para mostrar en bag
 * obj.count_notify -> cantidad de notificaciones sin ver
 * */
var obj= new Object();
function getNotifysUser(user_id, username, socket){
    pool.getConnection(function(err, connection) {
        // Use the connection
        if(err){
            console.log("Error de conexión a base de datos");
        }else{
            //query para notificaciones con enviado a 0
            var sql_query='SELECT * FROM vnotificaciones WHERE user_id='+user_id+' ORDER BY id ASC';
            //query para notificaciones con visto a 0
            connection.query(sql_query, function(err, rows) {
                if(err){
                    console.log(err);
                }else{
                    if(rows[0]!=undefined){//si contiene información a mostrar
                        obj.rows=true;
                        obj.content=rows[0];
                        var sql_query_update = "UPDATE notificacioncolas SET enviada = 1 WHERE user_id = "+user_id;
                        connection.query(sql_query_update, function (err, rows_update) {
                            if (err){
                                console.log(err);
                            }else{
                                notifys_visto(connection, obj, user_id, socket);
                                insert_table_enviada_logs(rows, connection,username);
                            }
                        });
                    }else{
                        obj.rows=false;
                        obj.content=null;
                        notifys_visto(connection, obj, user_id, socket);
                    }
                }
            });
            connection.release();
        }
    });
}

function notifys_visto(connection, obj, user_id, socket){
    var sql_query_vista='SELECT * FROM vnotificaciones_visto WHERE user_id='+user_id;
    connection.query(sql_query_vista, function(err, rows) {
        if(err){
            console.log(err);
        }else{
            count_notifys=0;
            rows.forEach(function(row) {
                count_notifys++;
            });
            if(rows[0]!=undefined){//si contiene información a mostrar
                obj.count=true;
                obj.count_notify=count_notifys;
            }else{
                obj.count=false;
                obj.count_notify=null;
            }
            var obj_json=JSON.stringify(obj);
            socket.emit('notifys',obj_json);
        }
    });

}

function insert_table_enviada_logs(rows, connection, username){
    rows.forEach(function(element){
        var sql_query_insert_enviada="INSERT INTO notificacionenviadalogs(notificacion_id,user_id,mensaje,created,usuario) VALUES("+
            element.id+","+element.user_id+",'"+element.mensaje+"',now(),'"+username+"')";
        connection.query(sql_query_insert_enviada, function(err, rows_insert_enviada) {
            if(err){
                console.log(err);
            }
        });
    });
}

function insert_table_visto_logs(id_notify, connection, username){
    var sql_query_insert_visto="INSERT INTO notificacionvistalogs(notificacion_id,user_id,mensaje,url,created,usuario) "+
        "SELECT "+id_notify+",nc.user_id,n.mensaje,n.url,now(),'"+username+"' FROM notificacions n INNER JOIN "+
        "notificacioncolas nc ON n.id=nc.notificacion_id where n.id="+id_notify;
    connection.query(sql_query_insert_visto, function(err, rows_insert_visto) {
        if(err){
            console.log(err);
        }
    });
}

server.listen(8080, function(){//creando servidor en el puerto 8080
    console.log("servidor enabled");
});

io.on('connection', function (socket) {
    //recibir id usuario emitido desde el layout
    socket.on('id_perfil', function (data) {
        userId=data.userId;
        username=data.username;
        if(userId!=0){
            getNotifysUser(userId, username, socket);
        }
    });

    //recibir id notificacion para actualizar campo visto al dar click en sus enlaces
    socket.on('set_notify_visto', function (data) {
        id_notify=data.id_notify;
        username=data.username;
        if(id_notify!=0){
            var sql_query_update = "UPDATE notificacioncolas SET visto = 1 WHERE id = "+id_notify;
            pool.getConnection(function(err, connection) {
                connection.query(sql_query_update, function (err, rows) {
                    if (err){
                        console.log(err);
                    }else{
                        insert_table_visto_logs(id_notify, connection, username);
                        socket.emit('redirect_notify',{res:true});
                        connection.release();
                    }
                });
            });
        }
    });
});
