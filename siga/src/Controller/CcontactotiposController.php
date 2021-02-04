<?php
namespace App\Controller;

use App\Model\Entity\Contactofd;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Ccontactotipos Controller
 *
 * @property \App\Model\Table\CcontactotiposTable $Ccontactotipos
 *
 * @method \App\Model\Entity\Ccontactotipo[] paginate($object = null, array $settings = [])
 */
class CcontactotiposController extends AppController
{
    var $idselect_validos=array(1,2);

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadModel('Contactofds');
        $this->loadModel('Formdinamics');
        $this->loadModel('Dataforms');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Ccontactotipos";
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
            $paginacion = ($band==null)?20:1000000;
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data= $this->request->getData();
                $busqueda = new BusquedaController();
                $query = $busqueda->busqueda($this->modelo, 'Ccontactotipos.nombre', 'asc', $data,$paginacion);
                $query->contain('Cestados');
            } elseif (isset($_SESSION["tabla[$this->modelo]"])) {
                $session =  $_SESSION["tabla[$this->modelo]"];
                $data = $session['data'];
                $busqueda = new BusquedaController();
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $busqueda->busqueda($this->modelo, 'Ccontactotipos.nombre', 'asc', $data,$paginacion, $field, $direction);
                }else{
                    $query = $busqueda->busqueda($this->modelo, 'Ccontactotipos.nombre', 'asc', $data,$paginacion);
                }
                $query->contain('Cestados');
            } else {
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $this->Ccontactotipos->find()
                        ->where(['Ccontactotipos.trash' => 0])
                        ->andWhere(['Cestados.id'=>1])
                        ->order([$field=>$direction])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }else {
                    $query = $this->Ccontactotipos->find()
                        ->where(['Ccontactotipos.trash' => 0])
                        ->andWhere(['Cestados.id'=>1])
                        ->order(['Ccontactotipos.nombre'=>"asc"])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }
            }
            $ccontactotipos = $this->Paginator->paginate($query);
            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo, ["index",'edit', 'assignmentform']);
            $nav = $permisos->getRuta($this->modelo, null, $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $tiposestados = $this->Ccontactotipos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andwhere(['Cestados.id IN'=>[1,2]]);
            $this->set(compact('ccontactotipos','herramientas','controltools','nav','titulo','tiposestados'));
            $this->set('_serialize', ['ccontactotipos']);
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
     * @param string|null $id Ccontactotipo id.
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
            if(!$valid->AccessRegisterView($this->modelo, $id, 'index')){
               return $this->redirect(['action'=>'index']);
            }
            /*$cestado = $this->Cestados->get($id, [
                        'contain' => ['Ccontactotipos', 'Cdocidtipos', 'Centidadrols', 'Centidadtipos', 'Cformtipos', 'Cindicadorambitos', 'Cindicadortipos', 'Cobjetoplanifs', 'Ctipodatos', 'Cunidads']
                    ]);*/
            $ccontactotipo = $this->Ccontactotipos->get($id,[
                'contain' => ['Cestados']
            ]);

            $this->set('ccontactotipo', $ccontactotipo);
            $this->set('_serialize', ['ccontactotipo']);
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            /*Obteniendo id de formularios ingresados al tipo de entidad*/
            $formasigids=$this->Contactofds->find('list',[
                'keyField'=>'s',
                'valueField'=>'formdinamic_id'
            ])
                ->where(['ccontactotipo_id'=>$id])
                ->andWhere(['trash'=>0])
                ->andWhere(['vinculo'=>1])
                ->toArray();

            if(count($formasigids)==0)$formasigids[0]=0;
            //obteniendo información de formularios ya ingresados al tipo de entidad
            $formasig=$this->Formdinamics->find()
                ->where(['Formdinamics.id IN'=>$formasigids])
                ->andWhere(['Formdinamics.trash'=>0])
                ->andWhere(['Cestados.trash'=>0])
                ->contain('Cestados');
            $this->set(compact("controltools","nav",'titulo','formasig'));
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
            $ccontactotipo = $this->Ccontactotipos->newEntity();
            if ($this->request->is('post')) {
                $valid= new ValidacionesController();
                $array=array();
                $array=$valid->TrimData($this->request->getData());
                $id_select=$this->request->getData('cestado_id');
                if($valid->RegistrosIdValidos($id_select,$this->idselect_validos,'ccontactotipo-save',0)){
                    $ccontactotipo = $this->Ccontactotipos->patchEntity($ccontactotipo, $array);
                    $ccontactotipo->usuario = $this->Auth->user('username');
                    $ccontactotipo->created = date("Y-m-d H:i:s");
                    if ($this->Ccontactotipos->save($ccontactotipo)) {
                        $_SESSION["ccontactotipo-save"]=1;
                        return $this->redirect(['action' => 'view',$ccontactotipo->id]);
                    }else{
                        $_SESSION["ccontactotipo-save"]=0;
                        return $this->redirect(['action' => 'add']);
                    }
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit', 'assignmentform']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "add",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            /*Elementos para select*/
            $tiposestados = $this->Ccontactotipos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andwhere(['Cestados.id IN'=>[1,2]]);
            $this->set(compact('ccontactotipo','controltools','nav','titulo','tiposestados'));
            $this->set('_serialize', ['ccontactotipo']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Ccontactotipo id.
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
            $array=array();
            $array=$valid->TrimData($this->request->getData());
            if(!$valid->AccessRegisterView($this->modelo, $id, 'index')){
                return $this->redirect(['action'=>'index']);
            }
            $ccontactotipo = $this->Ccontactotipos->get($id, [
                'contain' => ['Cestados']
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $id_select=$this->request->getData('cestado_id');
                if($valid->RegistrosIdValidos($id_select,$this->idselect_validos,'ccontactotipo-save',0)){
                    $ccontactotipo = $this->Ccontactotipos->patchEntity($ccontactotipo, $array);
                    $ccontactotipo->modified = date("Y-m-d H:i:s");
                    $ccontactotipo->usuariomodif = $this->Auth->user('username');

                    if ($this->Ccontactotipos->save($ccontactotipo)) {
                        $_SESSION["ccontactotipo-save"]=1;
                        return $this->redirect(['action' => 'view',$id]);
                    }else{
                        $_SESSION["ccontactotipo-save"]=0;
                        return $this->redirect(['action' => 'view',$id]);
                    }
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit', 'assignmentform']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "edit",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            /*Elementos para select*/
            $tiposestados = $this->Ccontactotipos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andwhere(['Cestados.id IN'=>[1,2]]);
            $this->set(compact('ccontactotipo','controltools','nav',"titulo", 'tiposestados'));
            $this->set('_serialize', ['ccontactotipo']);
        }
    }

    public function assignmentform($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'assignmentform')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Ccontactotipos->exists(['Ccontactotipos.id' => $id, 'Ccontactotipos.trash' => 0])) {
                $ccontactotipo = $this->Ccontactotipos->get($id, [
                    'contain' => []
                ]);

                $json = new JsonController();
                $valid= new ValidacionesController();

                //obteniendo el estado publicado de cpreferences
                $epublicado=$json->preferencesLevel1('FormDinamicEstados','publicado');

                /*Obteniendo id de formularios ingresados al tipo de entidad*/
                $formasigids=$this->Contactofds->find('list',[
                    'keyField'=>'s',
                    'valueField'=>'formdinamic_id'
                ])
                    ->where(['ccontactotipo_id'=>$id])
                    ->andWhere(['trash'=>0])
                    ->andWhere(['vinculo'=>1])
                    ->toArray();

                if(count($formasigids)==0)$formasigids[0]=0;
                //obteniendo información de formularios ya ingresados al tipo de entidad
                $formasig=$this->Formdinamics->find()
                    ->where(['Formdinamics.id IN'=>$formasigids])
                    ->andWhere(['Formdinamics.trash'=>0])
                    ->andWhere(['Cestados.trash'=>0])
                    ->contain('Cestados');

                //Obteniendo listado de idformdinamicos no asignados al tipo de entidad
                $forms=$this->Formdinamics->find('list',[
                    'keyField'=>'id',
                    'valueField'=>'alias'
                ])
                    ->where(['cestado_id'=>$epublicado])
                    ->andWhere(['trash'=>0])
                    ->andWhere(['id NOT IN'=>$formasigids])
                    ->toArray();

                $forms_no_asig=$this->Formdinamics->find('list',[
                    'keyField'=>'id',
                    'valueField'=>'id'
                ])
                    ->where(['cestado_id'=>$epublicado])
                    ->andWhere(['trash'=>0])
                    ->andWhere(['id NOT IN'=>$formasigids])
                    ->toArray();

                if($formasigids[0]==0)$formasigids=[];

                if ($this->request->is(['post', 'put'])) {
                    $id_select=$this->request->getData('forms_id');
                    if($valid->RegistrosIdValidos($id_select,$forms_no_asig,'assignment-save',0)) {
                        $contactofdsTable = TableRegistry::get('Contactofds');

                        $contactofd=$this->Contactofds->find()
                            ->where(['ccontactotipo_id'=>$id])
                            ->andWhere(['formdinamic_id'=>$id_select])
                            ->andWhere(['trash'=>0])
                            ->andWhere(['vinculo'=>0])
                            ->first();

                        if(count($contactofd) > 0) {
                            $contactofd->vinculo=1;
                            $contactofd->modified = date("Y-m-d H:i:s");
                            $contactofd->usuariomodif = $this->Auth->user('username');
                        } else {
                            $contactofd = new Contactofd();
                            $contactofd->ccontactotipo_id=$id;
                            $contactofd->formdinamic_id=$id_select;
                            $contactofd->vinculo=1;
                            $contactofd->created = date("Y-m-d H:i:s");
                            $contactofd->usuario = $this->Auth->user('username');
                            $contactofd->modified = null;
                        }

                        if($contactofdsTable->save($contactofd)){
                            $_SESSION["assignment-save"]=1;
                            return $this->redirect(['action' => 'assignmentform',$id]);
                        }else{
                            $_SESSION["assignment-save"]=0;
                            return $this->redirect(['action' => 'assignmentform',$id]);
                        }
                    }
                }

                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools = $permisos->getControlTool($this->modelo, ["exportPdf", 'index', "add",'imprimir','edit', 'assignmentform']);
                $nav = $permisos->getRuta($this->modelo,"assignmentform",$user['perfil_id']);
                $titulo = $permisos->getTitle($this->modelo);

                //contador para saber el colo de la fila a pintar
                $cont=1;
                $access=1;
                if(count($forms)==0) $access=0;

                $this->set(compact('ccontactotipo', 'controltools', 'nav', 'titulo', 'epublicado', 'forms', 'formasigids', 'formasig', 'cont', 'access'));
            } else {
                $this->Flash->erroracceso(__('El tipo de contacto no existe o ha sido eliminado'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
            }
        }
    }

    public function desvincularfd()
    {
        $registroid = $_POST['registroid'];
        $formdinamicid = $_POST['formdinamicid'];

        $registro=$this->Contactofds->find()
            ->where(['ccontactotipo_id'=>$registroid])
            ->andWhere(['formdinamic_id'=>$formdinamicid])
            ->andWhere(['trash'=>0])
            ->andWhere(['vinculo'=>1])
            ->first();

        if(count($registro) > 0) {
            $datos=$this->Dataforms->find()
                ->andWhere(['formdinamic_id'=>$formdinamicid])
                ->andWhere(['modelo'=>'Contactos'])
                ->andWhere(['trash'=>0])
                ->all()->toArray();
            if(count($datos) > 0) {
                $error = 1;
            } else {
                $contactofds = TableRegistry::get('Contactofds');
                $registro->vinculo = 0;
                $registro->modified = date('Y-m-d H:i:s');
                $registro->usuariomodif = $this->Auth->user('username');
                if($contactofds->save($registro)) {
                    $error = 0;
                    $_SESSION['desvincular-contactotipo'] = 1;
                } else {
                    $error = 2;
                }
            }
        } else {
            $error = 2;
        }
        
        echo json_encode(["error" => $error]);
        $this->autoRender=false;
    }
    
    public function valunique(){
        $campo = trim($_POST['campo']);
        $id = $_POST['id'];
        $this->Ccontactotipos->recursive=-1;
        if($id != 0){
            $info = $this->Ccontactotipos->find("all")
                ->where(['Ccontactotipos.nombre'=>$campo, 'Ccontactotipos.id !='=>$id, 'Ccontactotipos.trash'=>0]);
        }else{
            $info = $this->Ccontactotipos->find("all")
                ->where(['Ccontactotipos.nombre'=>$campo, 'Ccontactotipos.trash'=>0]);
        }
        if($info->count()>0){
            echo json_encode(["error"=>1,"msj"=>"El nombre del tipo contacto ya existe."]);
        }else{
            echo json_encode(["error"=>0,"msj"=>""]);
        }
        $this->autoRender=false;
    }

    /**
     * Delete method
     *
     * @param string|null $id Ccontactotipo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            parent::callprocedureDelete('dbtransac',$this->modelo,$id,'ccontactotiposDele');
            return $this->redirect(['action' => 'index']);
        }
    }
}
