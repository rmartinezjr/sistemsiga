<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        // Configuracion del componente de autenticacion
        $this->loadComponent('Auth', [
            'authorize' => ['Controller'],
            'authenticate' => [  // Tipo de autenticacion
                'Form' => [
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password',
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authError' => '', // Cuando se accede sin autenticarse
            'loginRedirect' => [ // Redireccion cuando se autentifique de forma correcta
                'controller' => 'Pages',
                'action' => 'index'
            ],
            'logoutRedirect' => [ // Redireccion cuando se cierre la sesion
                'controller' => 'Users',
                'action' => 'login'
            ]
        ]);
    }

    public function beforeFilter(Event $event) {
        $userId = $this->Auth->user('id');
        $userName = $this->Auth->user('username');

        $this->loadModel('Users');
        $usuario_actual = $this->Users->find()
            ->select(['Contactos.nombres','Contactos.apellidos','Perfils.nombre'])
            ->where(['Users.id' => $userId])
            ->contain(['Contactos','Perfils'])
            ->first();

        $nombre_usuario = '';
        $perfil_user = '';
        if($usuario_actual) {
            $array_nombre = explode(' ', $usuario_actual->Contactos->nombres);
            $array_apellido = explode(' ', $usuario_actual->Contactos->apellidos);
            $nombre_usuario = ucfirst(strtolower($array_nombre[0])) . ' ' . ucfirst(strtolower($array_apellido[0]));
            $perfil_user = $usuario_actual->Perfils->nombre;
        }

        $this->set(compact("nombre_usuario","UserId", "userName",'perfil_user'));
    }

    // Verifica las acciones que puede realizar el perfil del usuario logueado
    public function accesoPantalla($modelo = null, $metodo = null) {
        $perfil_id = $user = $this->Auth->user('perfil_id');

        $sql = "SELECT pm.allow
                FROM modelos m 
                  INNER JOIN modelofuncions mf ON m.id = mf.modelo_id 
                  INNER JOIN funcions f ON mf.funcion_id = f.id 
                  INNER JOIN privmodelos pm ON pm.modelofuncion_id = mf.id 
                WHERE mf.trash = 0 AND f.trash = 0 AND f.funcion = '$metodo' 
                  AND m.trash = 0 AND m.modelo = '$modelo' 
                  AND pm.trash = 0 AND pm.perfil_id = $perfil_id";

        $conn = ConnectionManager::get('dbpriv');
        $inf = $conn->execute($sql);
        $resultado = $inf->fetchAll("assoc");

        if(isset($resultado[0]['allow'])) {
            if($resultado[0]['allow'] == '0') {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    //funcion para executar procedure de eliminar
    /*  $dbconnect -> nombre de la conexi�n a utilizar dbpriv/dbtransac por ejemplo
        $model -> nombre del modelo
        $id -> id del registro a eliminar
        $session -> nombre de la session a crear
    */
    public function callprocedureDelete($dbconnect, $model, $id, $session){
        $model="'".strtolower($model)."'";
        $conn = ConnectionManager::get($dbconnect);
        $table="'".$conn->config()["database"]."'";
        $conn->execute("CALL sp_eliminar_registro($table,$model,".$id.")");

            if($session != '') {
                $_SESSION[$session]=1;
            }
    }

    /* Procedimiento para buscar si el id del estado esta relacionado con otro modelo, la busqueda la hace
        en $dbconnect( se obtine la base asignada en app.php) y $dbpriv ( se obtine la base asignada en app.php)
        si hay relacion retorna el nombre de la base de datos concatenado con el nombre de la tabla, sino retorna 0.
        variables:
        columna-> nombre de la llave foranea */

    public function callprocedureCestado($id, $columna, $dbconnect, $dbpriv){

        $conn = ConnectionManager::get($dbconnect);
        $base1="'".$conn->config()["database"]."'";
        $conn2 = ConnectionManager::get($dbpriv);
        $base2="'".$conn2->config()["database"]."'";
        $conn->execute("CALL sp_cestado(".$id.", @p1,'$columna',$base1,$base2)");

        $msj= $conn->execute( "SELECT @p1 AS out_table_name");
        return  $msj->fetchAll("assoc")[0]['out_table_name'];
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        //$current_user = $this->Auth->user();
        //$this->set(compact("current_user"));
    }

    // Autorizacion de usuarios
    public function isAuthorized($user) {
        return true;
    }

    public function menu($perfil = null)
    {
        //Nivel 1
        $query1= $this->loadModel('Menus')->find('all',
            ['fields' => array('Menus.alias', 'Menus.id', 'Menus.filename', 'Menus.posicion','Menus.orden'), 'recursive' => -1])
            ->join([
                ['table' => 'menus',
                    'alias' => 'mn',
                    'type' => 'inner',
                    'conditions' => ['Menus.id = mn.menu_id']
                ],
                ['table' => 'menuitems',
                    'alias' => 'mi',
                    'type' => 'inner',
                    'conditions' => ['mi.menu_id = mn.id']
                ],
                ['table' => 'modelofuncions',
                    'alias' => 'mf',
                    'type' => 'inner',
                    'conditions' => ['mi.modelofuncion_id =mf.id']
                ]
                ,
                ['table' => 'modelos',
                    'alias' => 'm',
                    'type' => 'inner',
                    'conditions' => ['m.id =mf.modelo_id']
                ]
                ,
                ['table' => 'privmodelos',
                    'alias' => 'pm',
                    'type' => 'inner',
                    'conditions' => ['pm.modelofuncion_id=mf.id']
                ]
                ,
                ['table' => 'perfils',
                    'alias' => 'p',
                    'type' => 'inner',
                    'conditions' => ['p.id=pm.perfil_id']
                ]
            ])
            ->where([' pm.perfil_id ' => $perfil, 'pm.allow'=>1])
            ->group(['Menus.id']);
        $data['data']['nivel1']=$query1->select(['sum' => $query1->func()->sum('m.movil')])
            ->order(['Menus.orden'=>'ASC'])
            ->toArray();



        // Nivel 2
        $query= $this->loadModel('Menus')->find('all',
            ['fields' => array('Menus.id','Menus.alias', 'Menus.icon','Menus.menu_id'), 'recursive' => -1])
            ->join([
                ['table' => 'menuitems',
                    'alias' => 'mi',
                    'type' => 'inner',
                    'conditions' => ['mi.menu_id = Menus.id']
                ],
                ['table' => 'modelofuncions',
                    'alias' => 'mf',
                    'type' => 'inner',
                    'conditions' => ['mi.modelofuncion_id =mf.id']
                ]
                ,
                ['table' => 'modelos',
                    'alias' => 'm',
                    'type' => 'inner',
                    'conditions' => ['m.id =mf.modelo_id']
                ]
                ,
                ['table' => 'privmodelos',
                    'alias' => 'pm',
                    'type' => 'inner',
                    'conditions' => ['pm.modelofuncion_id=mf.id']
                ]
                ,
                ['table' => 'perfils',
                    'alias' => 'p',
                    'type' => 'inner',
                    'conditions' => ['p.id=pm.perfil_id']
                ]
            ])
            ->where([' pm.perfil_id ' => $perfil,'pm.allow'=>1])
            ->group(['Menus.id']);
              $data['data']['nivel2']=$query->select(['sum' => $query->func()->sum('m.movil')])
            ->order(['Menus.orden'=>'ASC'])
            ->toArray();

        $data['data']['nivel3'] = $this->loadModel('Menuitems')->find('all',
            ['fields' => array('Menuitems.alias', 'Menuitems.menu_id', 'Menuitems.id','m.modelo','m.movil','m.id'), 'recursive' => -1])
            ->join([
                ['table' => 'modelofuncions',
                    'alias' => 'mf',
                    'type' => 'inner',
                    'conditions' => ['Menuitems.modelofuncion_id =mf.id']
                ]
                ,
                ['table' => 'modelos',
                    'alias' => 'm',
                    'type' => 'inner',
                    'conditions' => ['m.id =mf.modelo_id']
                ]
                ,
                ['table' => 'privmodelos',
                    'alias' => 'pm',
                    'type' => 'inner',
                    'conditions' => ['pm.modelofuncion_id=mf.id']
                ]
                ,
                ['table' => 'perfils',
                    'alias' => 'p',
                    'type' => 'inner',
                    'conditions' => ['p.id=pm.perfil_id']
                ]
            ])
            ->where([' pm.perfil_id ' => $perfil, 'pm.allow'=>1])
            ->group(['Menuitems.id','m.id'])
            ->order(['Menuitems.orden'=>'ASC','Menuitems.alias'=>'DESC'])
            ->toArray();

        return $data['data'];
    }
    public function changeSort($field, $direction)
    {
        $explode=explode('.',$field);
        switch($explode[0]){
            case 'Cestados':
                $dir=$this->dirSort($direction);
                break;
            case 'Perfils':
                $dir=$this->dirSort($direction);
                break;
            case 'Contactos':
                $dir=$this->dirSort($direction);
                break;
            case 'Cmunicipios':
                $dir=$this->dirSort($direction);
                break;
            case 'Cdepartamentos':
                $dir=$this->dirSort($direction);
                break;
            case 'Ctipoprogramas':
                $dir=$this->dirSort($direction);
                break;
            case 'Provservicioareaapoyos':
                $dir=$this->dirSort($direction);
                break;
            case 'Provserviciocoberturas':
                $dir=$this->dirSort($direction);
                break;
            case 'Cinversions':
                $dir=$this->dirSort($direction);
                break;
            default:
                $dir=null;
        }
        return $dir;
    }

    public function dirSort($direction)
    {
        if(strcmp($direction,'asc')===0)
            return 'desc';
        return 'asc';
    }

    /*Funcion para obtener los estados validos a partir de los Workflows*/
    //$id -> id del registro a buscar
    //$modelo-> nombre de la tabla que contiene el registro de union con la tabla workflow
    //$campo_id -> campo que es uni�n entra tablas para obtener el registro especificado por el id
    //$accion-> variable para identificar el procesos a realizar y generar las condiciones para la construcci�n del query
    //$cestado_actual -> cestado_id actual del registro a buscar y as� obtener sus acciones siguientes(se utiliza solo en el view)
    public function estadosWf($id,$modelo,$campo_id, $accion=null, $cestado_actual=null){
        $json = new JsonController();
        $estado_publicado=$json->preferencesLevel1("WorkflowEstados","publicado");

        $modelo=strtolower($modelo);
        $array=[];
        $etapafinal = 0;
        $hay_etapa_fin = 0;
        $conn = ConnectionManager::get('dbtransac');
        $sql = "SELECT ce.id,ce.colorbkg,ce.colortext,wfe.icon, wfe.workflow_id,";

        if(strcmp($accion,'view')==0) $sql.="wft.nombre, wft.id as wft_id";
        else $sql.="wft.nombre";
        $sql.=" FROM cestados ce
          INNER JOIN wfetapas wfe ON ce.id = wfe.cestado_id
          INNER JOIN wftransicions wft ON wft.wfetapafin = wfe.id
          INNER JOIN workflows wf ON wfe.workflow_id = wf.id AND wft.workflow_id = wf.id
          INNER JOIN ".$modelo." rwfs ON rwfs.workflow_id = wf.id WHERE rwfs.".$campo_id."=".$id;

        switch($accion){
            case 'add':
                $sql.=" AND wft.wfetapaini=0";
                $hay_etapa_fin = 1;
                break;
            case 'view':
                $reg_eactual="SELECT wft.wfetapafin
                FROM cestados ce
                  INNER JOIN wfetapas wfe ON ce.id = wfe.cestado_id
                  INNER JOIN wftransicions wft ON wft.wfetapafin = wfe.id
                  INNER JOIN workflows wf ON wfe.workflow_id = wf.id AND wft.workflow_id = wf.id
                  INNER JOIN ".$modelo." rwfs ON rwfs.workflow_id = wf.id WHERE rwfs.".$campo_id."=".$id."
                    AND ce.id=".$cestado_actual." AND ce.trash=0 AND wf.trash=0 AND wfe.trash=0 AND wft.trash=0 AND rwfs.trash=0
                    AND wf.cestado_id=".$estado_publicado." AND rwfs.vinculo=1
                    GROUP BY wft.wfetapafin
                    ORDER BY wft.wfetapafin";
                $inf = $conn->execute($reg_eactual);
                $resultado = $inf->fetchAll("assoc");

                if(count($resultado) > 0) {
                    $hay_etapa_fin = 1;
                    $sql.=" AND wft.wfetapaini=".$resultado[0]['wfetapafin'];
                }
                break;
        }
        $sql.=" AND ce.trash=0 AND wf.trash=0 AND wfe.trash=0 AND wft.trash=0 AND rwfs.trash=0
                AND wf.cestado_id=".$estado_publicado." AND rwfs.vinculo=1 ";

        if(strcmp($accion,'view')==0) $sql.="GROUP BY wft.id,ce.id,wfe.id ORDER BY ce.nombre";
        else $sql.="GROUP BY ce.id,wfe.id,wft.id ORDER BY ce.nombre";
        $inf = $conn->execute($sql);
        $resultado = $inf->fetchAll("assoc");

        $cestados = new CestadosController();
        $this->loadModel('Cestados');
        if(empty($accion)){
            foreach ($resultado as $key => $value) $array[$value['id']]=$value['nombre'];
        }
        else{
            if(count($resultado) > 0) {
                if($hay_etapa_fin == 1) {
                    $cont=0;
                    foreach ($resultado as $key => $value) {

                        $array[$cont]['id']=$value['id'];
                        $array[$cont]['nombre']=$value['nombre'];
                        $array[$cont]['color_fondo']=$value['colorbkg'];
                        $array[$cont]['color_texo']=$value['colortext'];
                        $array[$cont]['icon']=$value['icon'];
                        if(isset($value['wft_id'])) {
                            $array[$cont]['wft_id']=$value['wft_id'];//id de la transición
                        }

                        $cont++;
                    }
                }
            } else {
                if($hay_etapa_fin == 0) {
                    // Se obtienen los estados activo e inactivo de la configuracion general
                    $estadosId = $cestados->getEstados(false);

                    $array = $this->Cestados->find()
                        ->where(['Cestados.trash' => 0])
                        ->andwhere(['Cestados.id IN' => $estadosId])->all()->toArray();
                }
            }
        }

        return $array;
    }
}
