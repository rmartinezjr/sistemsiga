<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use App\Controller\AppController;

/**
 * Cerrors Controller
 *
 * @property \App\Model\Table\CerrorsTable $Cerrors
 *
 * @method \App\Model\Entity\Cerror[] paginate($object = null, array $settings = [])
 */
class CerrorsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');

        $json = new JsonController();
        $this->estadosId = $json->getEstadosConfGeneral();
        $this->estadoActivo = $json->getEstadosConfGeneral('activo');
        $this->idselect_validos = $this->estadosId;
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Cerrors";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";

        $this->Auth->allow(['getmessageserror']);
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
                $query = $busqueda->busqueda($this->modelo, 'Cerrors.nombre', 'asc', $data, $paginacion);
                $query->contain('Cestados');
            } elseif (isset($_SESSION["tabla[$this->modelo]"])) {
                $session =  $_SESSION["tabla[$this->modelo]"];
                $data = $session['data'];
                $busqueda = new BusquedaController();
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $busqueda->busqueda($this->modelo, 'Cerrors.nombre', 'asc', $data, $paginacion, $field, $direction);
                } else {
                    $query = $busqueda->busqueda($this->modelo, 'Cerrors.nombre', 'asc', $data, $paginacion);
                }

                $query->contain('Cestados');
            } else {
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $this->Cerrors->find()
                        ->where(['Cerrors.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$field => $direction])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }else {
                    $query = $this->Cerrors->find()
                        ->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Cestados.id' => $this->estadoActivo])
                        ->order([$this->modelo . '.nombre'=>"asc"])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }
            }
            $cerrors = $this->Paginator->paginate($query);

            $cestados = $this->Cerrors->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $user = $this->Auth->user();
            $permisos = new VerificacionPermisosController();

            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo,["index",'edit']);
            $nav = $permisos->getRuta($this->modelo,null,$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $this->set(compact('cerrors','cestados','herramientas','controltools','nav','titulo'));
            $this->set('_serialize', ['cerrors']);
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
     * @param string|null $id Cerror id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Cerrors->exists(['Cerrors.id' => $id, 'Cerrors.trash' => 0])) {
                $cerror = $this->Cerrors->get($id, [
                    'contain' => ['Cestados']
                ]);

                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir']);
                $nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
                $titulo = $permisos->getTitle($this->modelo);

                $this->set(compact('cerror', 'controltools', 'nav', 'titulo'));
                $this->set('_serialize', ['cerror']);
            } else {
                $this->Flash->erroracceso(__('El catalogo de error no existe o ha sido eliminado'));
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
            $cerror = $this->Cerrors->newEntity();
            if ($this->request->is('post')) {
                $valid= new ValidacionesController();
                $array=array();
                $array=$valid->TrimData($this->request->getData());
                $id_select=$this->request->getData('cestado_id');

                if($valid->RegistrosIdValidos($id_select,$this->idselect_validos,'cerror-save',0)){
                    $cerror = $this->Cerrors->patchEntity($cerror, $array);
                    $cerror->usuario = $this->Auth->user('username');
                    $cerror->created = date('Y-m-d H:i:s');
                    $cerror->trash = 0;
                    $cerror->modified = null;

                    if ($this->Cerrors->save($cerror)) {
                        $_SESSION["cerror-save"]=1;
                        return $this->redirect(['action' => 'view', $cerror->id]);
                    } else {
                        $_SESSION["cerror-save"]=0;
                        return $this->redirect(['action' => 'add']);
                    }
                }
            }
            $cestados = $this->Cerrors->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools=$permisos->getControlTool($this->modelo ,["exportPdf","add",'imprimir','edit']);
            $nav = $permisos->getRuta($this->modelo, "add",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('cerror', 'cestados', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['cerror']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Cerror id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Cerrors->exists(['Cerrors.id' => $id, 'Cerrors.trash' => 0])) {
                $cerror = $this->Cerrors->get($id, [
                    'contain' => []
                ]);

                $valid= new ValidacionesController();
                if ($this->request->is(['patch', 'post', 'put'])) {
                    $array=array();
                    $array=$valid->TrimData($this->request->getData());
                    $id_select=$this->request->getData('cestado_id');
                    if($valid->RegistrosIdValidos($id_select,$this->idselect_validos,'cfirma-save',0)){
                        $cerror = $this->Cerrors->patchEntity($cerror, $array);
                        $cerror->modified = date("Y-m-d H:i:s");
                        $cerror->usuariomodif = $this->Auth->user('username');
                        if ($this->Cerrors->save($cerror)) {
                            $_SESSION["cerror-save"]=1;
                            return $this->redirect(['action' => 'view',$id]);
                        } else {
                            $_SESSION["cerror-save"]=0;
                            return $this->redirect(['action' => 'view',$id]);
                        }
                    }
                }
                $cestados = $this->Cerrors->Cestados->find('list')
                    ->where(['Cestados.trash' => 0])
                    ->andWhere(['Cestados.id IN' => $this->estadosId]);

                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit']);
                $nav = $permisos->getRuta($this->modelo, "edit",$user['perfil_id']);
                $titulo = $permisos->getTitle($this->modelo);

                $this->set(compact('cerror', 'cestados','controltools', 'nav', "titulo"));
                $this->set('_serialize', ['cerror']);
            } else {
                $this->Flash->erroracceso(__('El catalogo de error no existe o ha sido eliminado'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
            }
        }
    }

    public function valunique(){
        $campo = trim($_POST['campo']);
        $id = $_POST['id'];
        $tipo= $_POST['tipo'];
        $flag=0;
        $msj="";
        switch($tipo){
            case 'nombre':
                $flag=1;
                $msj="El ";
                break;
            case 'codigo':
                $flag=1;
                $msj="El ";
                break;
            default:
                $flag=0;
        }

        if($flag==1){
            $this->Cerrors->recursive=-1;
            if($id != 0){
                $info = $this->Cerrors->find("all")
                    ->where(['Cerrors.'.$tipo=>$campo, 'Cerrors.id !='=>$id, 'Cerrors.trash'=>0]);
            }else{
                $info = $this->Cerrors->find("all")
                    ->where(['Cerrors.'.$tipo=>$campo, 'Cerrors.trash'=>0]);
            }
            if($info->count()>0){
                echo json_encode(["error"=>1,"msj"=>$msj.$tipo." del catálogo de error ya existe."]);
            }else{
                echo json_encode(["error"=>0,"msj"=>""]);
            }
        }
        else{
            echo json_encode(["error"=>0,"msj"=>""]);
        }
        $this->autoRender=false;
    }

    /**
     * Delete method
     *
     * @param string|null $id Cerror id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            parent::callprocedureDelete('dbtransac',$this->modelo,$id,'cerrorsDele');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function getmessageserror()
    {
        $error_code = (int)$_POST['error_code'];
        $url = $_POST['url_error'];

        $cerror = $this->Cerrors->find()
            ->where(['Cerrors.codigo' => $error_code])
            ->andWhere(['Cerrors.trash' => 0])
            ->andWhere(['Cerrors.cestado_id' => $this->estadoActivo])
            ->first();

        if(count($cerror) > 0) {
            $html = str_replace('$url', $url, $cerror->html);
            echo $html;
        } else {
            if($error_code < 500) {
                echo '<p style="margin-top: 15px;">Ha ocurrido un error, en la dirección URL, </p>';
                echo '<p style="margin-top: 10px; text-decoration: underline;">' . $url . '</p>';
                echo '<p style="margin-top: 10px;">Dar clic a <strong>Inicio</strong> o intente con otra dirección VÁLIDA, si el error persiste por favor contacte al administrador del sistema o enviar correo electrónico a <span style="text-decoration: underline;">soporte@tecnologias101.com</span>.</p>';
            } else {
                echo '<p style="margin-top: 15px;">Ha ocurrido un error interno, en la dirección URL, </p>';
                echo '<p style="margin-top: 10px; text-decoration: underline;">' . $url . '</p>';
                echo '<p style="margin-top: 10px;">Dar clic a <strong>Inicio</strong> o intente nuevamente, si el error persiste por favor contacte al administrador del sistema o enviar correo electrónico a <span style="text-decoration: underline;">soporte@tecnologias101.com</span>.</p>';
            }
        }

        $this->autoRender=false;
    }
}
