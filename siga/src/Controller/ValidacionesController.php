<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

class ValidacionesController extends AppController {
    //Función que verifica que el valor enviado por post sea v�lido para un add o edit
    /*Parametros:
        $valor: valor a identificar,
        $array: array con los valores aceptados,
        $session: nombre de la session a generar,
        $valor_session: valor de la session a crear
    */
    public function RegistrosIdValidos($valor, $array, $session, $valor_session){
        if(in_array($valor, $array)){
            return true;
        }else{
            $_SESSION[$session]=$valor_session;
            return false;
        }


    }

    //Función para identificar si existe el id en la tabla
    //$db -> para indicar que base de datos se conectara
    public function AccessRegisterView($model, $id, $action, $db=1){
        switch ($db){
            case  0:
                $dbconnect='dbpriv';
                break;
            case 1:
                $dbconnect='dbtransac';
                break;
            default:
                $dbconnect='dbtransac';
        }
        $model=strtolower($model);
        $sql = "SELECT count(id) as count FROM " .$model." WHERE id = ".$id;
        $conn = ConnectionManager::get($dbconnect);
        $inf = $conn->execute($sql);
        $resultado = $inf->fetchAll("assoc");
        $count=$resultado[0]['count'];
        if($count==0){//Registro que no esta en la tabla
            return false;
        }else{
            //para identificar si el registro esta en la tabla pero no fue eliminado trash =0
            $sql .= " and trash = 0";
            $inf = $conn->execute($sql);
            $resultado = $inf->fetchAll("assoc");
            $count=$resultado[0]['count'];
            if($count==0){
                return false;
            }else{
                return true;
            }
        }

    }

    //funcion que limpia los valores del getData
    public function TrimData($data){
        $array=array();
        foreach ($data as $campo => $value) {//recorriendo data para obtener los campos del modelo y valores ingresados por el usuario
            if(!is_array($value)) {
                $array[$campo]=trim($value);//creando un nuevo array con campos como indices y limpiando valores
            }
        }
        return $array;
    }

    public function AccessDataWf($model,$campo_id, $id=null,$modeloprincipal=null){
        $dbconnect='dbtransac';
        $conn = ConnectionManager::get($dbconnect);
        $model=strtolower($model);
        $modeloprincipal=strtolower($modeloprincipal);
        $sql = "SELECT count(m.id) as count FROM " .$model." m";
        if(!empty($modeloprincipal)){
            $sqlid="SELECT id from cestados WHERE nombre like 'Publicado'";
            $inf = $conn->execute($sqlid);
            $idestado = $inf->fetchAll("assoc");
            $sql.=" INNER JOIN ".$modeloprincipal." mp ON mp.id=m.".$campo_id;
            $sql.=" WHERE m.".$campo_id."= ".$id." AND mp.cestado_id=".$idestado[0]['id']." AND m.vinculo=1 AND m.trash=0 AND mp.trash=0";
        }else $sql.=" WHERE m.".$campo_id."= ".$id." AND m.vinculo=1 AND m.trash=0";
        $inf = $conn->execute($sql);
        $resultado = $inf->fetchAll("assoc");
        $count=$resultado[0]['count'];
        if($count==0){//Registro que no esta en la tabla
            return false;
        }else{
            return true;
        }
    }
}