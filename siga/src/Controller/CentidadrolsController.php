<?php
namespace App\Controller;

use Cake\Event\Event;

/**
 * Centidadrols Controller
 *
 * @property \App\Model\Table\CentidadrolsTable $Centidadrols
 *
 * @method \App\Model\Entity\Centidadrol[] paginate($object = null, array $settings = [])
 */
class CentidadrolsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Centidadrols";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";
        $this->estadosId = [1,2];
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
                $query = $busqueda->busqueda($this->modelo, 'Centidadrols.nombre', 'asc', $data,$paginacion);
                $query->contain('Cestados');
            } elseif ( isset($_SESSION["tabla[$this->modelo]"]) ) {
                $session =  $_SESSION["tabla[$this->modelo]"];
                $data = $session['data'];
                $busqueda = new BusquedaController();
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $busqueda->busqueda($this->modelo, 'Centidadrols.nombre', 'asc', $data, $paginacion, $field, $direction);
                } else {
                    $query = $busqueda->busqueda($this->modelo, 'Centidadrols.nombre', 'asc', $data,$paginacion);
                }

                $query->contain('Cestados');
            } else {
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $this->Centidadrols->find()
                        ->where(['Centidadrols.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$field => $direction])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }else {
                    $query = $this->Centidadrols->find()
                        ->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$this->modelo . '.nombre'=>"asc"])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }
            }
            $centidadrols = $this->Paginator->paginate($query);
            $permite_eliminar=array();//array para conocer si el registro puede ser eliminado o no
            foreach($centidadrols as $centidadrol){
                //si el retorno de función es cero, el registro puede ser eliminado
                $permite_eliminar[$centidadrol->id]=$this->extractRegistrosEntidadCrol($centidadrol->id);
            }
            $cestados = $this->Centidadrols->Cestados->find('list')
                             ->where(['Cestados.trash' => 0])
                             ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo,["index",'edit']);
            $nav = $permisos->getRuta($this->modelo,null,$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('centidadrols','cestados', 'herramientas','controltools','nav','titulo','permite_eliminar'));
            $this->set('_serialize', ['centidadrols']);
        }
    }

    public function vertodos() {
        unset($_SESSION["tabla[$this->modelo]"]);
        $this->redirect(array('controller'=> $this->modelo, 'action'=> "index"));
        $this->autoRender=false;
    }

    // Metodo para mostrar impresion desde el index
    public function imprimir(){
        $this->index(1);
        $this->viewBuilder()->setLayout("imprimir");
    }

    /**
     * View method
     *
     * @param string|null $id Centidadrol id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Centidadrols->exists(['Centidadrols.id' => $id, 'Centidadrols.trash' => 0])) {
                $centidadrol = $this->Centidadrols->get($id, [
                    'contain' => ['Cestados']
                ]);

                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir']);
                $nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
                $titulo = $permisos->getTitle($this->modelo);

                $this->set(compact('centidadrol', 'controltools', 'nav', 'titulo'));
                $this->set('_serialize', ['centidadrol']);
            } else {
                $this->Flash->erroracceso(__('El rol de entidad no existe o ha sido eliminado'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
            }
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
            $centidadrol = $this->Centidadrols->newEntity();
            if ($this->request->is('post')) {
                $centidadrol = $this->Centidadrols->patchEntity($centidadrol, $this->request->getData());
                $centidadrol->usuario = $this->Auth->user('username');
                $centidadrol->created = date('Y-m-d H:i:s');
                $centidadrol->trash = 0;
                $centidadrol->modified = null;

                if ($this->Centidadrols->save($centidadrol)) {
                    //$this->Flash->success(__('The centidadrol has been saved.'));
                    $_SESSION["centidadrol-save"] = 1;
                    return $this->redirect(['action' => 'view', $centidadrol->id]);
                }
                //$this->Flash->error(__('The centidadrol could not be saved. Please, try again.'));
            }

            $cestados = $this->Centidadrols->Cestados->find('list')
                             ->where(['Cestados.trash' => 0])
                             ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools=$permisos->getControlTool($this->modelo ,["exportPdf","add",'imprimir','edit']);
            $nav = $permisos->getRuta($this->modelo, "add",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('centidadrol', 'cestados','controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['centidadrol']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Centidadrol id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $centidadrol = $this->Centidadrols->get($id, [
                'contain' => []
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $centidadrol = $this->Centidadrols->patchEntity($centidadrol, $this->request->getData());
                $centidadrol->usuariomodif = $this->Auth->user('username');
                $centidadrol->modified = date('Y-m-d H:i:s');
                if ($this->Centidadrols->save($centidadrol)) {
                    //$this->Flash->success(__('The centidadrol has been saved.'));
                    $_SESSION["centidadrol-save"] = 1;
                    return $this->redirect(['action' => 'view', $centidadrol->id]);
                }
                //$this->Flash->error(__('The centidadrol could not be saved. Please, try again.'));
            }
            $cestados = $this->Centidadrols->Cestados->find('list')
                             ->where(['Cestados.trash' => 0])
                             ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools = $permisos->getControlTool($this->modelo, ["exportPdf", "add",'imprimir','edit'  ]);
            $nav = $permisos->getRuta($this->modelo,"edit",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('centidadrol', 'cestados', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['centidadrol']);
        }
    }

    public function valunique(){
        $nombre = trim($_POST['campo']);
        $id = $_POST['id'];
        $this->Centidadrols->recursive=-1;
        if($id != 0){
            $info = $this->Centidadrols->find("all")
                ->where(['Centidadrols.nombre'=>$nombre, 'Centidadrols.id !='=>$id, 'Centidadrols.trash'=>0]);
        }else{
            $info = $this->Centidadrols->find("all")
                ->where(['Centidadrols.nombre'=>$nombre, 'Centidadrols.trash'=>0]);
        }
        if($info->count()>0){
            echo json_encode(["error"=>1,"msj"=>"El nombre del rol de entidad ya existe."]);
        }else{
            echo json_encode(["error"=>0,"msj"=>""]);
        }
        $this->autoRender=false;
    }

    /**
     * Delete method
     *
     * @param string|null $id Centidadrol id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if(!$this->extractRegistrosEntidadCrol($id)){
                parent::callprocedureDelete('dbtransac',$this->modelo,$id,'CentidadrolDele');
                return $this->redirect(['action' => 'index']);
            }else{
                $_SESSION['CentidadrolDele']=-1;
                return $this->redirect(['action' => 'index']);}
        }
    }

    public function extractRegistrosEntidadCrol($id){
        $this->loadModel('Entidads');
        $res=0;//si es 0 no se encuentra el id como llave foranea en la tabla Entidad
        $register=$this->Entidads->find()
            ->where(['centidadrol_id'=>$id])
            ->andwhere(['trash'=>0])
            ->toArray();
        if(count($register)>0) $res=1;
        return $res;

    }
}
