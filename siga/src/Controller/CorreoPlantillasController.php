<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;

/**
 * CorreoPlantillas Controller
 *
 * @property \App\Model\Table\CorreoPlantillasTable $CorreoPlantillas
 *
 * @method \App\Model\Entity\CorreoPlantilla[] paginate($object = null, array $settings = [])
 */
class CorreoPlantillasController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "CorreoPlantillas";
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
                $query = $busqueda->busqueda($this->modelo, 'nombre', 'asc', $data, $paginacion);
            } elseif (isset($_SESSION["tabla[$this->modelo]"])) {
                $session =  $_SESSION["tabla[$this->modelo]"];
                $data = $session['data'];
                $busqueda = new BusquedaController();
                $query = $busqueda->busqueda($this->modelo, 'nombre', 'asc', $data, $paginacion);
            } else {
                $query = $this->CorreoPlantillas->find()
                    ->where(['CorreoPlantillas.trash' => 0])
                    ->orderAsc('nombre')
                    ->limit($paginacion);
            }
            $correoPlantillas = $this->Paginator->paginate($query);
            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo,["index",'edit']);
            $nav = $permisos->getRuta($this->modelo,null,$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $this->set(compact('correoPlantillas','herramientas','controltools','nav','titulo'));
            $this->set('_serialize', ['correoPlantillas']);
        }
    }

    public function vertodos() {
        unset($_SESSION["tabla[$this->modelo]"]);
        $this->redirect(array('controller' => $this->modelo, 'action' => "index"));
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
     * @param string|null $id Correo Plantilla id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $correoPlantilla = $this->CorreoPlantillas->get($id);

            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir']);
            $nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('correoPlantilla', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['correoPlantilla']);
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
            $correoPlantilla = $this->CorreoPlantillas->newEntity();
            if ($this->request->is('post')) {
                $correoPlantilla = $this->CorreoPlantillas->patchEntity($correoPlantilla, $this->request->getData());
                $correoPlantilla->usuario = $this->Auth->user('username');
                $correoPlantilla->created = date('Y-m-d H:i:s');
                $correoPlantilla->trash = 0;
                $correoPlantilla->modified = null;

                if ($this->CorreoPlantillas->save($correoPlantilla)) {
                    //$this->Flash->success(__('Plantilla de correo almacenada correctamente.'));
                    $_SESSION["correoplantilla-save"] = 1;

                    return $this->redirect(['action' => 'view', $correoPlantilla->id]);
                }
                //$this->Flash->error(__('La plantilla de correos no ha sido almacenada.'));
            }
            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools=$permisos->getControlTool($this->modelo ,["exportPdf","add",'imprimir','edit']);
            $nav = $permisos->getRuta($this->modelo, "add",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('correoPlantilla','controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['correoPlantilla']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Correo Plantilla id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $correoPlantilla = $this->CorreoPlantillas->get($id, [
                'contain' => []
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $correoPlantilla = $this->CorreoPlantillas->patchEntity($correoPlantilla, $this->request->getData());
                $correoPlantilla->usuariomodif = $this->Auth->user('username');
                $correoPlantilla->modified = date('Y-m-d H:i:s');
                if ($this->CorreoPlantillas->save($correoPlantilla)) {
                    //  $this->Flash->success(__('Plantilla de correo almacenada correctamente.'));
                    $_SESSION["correoplantilla-save"] = 1;

                    return $this->redirect(['action' => 'view', $correoPlantilla->id]);
                }
                // $this->Flash->error(__('La plantilla de correos no ha sido almacenada.'));
            }
            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools = $permisos->getControlTool($this->modelo, ["exportPdf", "add",'imprimir','edit']);
            $nav = $permisos->getRuta($this->modelo,"edit",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('correoPlantilla','controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['correoPlantilla']);
        }
    }

    public function valunique(){
        $campo = $_POST['campo'];
        $id = $_POST['id'];
        $this->CorreoPlantillas->recursive=-1;
        if($id != 0){
            $info = $this->CorreoPlantillas->find("all")
                ->where(['CorreoPlantillas.nombre'=>$campo, 'CorreoPlantillas.id !='=>$id, 'CorreoPlantillas.trash'=>0]);
        }else{
            $info = $this->CorreoPlantillas->find("all")
                ->where(['CorreoPlantillas.nombre'=>$campo, 'CorreoPlantillas.trash'=>0]);
        }
        if($info->count()>0){
            echo json_encode(["error"=>1,"msj"=>"El nombre de la plantilla de correo ya existe."]);
        }else{
            echo json_encode(["error"=>0,"msj"=>""]);
        }
        $this->autoRender=false;
    }

    /**
     * Delete method
     *
     * @param string|null $id Correo Plantilla id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $conn = ConnectionManager::get('dbtransac');
            $conn->execute("CALL sp_eliminar_registro('fiaes_security', 'correo_plantillas', " . $id . ")");
            $_SESSION["correoPlantillaDele"] = 1;

            return $this->redirect(['action' => 'index']);
        }
    }
}
