<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;

/**
 * Privmodelos Controller
 *
 * @property \App\Model\Table\PrivmodelosTable $Privmodelos
 *
 * @method \App\Model\Entity\Privmodelo[] paginate($object = null, array $settings = [])
 */
class PrivmodelosController extends AppController
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
        $this->modelo = "Privmodelos";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";
        $this->estadosId = [1,2];
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($id_perfil=null)
    {
        $this->paginate = [
            'contain' => ['Modelofuncions', 'Perfils']
        ];
        $privmodelos = $this->paginate($this->Privmodelos);

        $this->set(compact('privmodelos'));
        $this->set('_serialize', ['privmodelos']);
        $permisos = new VerificacionPermisosController();
        $user = $this->Auth->user();
        $perfil_search=$user['perfil_id'];
        if(!empty($id_perfil)) $perfil_search=$id_perfil;
        $controltools=$permisos->getControlTool($this->modelo,["index"]);
        $nav = $permisos->getRuta($this->modelo,null,$user['perfil_id']);
        $titulo = $permisos->getTitle($this->modelo);
        /***LISTADO DE PERFILES**/
        $perfiles = $this->Privmodelos->Perfils->find()
            ->select(['Perfils.id','Perfils.nombre'])
            ->where(['Perfils.trash'=>0])
            ->order(['Perfils.nombre'=>'ASC'])
            ->all();
        //PRIMER PERFIL

        $funciones = $this->Privmodelos->Modelofuncions->Funcions->find()
            ->select(['Funcions.id','Funcions.funcion','Funcions.alias'])
            ->where(['Funcions.trash'=>0])
            ->order(['Funcions.alias'=>"ASC"])
            ->all();
        /****MENUS****/
        $this->loadModel("Menus");
        /*$menus = $this->Menus->find()
                                ->select(['Menus.id','Menus.alias'])
                                ->where(['Menus.trash'=>0,'Menus.menu_id'=>0])
                                ->order(['Menus.alias'=>"ASC"])
                                ->all();*/
        $menus = $this->Menus->find()
            ->select(['Menus.id','Menus.alias'])
            ->join([
                [
                    'table'=>'menus',
                    'alias'=>'m',
                    'type'=>'INNER',
                    'conditions'=>['Menus.id = m.menu_id']
                ],
                [
                    'table'=>'menuitems',
                    'alias'=>'mi',
                    'type'=>'INNER',
                    'conditions'=>['mi.menu_id = m.id']
                ],
                [
                    'table'=>'modelofuncions',
                    'alias'=>'mf',
                    'type'=>'INNER',
                    'conditions'=>['mf.id = mi.modelofuncion_id']
                ],
                [
                    'table'=>'privmodelos',
                    'alias'=>'pm',
                    'type'=>'INNER',
                    'conditions'=>['pm.modelofuncion_id=mf.id']
                ]])
            ->where(['pm.perfil_id'=>$perfil_search])
            ->order(['Menus.alias'=>"ASC"])
            ->group(['Menus.id'])
            ->all();
        $submenus=[];
        foreach ($menus as $item){
            $sql = $this->Menus->find()
                ->select(['Menus.id','Menus.alias'])
                ->join([
                    [
                        'table'=>'menuitems',
                        'alias'=>'mi',
                        'type'=>'INNER',
                        'conditions'=>['mi.menu_id = Menus.id']
                    ],
                    [
                        'table'=>'modelofuncions',
                        'alias'=>'mf',
                        'type'=>'INNER',
                        'conditions'=>['mf.id = mi.modelofuncion_id']
                    ],
                    [
                        'table'=>'privmodelos',
                        'alias'=>'pm',
                        'type'=>'INNER',
                        'conditions'=>['pm.modelofuncion_id=mf.id']
                    ]])
                ->where(['Menus.trash'=>0,'Menus.menu_id'=>$item->id, 'pm.perfil_id'=>$perfil_search])
                ->order(['Menus.alias'=>"ASC"])
                ->group(['Menus.id'])
                ->all();
            $submenus[$item->id] = $sql;
        }
        /**********************/
        $nivel0 = $this->Privmodelos->find()
                                    ->select(['model.id','model.alias','m.id','m.alias'])
                                    ->join([
                                        [
                                            'table'=>'modelofuncions',
                                            'alias'=>'mf',
                                            'type'=>'INNER',
                                            'conditions'=>['Privmodelos.modelofuncion_id = mf.id']
                                        ],
                                        [
                                            'table'=>'modelos',
                                            'alias'=>'model',
                                            'type'=>'INNER',
                                            'conditions'=>['mf.modelo_id = model.id']
                                        ],
                                        [
                                            'table'=>'funcions',
                                            'alias'=>'f',
                                            'type'=>'INNER',
                                            'conditions'=>['mf.funcion_id = f.id']
                                        ],
                                        [
                                            'table'=>'menuitems',
                                            'alias'=>'mi',
                                            'type'=>'INNER',
                                            'conditions'=>['mi.modelofuncion_id = mf.id']
                                        ],
                                        [
                                            'table'=>'menus',
                                            'alias'=>'m',
                                            'type'=>'INNER',
                                            'conditions'=>['m.id = mi.menu_id']
                                        ],
                                    ])
                                ->where(['Privmodelos.perfil_id'=>$perfil_search])
                                ->all();
        $modelosFunc=[];
        $cont=0;
        foreach($nivel0 as $item){
            $sqlFunc = $this->Privmodelos->find()
                                        ->join([
                                            [
                                                'table'=>'modelofuncions',
                                                'alias'=>'mf',
                                                'type'=>'INNER',
                                                'conditions'=>['Privmodelos.modelofuncion_id = mf.id']
                                            ],
                                            [
                                                'table'=>'modelos',
                                                'alias'=>'model',
                                                'type'=>'INNER',
                                                'conditions'=>['mf.modelo_id = model.id']
                                            ],
                                            [
                                                'table'=>'funcions',
                                                'alias'=>'f',
                                                'type'=>'INNER',
                                                'conditions'=>['f.id = mf.funcion_id']
                                            ]
                                        ])
                                        ->select(['model.alias','mf.id','mf.funcion_id','Privmodelos.id','Privmodelos.allow','f.alias','mf.modelo_id'])
                                        ->where(['Privmodelos.perfil_id'=>$perfil_search,'mf.modelo_id'=>$item->model['id']])
                                        ->order(['f.alias'=>'ASC'])
                                        ->all();
            $modelosFunc[$item->m['id']][$cont] = $sqlFunc;
            $cont++;
        }

        /*Datos para Sección Datos*/
        $option_adming=array(0=>array('id'=>1,
                                     'nombre'=>'Planificación Extra'),
                            1=>array('id'=>2,
                                'nombre'=>'Territorios'),
                            2=>array('id'=>3,
                                'nombre'=>'Convocatorias'),
                            3=>array('id'=>4,
                                'nombre'=>'Proyectos')
        );

        $option_personalizacion=array(0=>'Todos',
                                      1=>'Propios',
                                      2=>'Personalizado');

        $this->set(compact('perfiles','controltools','nav','titulo','funciones','menus','submenus','modelosFunc','perfil_search','option_adming','option_personalizacion'));
    }

    public function changeprivilegios(){
        $this->autoRender=false;
        if ($this->request->is(['get','post'])) {
            $array=array();
            $modelfuncion_id=json_decode($this->request->query['modelofuncion_id']);
            $perfil_id=$this->request->query['perfil_id'];
            $reload=false;
            if(count($modelfuncion_id)==0){
                $array=array('resp'=>true,
                            'msj'=>'No existe información nueva que almacenar.');
            }
            foreach ($modelfuncion_id as $key) {
                $privmodel=$this->Privmodelos->find()
                ->where(['modelofuncion_id'=>$key[0]])
                ->andwhere(['perfil_id'=>$perfil_id])
                ->andWhere(['trash'=>0])
                ->toArray();
                if(count($privmodel)>0){
                    $privmodel=$this->Privmodelos->get($privmodel[0]->id);
                    $privmodel->allow=$key[1];
                    $privmodel->modified=date("Y-m-d H:i:s");
                    $privmodel->usuariomodif=$this->Auth->user('username');
                    if($this->Privmodelos->save($privmodel)){
                        $reload=true;
                        $array=array('resp'=>true,
                            'msj'=>'Privilegio Guardado Exitosamente.',
                            'reload'=>$reload
                            );
                    }else{
                        $array=array('resp'=>false,
                            'msj'=>'No es posible almacenar la información. Inténtalo nuevamente.');
                    }
                }else{
                    $array=array('resp'=>false,
                        'msj'=>'No es posible almacenar la información. Inténtalo nuevamente.');
                }  
            }
            if($reload) 
                $_SESSION['menus']=parent::menu($this->Auth->user('perfil_id'));
            echo json_encode($array);
        }
    }

    public function lastFuncionModel($id){
        $this->loadModel('ModeloFuncions');
        $privmodel=$this->Privmodelos->get($id,[
                        'contain'=>['Modelofuncions']
                        ]);
        $modelo_funcion=$this->ModeloFuncions->find('list',[
            'keyField'=>'s',
            'valueField'=>'id'
            ])
        ->where(['modelo_id'=>$privmodel->modelofuncion->modelo_id])
        ->andwhere(['trash'=>0])
        ->toArray();
        $privmodel=$this->Privmodelos->find()
        ->where(['modelofuncion_id IN'=>$modelo_funcion])
        ->andwhere(['trash'=>0])
        ->andwhere(['allow'=>1])
        ->count();
        return $privmodel;

    }

    public function savepersonalizacion(){
        $this->autoRender=false;
        if ($this->request->is(['get','post'])) {
            $array=array();
            $option_radioadming=$this->request->query['option_radioadming'];
            $option_selectcustom=$this->request->query['option_selectcustom'];
            $perfil_id=$this->request->query['perfil_id'];
            $array=array('resp'=>true,
                'msj'=>'Datos Guardados Exitosamente.');
            echo json_encode($array);
        }
    }

    public function savedetallepersonalizacion(){
        $this->autoRender=false;
        if ($this->request->is(['get','post'])) {
            $array=array();
            $option_radioadming=$this->request->query['option_radioadming'];
            $option_selectcustom=$this->request->query['option_selectcustom'];
            $option_selectdetailcustom=$this->request->query['option_selectdetailcustom'];
            $perfil_id=$this->request->query['perfil_id'];
            $array=array('resp'=>true,
                'msj'=>'Datos Guardados Exitosamente.');
            echo json_encode($array);
        }
    }

    public function getopcionesdetallepersonalizacion(){
        $this->autoRender=false;
        if ($this->request->is(['get','post'])) {
            $array=array();
            $option_radioadming=$this->request->query['option_radioadming'];
            $option_selectcustom=$this->request->query['option_selectcustom'];
            $perfil_id=$this->request->query['perfil_id'];
            $array=array('resp'=>true,
                'data'=>array(0=>array('id'=>1, 'nombre'=>'San Francisco Menéndez'),
                    1=>array('id'=>2, 'nombre'=>'Tacuba'),
                    2=>array('id'=>3, 'nombre'=>'Jujutla'),
                    3=>array('id'=>4, 'nombre'=>'Acajutla'),
                    4=>array('id'=>5, 'nombre'=>'Concepción de Ataco'),
                    5=>array('id'=>6, 'nombre'=>'Ahuachapán')
                )
            );
            echo json_encode($array);
        }
    }

    /**
     * View method
     *
     * @param string|null $id Privmodelo id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($modelo_id = null, $perfil_id=null)
    {
        $this->loadModel('Perfils');
        $this->loadModel('Modelofuncions');
        $this->loadModel('Menuitems');
        $modelosfuncions=$this->Modelofuncions->find('list',[
            'keyField'=>'s',
            'valueField'=>'id'
        ])
            ->where(['modelo_id'=>$modelo_id])
            ->toArray();

        $menuitem=$this->Menuitems->find('list',[
            'keyField'=>'0',
            'valueField'=>'menu_id'
        ])
            ->where(['modelofuncion_id IN'=>$modelosfuncions])
            ->toArray();

        $bitacora_date_min=$this->Privmodelos->find()
            ->where(['modelofuncion_id IN'=>$modelosfuncions])
            ->order(['created'=>'ASC'])
            ->first();

        $bitacora_date_max=$this->Privmodelos->find()
            ->where(['modelofuncion_id IN'=>$modelosfuncions])
            ->order(['modified'=>'DESC'])
            ->first();
        $bitacora=(object) array('usuario'=>$bitacora_date_min->usuario,
                                'created'=>$bitacora_date_min->created,
                                'usuariomodif'=>$bitacora_date_max->usuariomodif,
                                'modified'=>$bitacora_date_max->modified);

        $recursos=$this->recursiveMenu($menuitem[0]);
        $sqlFunc = $this->Privmodelos->find()
            ->join([
                [
                    'table'=>'modelofuncions',
                    'alias'=>'mf',
                    'type'=>'INNER',
                    'conditions'=>['Privmodelos.modelofuncion_id = mf.id']
                ],
                [
                    'table'=>'modelos',
                    'alias'=>'model',
                    'type'=>'INNER',
                    'conditions'=>['mf.modelo_id = model.id']
                ],
                [
                    'table'=>'funcions',
                    'alias'=>'f',
                    'type'=>'INNER',
                    'conditions'=>['f.id = mf.funcion_id']
                ]
            ])
            ->select(['model.alias','mf.id','mf.funcion_id','Privmodelos.id','Privmodelos.allow','f.alias','Privmodelos.created','Privmodelos.usuario','Privmodelos.modified','Privmodelos.usuariomodif'])
            ->where(['Privmodelos.perfil_id'=>$perfil_id,'mf.modelo_id'=>$modelo_id])
            ->order(['f.alias'=>'ASC'])
            ->all();
        $perfil = $this->Perfils->get($perfil_id);
        $cont=1;
        $this->set('perfil', $perfil);
        $this->set('_serialize', ['privmodelo']);
        $permisos = new VerificacionPermisosController();
        $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir','edit','add']);
        $user = $this->Auth->user();
        $nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
        $titulo = $permisos->getTitle($this->modelo);
        $this->set(compact("controltools","nav",'titulo','perfil_id','sqlFunc','cont','recursos','bitacora','modelo_id'));
    }

    /*Función recursiva para obtener menu y/o submenu*/
    public function recursiveMenu($menu_id, $array=array()){
        $this->loadModel('Menus');
        $menu_record=$this->Menus->get($menu_id);
        array_push($array, $menu_record->alias);
        if($menu_record->menu_id==0) return $array;
        else return $this->recursiveMenu($menu_record->menu_id, $array);

    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $privmodelo = $this->Privmodelos->newEntity();
        if ($this->request->is('post')) {
            $privmodelo = $this->Privmodelos->patchEntity($privmodelo, $this->request->getData());
            if ($this->Privmodelos->save($privmodelo)) {
                $this->Flash->success(__('The privmodelo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The privmodelo could not be saved. Please, try again.'));
        }
        $modelofuncions = $this->Privmodelos->Modelofuncions->find('list', ['limit' => 200]);
        $perfils = $this->Privmodelos->Perfils->find('list', ['limit' => 200]);
        $this->set(compact('privmodelo', 'modelofuncions', 'perfils'));
        $this->set('_serialize', ['privmodelo']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Privmodelo id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $privmodelo = $this->Privmodelos->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $privmodelo = $this->Privmodelos->patchEntity($privmodelo, $this->request->getData());
            if ($this->Privmodelos->save($privmodelo)) {
                $this->Flash->success(__('The privmodelo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The privmodelo could not be saved. Please, try again.'));
        }
        $modelofuncions = $this->Privmodelos->Modelofuncions->find('list', ['limit' => 200]);
        $perfils = $this->Privmodelos->Perfils->find('list', ['limit' => 200]);
        $this->set(compact('privmodelo', 'modelofuncions', 'perfils'));
        $this->set('_serialize', ['privmodelo']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Privmodelo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $privmodelo = $this->Privmodelos->get($id);
        if ($this->Privmodelos->delete($privmodelo)) {
            $this->Flash->success(__('The privmodelo has been deleted.'));
        } else {
            $this->Flash->error(__('The privmodelo could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
