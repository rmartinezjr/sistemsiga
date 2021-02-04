<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;


/**
 * Modelos Controller
 *
 * @property \App\Model\Table\ModelosTable $Modelos
 *
 * @method \App\Model\Entity\Modelo[] paginate($object = null, array $settings = [])
 */
class ModelosController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->estadosId = [1,2];
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Modelos";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";
        $this->estadosId = [1,2];
    }


    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($band=null)
    {
        $this->loadModel('ModeloMenu');
        if(!$this->accesoPantalla($this->modelo, 'index')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $dirdefault='asc';
            $paginacion = ($band==null)?20:1000000;
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data= $this->request->getData();


                $busqueda = new BusquedaController();
                $query = $busqueda->busqueda('ModeloMenu', 'ModeloMenu.alias', 'asc', $data,$paginacion);



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

                    $query = $busqueda->busqueda($this->modelo, 'ModeloMenu.alias', 'asc', $data,$paginacion, $field, $direction);
                }else{
                    $query = $busqueda->busqueda($this->modelo, 'ModeloMenu.alias', 'asc', $data,$paginacion);
                }

            } else {
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];

                    $direction = $this->request->getQuery()["direction"];
                    $flagdirection =parent::changeSort($field,$direction);
                    if(!empty($flagdirection))
                        $dirdefault=$flagdirection;
                    $query = $this->ModeloMenu->find()
                        ->where(['ModeloMenu.trash' => 0])
                        ->andWhere(['ModeloMenu.cestado_id' => 1])
                        ->order([$field=>$direction])
                        ->limit($paginacion);

                }else {

                    $query = $this->ModeloMenu->find()
                        ->where(['ModeloMenu.trash' => 0])
                        ->andWhere(['ModeloMenu.cestado_id' => 1])
                        ->order(['ModeloMenu.alias'=>"asc"]);

                }
            }

            $query->contain('Cestados');
            $modelos= $this->Paginator->paginate($query);
            $menu= $this->loadModel('Menus')->find('list',
                ['fields' => array('Menus.alias', 'Menus.id'), 'recursive' => -1])
                ->join([
                    ['table' => 'menus',
                        'alias' => 'mn',
                        'type' => 'inner',
                        'conditions' => ['Menus.id = mn.menu_id']
                    ]])
                ->order(['Menus.alias'=>'ASC'])
            ->toArray();

          //     debug($modelos->toArray()[0]->modelofuncions[1]->funcion_id);
        /*   debug($menu->toArray());
            exit();*/


            $cestados = $this->Modelos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo, ["index",'edit']);
            $nav = $permisos->getRuta($this->modelo, null, $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);



            $this->set(compact('modelos','herramientas','controltools','nav','titulo','cestados','dirdefault','menu'));
            $this->set('_serialize', ['modelos']);
        }
    }

    public function vertodos() {
        unset($_SESSION["tabla[ModeloMenu]"]);

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
     * @param string|null $id Modelo id.
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
            if(!$valid->AccessRegisterView($this->modelo, $id, 'index',0)){
                return $this->redirect(['action'=>'index']);
            }

            $modelos = $this->Modelos->get($id,[
                'contain'=>['Cestados' => [
                    'strategy' => 'select', 'queryBuilder' => function ($q) {
                        return $q->order(['Cestados.id' =>'ASC'])->limit(1);}]]
            ]);


            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir']);

            $cestados = $this->Modelos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $userAut = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "view", $userAut['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $this->set('modelos', $modelos);

            $this->set('_serialize', ['modelos']);
            $this->set(compact("controltools","nav",'titulo','cestados'));
        }

    }
    public function valunique(){
        $campo = trim($_POST['campo']);
        $id = $_POST['id'];
        $tipo= $_POST['tipo'];
        $nombreCampo="";
        $flag=0;
        $msj="";
        switch($tipo){
            case 'modelo':
                $flag=1;
                $nombreCampo="nombre";
                $msj="El ";
                break;
            case 'alias':
                $flag=1;
                $nombreCampo=$tipo;
                $msj="El ";
                break;
            case 'etiqueta':
                $nombreCampo=$tipo;
                $flag=1;
                $msj="La ";
                break;
            case 'username':
                $flag=1;
                $nombreCampo="nombre de usuario";
                $msj="El ";
                break;
            default:
                $flag=0;
        }

        if($flag==1){
            $this->Modelos->recursive=-1;
            if($id != 0){
                $info = $this->Modelos->find("all")
                    ->where(['Modelos.'.$tipo=>$campo, 'Modelos.id !='=>$id, 'Modelos.trash'=>0]);
            }else{
                $info = $this->Modelos->find("all")
                    ->where(['Modelos.'.$tipo=>$campo, 'Modelos.trash'=>0]);
            }
            if($info->count()>0){
                echo json_encode(["error"=>1,"msj"=>$msj.$nombreCampo." de modelo  ya existe."]);
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
            $modelo = $this->Modelos->newEntity();

            if ($this->request->is('post')) {



                $modelo = $this->Modelos->patchEntity($modelo, $this->request->getData());

                $funciones=$this->loadModel('Funcions')->find();

                $menu_id=$this->request->getData()['items_id'];
                $cestado_id=$this->request->getData()['cestado_id'];
                $descripcion=$this->request->getData()['descripcion'];
                $nombre=$this->request->getData()['modelo'];
                $alias=$this->request->getData()['alias'];

                $modelo->usuario = $this->Auth->user('username');
                $modelo->created = date("Y-m-d H:i:s");
                if ($this->Modelos->save($modelo)) {

                    foreach ($funciones as $funcion)
                    {
                       $ModelofuncionTable = TableRegistry::get('Modelofuncions');
                       $Modelofuncion = $ModelofuncionTable->newEntity();

                        $Modelofuncion->modelo_id= $modelo->id;
                        $Modelofuncion->funcion_id= $funcion->id;
                        $Modelofuncion->cestado_id=$cestado_id;
                        $Modelofuncion->used=1;
                        $Modelofuncion->orden=$funcion->id;
                        $Modelofuncion->usuario = $this->Auth->user('username');
                        $Modelofuncion->created = date("Y-m-d H:i:s");

                       if ($ModelofuncionTable->save($Modelofuncion)) {

                            if ($funcion->id==2)
                            {
                                $MenuitemsTable = TableRegistry::get('Menuitems');
                                $Menuitems= $MenuitemsTable->newEntity();
                                $Menuitems->menu_id=$menu_id;
                                $Menuitems->nombre=$nombre;
                                $Menuitems->alias=$alias;
                                $Menuitems->modelofuncion_id=$Modelofuncion->id;
                                $Menuitems->descripcion=$descripcion;
                                $Menuitems->cestado_id=$cestado_id;
                                $Menuitems->usuario =$this->Auth->user('username');
                                $Menuitems->created = date("Y-m-d H:i:s");
                                if ($MenuitemsTable->save($Menuitems)) {

                                }
                            }
                        }
                    }

                    $_SESSION["modelo-save"]=1;
                    return $this->redirect(['action' => 'view',$modelo->id]);
                }else{
                    $_SESSION["cformdinamictipo-save"]=1;
                    return $this->redirect(['action' => 'add']);
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit']);
            $user = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "add",$user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $cestados = $this->Modelos->Cestados->find('list', ['limit' => 200])
                ->Where(['Cestados.trash ' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);


            $menu= $this->loadModel('Menus')->find('list',
                ['fields' => array('Menus.alias', 'Menus.id'), 'recursive' => -1])
                ->join([
                    ['table' => 'menus',
                        'alias' => 'mn',
                        'type' => 'inner',
                        'conditions' => ['Menus.id = mn.menu_id']
                    ]])
                ->order(['Menus.alias'=>'ASC']);

            $this->set(compact('modelo','controltools','nav','titulo','cestados','menu'));
            $this->set('_serialize', ['modelo']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Modelo id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {

            $idFuncion=$this->Modelos->Modelofuncions->find()
                ->where(['Modelofuncions.modelo_id' => $id])
                ->andwhere(['Modelofuncions.funcion_id'=>2])
                ->toArray()[0]->id;

                $repositorioMenuitems=$this->Modelos->Modelofuncions->Menuitems->find()
                    ->where(['Menuitems.modelofuncion_id' => $idFuncion])
                    ->toArray()[0];

            $idSubmenu=$repositorioMenuitems->menu_id;
            $idMenuitems=$repositorioMenuitems->id;

            $valid= new ValidacionesController();
            if(!$valid->AccessRegisterView($this->modelo, $id, 'index',0)){
                return $this->redirect(['action'=>'index']);
            }
            $modelos = $this->Modelos->get($id, [
                'contain'=>['Cestados' => [
                    'strategy' => 'select', 'queryBuilder' => function ($q) {
                        return $q->order(['Cestados.id' =>'ASC'])->limit(1);}]]
            ]);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $id_select=$this->request->getData('cestado_id');
                $valid= new ValidacionesController();


                if($valid->RegistrosIdValidos($id_select,$this->estadosId,'ccontactotipo-save',0)){
                    $modelos = $this->Modelos->patchEntity($modelos, $this->request->getData());
                    $modelos->modified = date("Y-m-d H:i:s");
                    $modelos->usuariomodif = $this->Auth->user('username');

                    $menu_id=$this->request->getData()['items_id'];
                    $cestado_id=$this->request->getData()['cestado_id'];
                    $descripcion=$this->request->getData()['descripcion'];
                    $nombre=$this->request->getData()['modelo'];
                    $alias=$this->request->getData()['alias'];

                    if ($this->Modelos->save($modelos)) {

                        $MenuitemsTable = TableRegistry::get('Menuitems');
                        $MenuitemsDelete=$MenuitemsTable->get($idMenuitems);

                        if ($MenuitemsTable->delete($MenuitemsDelete)) {
                            $Menuitems= $MenuitemsTable->newEntity();
                            $Menuitems->menu_id=$menu_id;
                            $Menuitems->nombre=$nombre;
                            $Menuitems->alias=$alias;
                            $Menuitems->modelofuncion_id=$idFuncion;
                            $Menuitems->descripcion=$descripcion;
                            $Menuitems->cestado_id=$cestado_id;
                            $Menuitems->usuario = $this->Auth->user('username');
                            $Menuitems->created = date("Y-m-d H:i:s");
                            if ($MenuitemsTable->save($Menuitems)) {

                            }
                        }



                        $_SESSION["modelos-save"]=1;
                        return $this->redirect(['action' => 'view',$id]);
                    }else{
                        $_SESSION["modelos-save"]=0;
                        return $this->redirect(['action' => 'view',$id]);
                    }
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir','edit']);
            $users = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "edit",$users['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            /*Elementos para select*/
            $cestados = $this->Modelos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andwhere(['Cestados.id IN'=>[1,2]]);


            $menu= $this->loadModel('Menus')->find('list',
                ['fields' => array('Menus.alias', 'Menus.id'), 'recursive' => -1])
                ->join([
                    ['table' => 'menus',
                        'alias' => 'mn',
                        'type' => 'inner',
                        'conditions' => ['Menus.id = mn.menu_id']
                    ]])
                ->order(['Menus.alias'=>'ASC']);





            $idMenud=$this->Modelos->Modelofuncions->Menuitems->Menus->find()
                ->where(['Menus.id' => $idSubmenu])
                ->toArray()[0]->menu_id;

            $subMenu= $this->loadModel('Menus')->find('list')
                ->select(['id' => 'mns.id', 'alias' => 'mns.alias'])
                ->join([
                    ['table' => 'menus',
                        'alias' => 'mns',
                        'type' => 'inner',
                        'conditions' => ['Menus.id = mns.menu_id']
                    ]
                ])
                ->where(['mns.menu_id' =>$idMenud])
                ->order(['mns.alias'=>'ASC'])
                ->toArray();

         $this->set(compact('modelos','controltools','nav',"titulo", 'cestados','menu','idMenud','subMenu','idSubmenu'));
            $this->set('_serialize', ['modelos']);
        }

       /* $modelo = $this->Modelos->get($id, [
            'contain' => ['Users']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $modelo = $this->Modelos->patchEntity($modelo, $this->request->getData());
            if ($this->Modelos->save($modelo)) {
                $this->Flash->success(__('The modelo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The modelo could not be saved. Please, try again.'));
        }
        $cestados = $this->Modelos->Cestados->find('list', ['limit' => 200]);
        $users = $this->Modelos->Users->find('list', ['limit' => 200]);
        $this->set(compact('modelo', 'cestados', 'users'));
        $this->set('_serialize', ['modelo']);*/

    }

    /**
     * Delete method
     *
     * @param string|null $id Modelo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */

    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete'))
        {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            parent::callprocedureDelete('dbpriv',$this->modelo,$id,'ModeloDele');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function getMenuItems()
    {
        $subMenu= $this->loadModel('Menus')->find('all')
            ->select(['id' => 'mns.id', 'alias' => 'mns.alias'])
            ->join([
                ['table' => 'menus',
                    'alias' => 'mns',
                    'type' => 'inner',
                    'conditions' => ['Menus.id = mns.menu_id']
                ]
            ])
            ->where(['mns.menu_id' =>trim($_POST['mn'])])
            ->order(['mns.alias'=>'ASC'])
        ->toArray();

        echo json_encode($subMenu);
        $this->autoRender=false;
    }
}
