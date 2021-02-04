<?php

namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;

class VerificacionPermisosController extends AppController {

    // Verifica las acciones que puede realizar el perfil del usuario logueado
    public function herramientasEdicion($modelo = null, $perfil_id = null) {
        $sql = "SELECT f.alias, f.funcion 
                FROM modelos m 
                  INNER JOIN modelofuncions mf ON m.id = mf.modelo_id 
                  INNER JOIN funcions f ON mf.funcion_id = f.id 
                  INNER JOIN privmodelos pm ON pm.modelofuncion_id = mf.id 
                WHERE f.edition = 1 AND f.trash = 0 AND mf.trash = 0 
                  AND m.trash = 0 AND m.modelo = '$modelo' 
                  AND pm.trash = 0 AND pm.allow = 1 AND pm.perfil_id = $perfil_id";

        $conn = ConnectionManager::get('dbpriv');
        $inf = $conn->execute($sql);
        $resultado = $inf->fetchAll("assoc");

        $data = [];
        foreach ($resultado as $key => $row) {
            $data[$row['funcion']] = $row['alias'];
        }

        return $data;

    }

    /**LOAD DE FUNCIONES DE CONTROL TOOL***/
    public function getControlTool($modelo = null, $ignore=null){
        $conn = ConnectionManager::get('dbpriv');
        $ignorar="";
        $con=1;
        foreach ($ignore as $item){
            if($con==1){
                $ignorar .= " funcion != '".$item."' ";
            }else{
                $ignorar .= " AND funcion != '".$item."' ";
            }
            $con++;
        }

        $user = $this->Auth->user();
        $perfil=$user['perfil_id'];

        if($ignorar != ''){
            $inf = $conn->execute("SELECT f.alias, f.funcion, f.class, f.icon FROM modelos m INNER JOIN modelofuncions mf on m.id = mf.modelo_id INNER JOIN funcions f on mf.funcion_id = f.id INNER JOIN privmodelos pm on pm.modelofuncion_id = mf.id WHERE f.controltool = 1 AND f.trash = 0 AND m.trash = 0 AND mf.trash = 0 AND pm.trash = 0 AND pm.allow = 1 AND pm.perfil_id = ".$perfil." AND m.modelo = '".$modelo."' AND ".$ignorar." ORDER BY mf.orden ASC");
        }else{
            $inf = $conn->execute("SELECT f.alias, f.funcion, f.class, f.icon FROM modelos m INNER JOIN modelofuncions mf on m.id = mf.modelo_id INNER JOIN funcions f on mf.funcion_id = f.id INNER JOIN privmodelos pm on pm.modelofuncion_id = mf.id WHERE f.controltool = 1 AND f.trash = 0 AND m.trash = 0 AND mf.trash = 0 AND pm.trash = 0 AND pm.allow = 1 AND pm.perfil_id = ".$perfil." AND m.modelo = '".$modelo."' ORDER BY mf.orden ASC");
        }

        $data = $inf->fetchAll("assoc");
        return $data;
    }

    public function getControlToolMultipleModels($modelo = null, $ignore=null, $modeloIndex = null){
        $conn = ConnectionManager::get('dbpriv');
        $ignorar="";
        $con=1;
        foreach ($ignore as $item){
            if($con==1){
                $ignorar .= " funcion != '".$item."' ";
            }else{
                $ignorar .= " AND funcion != '".$item."' ";
            }
            $con++;
        }

        $user = $this->Auth->user();
        $perfil=$user['perfil_id'];

        if($ignorar != ''){
            $inf = $conn->execute("SELECT f.alias, f.funcion, f.class, f.icon FROM modelos m INNER JOIN modelofuncions mf on m.id = mf.modelo_id INNER JOIN funcions f on mf.funcion_id = f.id INNER JOIN privmodelos pm on pm.modelofuncion_id = mf.id WHERE f.controltool = 1 AND f.trash = 0 AND m.trash = 0 AND mf.trash = 0 AND pm.trash = 0 AND pm.allow = 1 AND pm.perfil_id = ".$perfil." AND m.modelo = '".$modelo."' AND ".$ignorar." ORDER BY mf.orden ASC");
        }else{
            $inf = $conn->execute("SELECT f.alias, f.funcion, f.class, f.icon FROM modelos m INNER JOIN modelofuncions mf on m.id = mf.modelo_id INNER JOIN funcions f on mf.funcion_id = f.id INNER JOIN privmodelos pm on pm.modelofuncion_id = mf.id WHERE f.controltool = 1 AND f.trash = 0 AND m.trash = 0 AND mf.trash = 0 AND pm.trash = 0 AND pm.allow = 1 AND pm.perfil_id = ".$perfil." AND m.modelo = '".$modelo."' ORDER BY mf.orden ASC");
        }

        $data = $inf->fetchAll("assoc");

        foreach ($data as $key => $row) {
            if($row['funcion'] == 'index') {
                $data[$key]['modelo'] = $modeloIndex;
            } else {
                $data[$key]['modelo'] = $modelo;
            }
        }

        return $data;
    }

    //METODO PARA OBTENER NIVELES DE ACCESO
    public function getRuta($modelo = null,$funcion = null, $perfil=null, $modelocomplemento = null){
        $conn = ConnectionManager::get('dbpriv');

        if($funcion != null){
            $sql = $conn->execute("SELECT m.nombre as nomn1, m.alias as aliasn1, m2.nombre as nomn2,m2.alias aliasn2,model.modelo,model.alias FROM menus m INNER JOIN menus m2 on m2.menu_id = m.id INNER JOIN menuitems mi on mi.menu_id = m2.id INNER JOIN modelofuncions mf on mi.modelofuncion_id = mf.id INNER JOIN privmodelos pm on pm.modelofuncion_id = mf.id INNER JOIN modelos model on mf.modelo_id = model.id WHERE pm.perfil_id = ".$perfil." AND model.modelo = '".$modelo."'  GROUP BY m.id, m2.id");
            if($modelocomplemento == null) {
                $sql2 = $conn->execute("SELECT f.alias FROM modelos m INNER JOIN modelofuncions mf on m.id = mf.modelo_id INNER JOIN funcions f on f.id = mf.funcion_id WHERE m.modelo = '".$modelo."' AND f.funcion = '".$funcion."'");
            } else {
                $sql2 = $conn->execute("SELECT CONCAT(f.alias, ' ', m.alias) AS alias  FROM modelos m INNER JOIN modelofuncions mf on m.id = mf.modelo_id INNER JOIN funcions f on f.id = mf.funcion_id WHERE m.modelo = '".$modelocomplemento."' AND f.funcion = '".$funcion."'");
            }

            $info = $sql->fetchAll("assoc");
            $info2 = $sql2->fetchAll("assoc");
        }else{
            $sql = $conn->execute("SELECT m.nombre as nomn1, m.alias as aliasn1, m2.nombre as nomn2,m2.alias aliasn2,model.modelo,model.alias FROM menus m INNER JOIN menus m2 on m2.menu_id = m.id INNER JOIN menuitems mi on mi.menu_id = m2.id INNER JOIN modelofuncions mf on mi.modelofuncion_id = mf.id INNER JOIN privmodelos pm on pm.modelofuncion_id = mf.id INNER JOIN modelos model on mf.modelo_id = model.id WHERE pm.perfil_id = ".$perfil." AND model.modelo = '".$modelo."'  GROUP BY m.id, m2.id");
            $info = $sql->fetchAll("assoc");
            $info2=[];
        }
        $data=["nav"=>$info,"complemento"=>$info2];
        return $data;
    }

    public function getTitle($model=null){
        $conn = ConnectionManager::get('dbpriv');
        $info = $conn->execute("SELECT alias FROM modelos WHERE modelo = '".$model."' AND trash = 0");
        $data = $info->fetchAll("assoc");
        return $data;
    }
}