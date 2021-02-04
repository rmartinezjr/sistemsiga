<?php
namespace App\Controller;

use Cake\Event\Event;

/**
 * Perfils Controller
 *
 * @property \App\Model\Table\PerfilsTable $Perfils
 *
 * @method \App\Model\Entity\Perfil[] paginate($object = null, array $settings = [])
 */
class PerfilsController extends AppController
{
    var $idselect_validos=array(1,2);
    var $dbconnect='dbpriv';
    var $db='fiaessecurity';

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Perfils";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($band=null)
    {
        if(!$this->accesoPantalla($this->modelo, 'index')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $dirdefault='asc';
            $paginacion = ($band==null)?20:1000000;
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data= $this->request->getData();
                $busqueda = new BusquedaController();
                $query = $busqueda->busqueda($this->modelo, 'Perfils.nombre', 'asc', $data,$paginacion);
                $query->contain('Cestados');
            } elseif (isset($_SESSION["tabla[$this->modelo]"])) {
                $session =  $_SESSION["tabla[$this->modelo]"];
                $data = $session['data'];
                $busqueda = new BusquedaController();
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $flagdirection =parent::changeSort($field,$direction);
                    if(!empty($flagdirection))
                        $dirdefault=$flagdirection;

                    $query = $busqueda->busqueda($this->modelo, 'Perfils.nombre', 'asc', $data,$paginacion, $field, $direction);
                }else{
                    $query = $busqueda->busqueda($this->modelo, 'Perfils.nombre', 'asc', $data,$paginacion);
                }
                $query->contain('Cestados');
            } else {
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $flagdirection =parent::changeSort($field,$direction);
                    if(!empty($flagdirection))
                        $dirdefault=$flagdirection;

                    $query = $this->Perfils->find()
                        ->where(['Perfils.trash' => 0])
                        ->andWhere(['Perfils.cestado_id'=>1])
                        ->order([$field=>$direction])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }else {
                    $query = $this->Perfils->find()
                        ->where(['Perfils.trash' => 0])
                        ->andWhere(['Perfils.cestado_id'=>1])
                        ->order(['Perfils.nombre'=>"asc"])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }
            }
            $perfils = $this->Paginator->paginate($query);
            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo, ["index",'edit']);
            $nav = $permisos->getRuta($this->modelo, null, $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $tiposestados = $this->Perfils->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andwhere(['Cestados.id IN'=>[1,2]]);
            $this->set(compact('perfils','herramientas','controltools','nav','titulo','tiposestados','dirdefault'));
            $this->set('_serialize', ['perfils']);
        }
    }

    public function vertodos() {
        unset($_SESSION["tabla[$this->modelo]"]);
        $this->redirect(array('controller' => $this->modelo, 'action'=> "index"));
        $this->autoRender=false;
    }

    /**
    METODO PARA IMPRIMIR DESDE EL INDEX***/
    public function imprimir(){
        $this->index(1);
        $this->viewBuilder()->setLayout("imprimir");

    }

    /**
     * View method
     *
     * @param string|null $id Perfil id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $valid= new ValidacionesController();
            if(!$valid->AccessRegisterView($this->modelo, $id, 'index', 0)){
                return $this->redirect(['action'=>'index']);
            }
            $perfil = $this->Perfils->get($id,[
                'contain' => ['Cestados']
            ]);

            $this->set('perfil', $perfil);
            $this->set('_serialize', ['perfil']);
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $this->set(compact("controltools","nav",'titulo'));
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if(!$this->accesoPantalla($this->modelo, 'add')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $perfil = $this->Perfils->newEntity();
            if ($this->request->is('post')) {
                $valid= new ValidacionesController();
                $array=array();
                $array=$valid->TrimData($this->request->getData());
                $id_select=$this->request->getData('cestado_id');
                if($valid->RegistrosIdValidos($id_select,$this->idselect_validos,'perfil-save',0)){
                    $perfil = $this->Perfils->patchEntity($perfil, $array);
                    $perfil->usuario = $this->Auth->user('username');
                    $perfil->created = date("Y-m-d H:i:s");
                    if ($this->Perfils->save($perfil)) {
                        $_SESSION["perfil-save"]=1;
                        return $this->redirect(['action' => 'view',$perfil->id]);
                    }else{
                        $_SESSION["perfil-save"]=0;
                        return $this->redirect(['action' => 'add']);
                    }
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "add",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            /*Elementos para select*/
            $tiposestados = $this->Perfils->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andwhere(['Cestados.id IN'=>[1,2]]);
            $this->set(compact('perfil','controltools','nav','titulo','tiposestados'));
            $this->set('_serialize', ['perfil']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Perfil id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $valid= new ValidacionesController();
            if(!$valid->AccessRegisterView($this->modelo, $id, 'index',0)){
                return $this->redirect(['action'=>'index']);
            }
            $perfil = $this->Perfils->get($id, [
                'contain' => ['Cestados']
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $id_select=$this->request->getData('cestado_id');
                if($valid->RegistrosIdValidos($id_select,$this->idselect_validos,'perfil-save',0)){
                    $array=array();
                    $array=$valid->TrimData($this->request->getData());
                    $perfil = $this->Perfils->patchEntity($perfil, $array);
                    $perfil->modified = date("Y-m-d H:i:s");
                    $perfil->usuariomodif = $this->Auth->user('username');

                    if ($this->Perfils->save($perfil)) {
                        $_SESSION["perfil-save"]=1;
                        return $this->redirect(['action' => 'view',$id]);
                    }else{
                        $_SESSION["perfil-save"]=0;
                        return $this->redirect(['action' => 'view',$id]);
                    }
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "edit",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            /*Elementos para select*/
            $tiposestados = $this->Perfils->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andwhere(['Cestados.id IN'=>[1,2]]);
            $this->set(compact('perfil','controltools','nav',"titulo", 'tiposestados'));
            $this->set('_serialize', ['perfil']);
        }
    }

    public function valunique(){
        $campo = trim($_POST['campo']);
        $id = $_POST['id'];
        $this->Perfils->recursive=-1;
        if($id != 0){
            $info = $this->Perfils->find("all")
                ->where(['Perfils.nombre'=>$campo, 'Perfils.id !='=>$id, 'Perfils.trash'=>0]);
        }else{
            $info = $this->Perfils->find("all")
                ->where(['Perfils.nombre'=>$campo, 'Perfils.trash'=>0]);

        }
        if($info->count()>0){
            echo json_encode(["error"=>1,"msj"=>"El nombre del perfil ya existe."]);
        }else{
            echo json_encode(["error"=>0,"msj"=>""]);
        }
        $this->autoRender=false;
    }

    /**
     * Delete method
     *
     * @param string|null $id Perfil id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            parent::callprocedureDelete($this->dbconnect,$this->modelo,$id,'perfilsDele');
            return $this->redirect(['action' => 'index']);
        }
    }
}
