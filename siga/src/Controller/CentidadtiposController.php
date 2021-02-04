<?php
namespace App\Controller;

use App\Model\Entity\Entidadfd;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Centidadtipos Controller
 *
 * @property \App\Model\Table\CentidadtiposTable $Centidadtipos
 *
 * @method \App\Model\Entity\Centidadtipo[] paginate($object = null, array $settings = [])
 */
class CentidadtiposController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadModel('Entidadfds');
        $this->loadModel('Formdinamics');
        $this->loadModel('Dataforms');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Centidadtipos";
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
                $query = $busqueda->busqueda($this->modelo, 'Centidadtipos.nombre', 'asc', $data,$paginacion);
                $query->contain('Cestados');
            } elseif ( isset($_SESSION["tabla[$this->modelo]"]) ) {
                $session =  $_SESSION["tabla[$this->modelo]"];
                $data = $session['data'];
                $busqueda = new BusquedaController();
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $busqueda->busqueda($this->modelo, 'Centidadtipos.nombre', 'asc', $data, $paginacion, $field, $direction);
                } else {
                    $query = $busqueda->busqueda($this->modelo, 'Centidadtipos.nombre', 'asc', $data, $paginacion);
                }

                $query->contain('Cestados');
            } else {
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $this->Centidadtipos->find()
                        ->where(['Centidadtipos.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$field => $direction])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }else {
                    $query = $this->Centidadtipos->find()
                        ->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$this->modelo . '.nombre'=>"asc"])
                        ->limit($paginacion)
                        ->contain('Cestados');
                }
            }
            $centidadtipos = $this->Paginator->paginate($query);
            $cestados = $this->Centidadtipos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo,["index",'edit','assignmentform']);
            $nav = $permisos->getRuta($this->modelo,null,$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('centidadtipos','cestados', 'herramientas', 'controltools', 'nav','titulo'));
            $this->set('_serialize', ['centidadtipos']);
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
     * @param string|null $id Centidadtipo id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Centidadtipos->exists(['Centidadtipos.id' => $id, 'Centidadtipos.trash' => 0])) {
                $centidadtipo = $this->Centidadtipos->get($id, [
                    'contain' => ['Cestados']
                ]);

                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir']);
                $nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
                $titulo = $permisos->getTitle($this->modelo);

                /*Obteniendo id de formularios ingresados al tipo de entidad*/
                $formasigids=$this->Entidadfds->find('list',[
                    'keyField'=>'s',
                    'valueField'=>'formdinamic_id'
                ])
                    ->where(['centidadtipo_id'=>$id])
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

                $this->set(compact('centidadtipo', 'controltools', 'nav', 'titulo','formasig'));
                $this->set('_serialize', ['centidadtipo']);
            } else {
                $this->Flash->erroracceso(__('El tipo de entidad no existe o ha sido eliminado'));
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
            $centidadtipo = $this->Centidadtipos->newEntity();
            if ($this->request->is('post')) {
                $centidadtipo = $this->Centidadtipos->patchEntity($centidadtipo, $this->request->getData());
                $centidadtipo->usuario = $this->Auth->user('username');
                $centidadtipo->created = date('Y-m-d H:i:s');
                $centidadtipo->trash = 0;
                $centidadtipo->modified = null;

                if ($this->Centidadtipos->save($centidadtipo)) {
                    //$this->Flash->success(__('The centidadtipo has been saved.'));

                    $_SESSION["centidadtipo-save"] = 1;
                    return $this->redirect(['action' => 'view', $centidadtipo->id]);
                }
                //$this->Flash->error(__('The centidadtipo could not be saved. Please, try again.'));
            }
            $cestados = $this->Centidadtipos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools=$permisos->getControlTool($this->modelo ,["exportPdf","add",'imprimir','edit', 'assignmentform']);
            $nav = $permisos->getRuta($this->modelo, "add",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('centidadtipo', 'cestados', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['centidadtipo']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Centidadtipo id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $centidadtipo = $this->Centidadtipos->get($id, [
                'contain' => []
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $centidadtipo = $this->Centidadtipos->patchEntity($centidadtipo, $this->request->getData());
                $centidadtipo->usuariomodif = $this->Auth->user('username');
                $centidadtipo->modified = date('Y-m-d H:i:s');
                if ($this->Centidadtipos->save($centidadtipo)) {
                    //$this->Flash->success(__('The centidadtipo has been saved.'));
                    $_SESSION["centidadtipo-save"] = 1;
                    return $this->redirect(['action' => 'view', $centidadtipo->id]);
                }
                //$this->Flash->error(__('The centidadtipo could not be saved. Please, try again.'));
            }
            $cestados = $this->Centidadtipos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools = $permisos->getControlTool($this->modelo, ["exportPdf", "add",'imprimir','edit', 'assignmentform']);
            $nav = $permisos->getRuta($this->modelo,"edit",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('centidadtipo', 'cestados', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['centidadtipo']);
        }
    }

    public function assignmentform($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'assignmentform')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Centidadtipos->exists(['Centidadtipos.id' => $id, 'Centidadtipos.trash' => 0])) {
                $centidadtipo = $this->Centidadtipos->get($id, [
                    'contain' => []
                ]);

                $json = new JsonController();
                $valid= new ValidacionesController();

                //obteniendo el estado publicado de cpreferences
                $epublicado=$json->preferencesLevel1('FormDinamicEstados','publicado');

                /*Obteniendo id de formularios ingresados al tipo de entidad*/
                $formasigids=$this->Entidadfds->find('list',[
                    'keyField'=>'s',
                    'valueField'=>'formdinamic_id'
                ])
                    ->where(['centidadtipo_id'=>$id])
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
                        $entidadfdsTable = TableRegistry::get('Entidadfds');

                        $entidadfd=$this->Entidadfds->find()
                            ->where(['centidadtipo_id'=>$id])
                            ->andWhere(['formdinamic_id'=>$id_select])
                            ->andWhere(['trash'=>0])
                            ->andWhere(['vinculo'=>0])
                            ->first();

                        if(count($entidadfd) > 0) {
                            $entidadfd->vinculo=1;
                            $entidadfd->modified = date("Y-m-d H:i:s");
                            $entidadfd->usuariomodif = $this->Auth->user('username');
                        } else {
                            $entidadfd = new Entidadfd();
                            $entidadfd->centidadtipo_id=$id;
                            $entidadfd->formdinamic_id=$id_select;
                            $entidadfd->vinculo=1;
                            $entidadfd->created = date("Y-m-d H:i:s");
                            $entidadfd->usuario = $this->Auth->user('username');
                            $entidadfd->modified = null;
                        }

                        if($entidadfdsTable->save($entidadfd)){
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

                $this->set(compact('centidadtipo', 'controltools', 'nav', 'titulo', 'epublicado', 'forms', 'formasigids', 'formasig', 'cont', 'access'));
            } else {
                $this->Flash->erroracceso(__('El tipo de entidad no existe o ha sido eliminado'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
            }
        }
    }

    public function desvincularfd()
    {
        $registroid = $_POST['registroid'];
        $formdinamicid = $_POST['formdinamicid'];

        $registro=$this->Entidadfds->find()
            ->where(['centidadtipo_id'=>$registroid])
            ->andWhere(['formdinamic_id'=>$formdinamicid])
            ->andWhere(['trash'=>0])
            ->andWhere(['vinculo'=>1])
            ->first();

        if(count($registro) > 0) {
            $datos=$this->Dataforms->find()
                ->andWhere(['formdinamic_id'=>$formdinamicid])
                ->andWhere(['modelo'=>'Entidads'])
                ->andWhere(['trash'=>0])
                ->all()->toArray();
            if(count($datos) > 0) {
                $error = 1;
            } else {
                $entidadfds = TableRegistry::get('Entidadfds');
                $registro->vinculo = 0;
                $registro->modified = date('Y-m-d H:i:s');
                $registro->usuariomodif = $this->Auth->user('username');
                if($entidadfds->save($registro)) {
                    $error = 0;
                    $_SESSION['desvincular-entidadtipo'] = 1;
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
        $nombre = trim($_POST['campo']);
        $id = $_POST['id'];
        $this->Centidadtipos->recursive = -1;
        if($id != 0){
            $info = $this->Centidadtipos->find("all")
                ->where(['Centidadtipos.nombre'=>$nombre, 'Centidadtipos.id !='=>$id, 'Centidadtipos.trash'=>0]);
        }else{
            $info = $this->Centidadtipos->find("all")
                ->where(['Centidadtipos.nombre'=>$nombre, 'Centidadtipos.trash'=>0]);
        }
        if($info->count()>0){
            echo json_encode(["error"=>1,"msj"=>"El nombre del tipo de entidad ya existe."]);
        }else{
            echo json_encode(["error"=>0,"msj"=>""]);
        }
        $this->autoRender=false;
    }

    /**
     * Delete method
     *
     * @param string|null $id Centidadtipo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            parent::callprocedureDelete('dbtransac',$this->modelo,$id,'CentidadtipoDele');
            return $this->redirect(['action' => 'index']);
        }
    }
}
