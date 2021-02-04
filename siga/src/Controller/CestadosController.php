<?php
namespace App\Controller;

use Cake\Event\Event;

/**
 * Cestados Controller
 *
 * @property \App\Model\Table\CestadosTable $Cestados
 *
 * @method \App\Model\Entity\Cestado[] paginate($object = null, array $settings = [])
 */
class CestadosController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Cestados";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($band = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'index')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $paginacion = ($band==null)?20:1000000;
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data= $this->request->getData();
                $busqueda = new BusquedaController();
                $query = $busqueda->busqueda($this->modelo, 'nombre', 'asc', $data,$paginacion);
            } elseif (isset($_SESSION["tabla[$this->modelo]"])) {
                $session =  $_SESSION["tabla[$this->modelo]"];
                $data = $session['data'];
                $busqueda = new BusquedaController();
                $query = $busqueda->busqueda($this->modelo, 'nombre', 'asc', $data,$paginacion);
            } else {
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $this->Cestados->find()
                        ->where(['Cestados.trash' => 0])
                        ->order([$field=>$direction])
                        ->limit($paginacion);
                }else {
                    $query = $this->Cestados->find()
                        ->where(['Cestados.trash' => 0])
                        ->order(['nombre'=>"asc"])
                        ->limit($paginacion);
                }
            }
            $cestados = $this->Paginator->paginate($query);

            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo, ["index",'edit']);
            $nav = $permisos->getRuta($this->modelo, null, $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $this->set(compact('cestados','herramientas','controltools','nav','titulo'));
            $this->set('_serialize', ['cestados']);
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
     * @param string|null $id Cestado id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            /*$cestado = $this->Cestados->get($id, [
                        'contain' => ['Ccontactotipos', 'Cdocidtipos', 'Centidadrols', 'Centidadtipos', 'Cformtipos', 'Cindicadorambitos', 'Cindicadortipos', 'Cobjetoplanifs', 'Ctipodatos', 'Cunidads']
                    ]);*/
            $cestado = $this->Cestados->get($id);

            $this->set('cestado', $cestado);
            $this->set('_serialize', ['cestado']);
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
            $cestado = $this->Cestados->newEntity();
            if ($this->request->is('post')) {

                $cestado = $this->Cestados->patchEntity($cestado, $this->request->getData());
                $cestado->usuario = $this->Auth->user('username');
                $cestado->created = date("Y-m-d H:i:s");
                if ($this->Cestados->save($cestado)) {
                    $_SESSION["cestado-save"]=1;
                    return $this->redirect(['action' => 'view',$cestado->id]);
                }else{
                    $_SESSION["cestado-save"]=1;
                    return $this->redirect(['action' => 'add']);
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "add",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $this->set(compact('cestado','controltools','nav','titulo'));
            $this->set('_serialize', ['cestado']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Cestado id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {

            $val=parent::callprocedureCestado($id,'cestado_id','dbtransac','dbpriv');
            if($val=="0")
            {
                $cestado = $this->Cestados->get($id, [
                'contain' => []
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {

                $cestado = $this->Cestados->patchEntity($cestado, $this->request->getData());

                $cestado->modified = date("Y-m-d H:i:s");
                $cestado->usuariomodif = $this->Auth->user('username');

                if ($this->Cestados->save($cestado)) {
                    $_SESSION["cestado-save"]=1;
                    return $this->redirect(['action' => 'view',$id]);
                }else{
                    $_SESSION["cestado-save"]=0;
                    return $this->redirect(['action' => 'view',$id]);
                }

            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "edit",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $this->set(compact('cestado','controltools','nav',"titulo"));
            $this->set('_serialize', ['cestado']);

            }
            else{
                $_SESSION["error_editarmsjcestado"]=explode (".", $val)[1];
                $_SESSION["error_editarcestado"]=1;
                return $this->redirect(['action' => 'index']);
            }
        }
    }

    public function valunique(){
        $estado = $_POST['estado'];
        $id = $_POST['id'];
        $this->Cestados->recursive=-1;
        if($id != 0){
            $info = $this->Cestados->find("all")
                        ->where(['Cestados.nombre'=>$estado, 'Cestados.id !='=>$id, 'Cestados.trash'=>0]);
        }else{
            $info = $this->Cestados->find("all")
                                    ->where(['Cestados.nombre'=>$estado, 'Cestados.trash'=>0]);
        }
        if($info->count()>0){
            echo json_encode(["error"=>1,"msj"=>"El nombre del estado ya existe."]);
        }else{
            echo json_encode(["error"=>0,"msj"=>""]);
        }
        $this->autoRender=false;
    }
    /**
     * Delete method
     *
     * @param string|null $id Cestado id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {

         $val=parent::callprocedureCestado($id,'cestado_id','dbtransac','dbpriv');
       if($val=="0")
       {
           parent::callprocedureDelete('dbtransac',$this->modelo,$id,'cestadoDele');
       }
       else{
           $_SESSION["error_eliminarsmj"]=explode (".", $val)[1];
           $_SESSION["error_eliminarcestado"]=1;
       }

            return $this->redirect(['action' => 'index']);
        }
    }

    public function getEstados($activo)
    {
        $this->loadModel('Cpreferences');
        if ($this->Cpreferences->exists(['Cpreferences.id' => 1, 'Cpreferences.trash' => 0])) {
            $general = $this->Cpreferences->find()
                ->select(['Cpreferences.params'])
                ->where(['Cpreferences.id' => 1])
                ->first()
                ->toArray();
            $configuraciones = $general['params'];

            $estadoactivo = (isset($configuraciones['estadoactivo'])) ? $configuraciones['estadoactivo'] : null;
            $estadoinactivo = (isset($configuraciones['estadoinactivo'])) ? $configuraciones['estadoinactivo'] : null;

            if(!$activo) {
                return [$estadoactivo, $estadoinactivo];
            } else {
                return $estadoactivo;
            }
        } else {
            if(!$activo) {
                return [0];
            } else {
                return 1;
            }
        }
    }

}
