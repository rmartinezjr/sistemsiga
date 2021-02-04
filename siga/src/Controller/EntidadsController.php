<?php
namespace App\Controller;

use App\Model\Entity\Entidadred;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Entidads Controller
 *
 * @property \App\Model\Table\EntidadsTable $Entidads
 *
 * @method \App\Model\Entity\Entidad[] paginate($object = null, array $settings = [])
 */
class EntidadsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Entidads";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";

        $cestados = new CestadosController();
        // Se obtienen los estados activo e inactivo de la configuracion general
        $this->estadosId = $cestados->getEstados(false);

        // Se obtiene el estado activo de la configuracion general
        $this->estadoActivo = $cestados->getEstados(true);

        // Se obtienen los tipos de documento para entidades de la configuacion general
        $docidtipos = new CdocidtiposController();
        $this->docidtipos = $docidtipos->getTipoDocumento('Entidads');

        //cargando modelos para la carga de datos
        $this->loadModel('Dataforms');
        $this->loadModel('Formdinamics');
        $this->loadModel('Formdinamicwfs');
        $this->loadModel('Wfetapas');
        $this->loadModel('Perfils');
        $this->loadModel('Cdatotipos');
        $this->loadModel('entidadfds');

        $valid= new JsonController();
        $this->estado_archivado=$valid->preferencesLevel1("Planificación", "archivado");
    }

    /**
     * Index method
     * @param string|null $id Contacto id.
     *
     * @return \Cake\Http\Response|void
     */
    public function index($id = null, $band = null)
    {
        $paginacion = ($band==null)?20:1000000;
        $query = $this->Entidads->find();
        if(count($this->request->getData()) == 0) {
            if(is_null($id)) {
                if(isset($_SESSION["tabla[$this->modelo]"])) {
                    $data = $_SESSION["tabla[$this->modelo]"]['data'];
                    $busqueda = new EntidadcontactosController();
                    $query = $busqueda->realizarBusqueda($data, $this->modelo);
                    $query->andWhere([$this->modelo . '.trash' => 0])
                          ->order([$this->modelo . '.nombre'=>"asc"])
                          ->limit($paginacion)
                          ->contain(['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados']);
                } else {
                    $query->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$this->modelo . '.nombre'=>"asc"])
                        ->limit($paginacion)
                        ->contain(['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados']);
                }
            } else {
                unset($_SESSION["tabla[$this->modelo]"]);
                $arrayEntidades = $this->filtroentidades($id);

                if(count($arrayEntidades) > 0) {
                    $query->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Entidads.id IN' => $arrayEntidades])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$this->modelo . '.nombre'=>"asc"])
                        ->limit($paginacion)
                        ->contain(['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados']);
                } else {
                    $query->where([$this->modelo . '.id' => 0]);
                }
            }
        } else {
            $data = $this->request->getData();

            if(is_null($id)) {
                if($data['load'] == 'ordenamiento') {
                    if(isset($_SESSION["tabla[$this->modelo]"])) {
                        $parametros = $_SESSION["tabla[$this->modelo]"]['data'];
                        $busqueda = new EntidadcontactosController();
                        $query = $busqueda->realizarBusqueda($parametros, $this->modelo);
                        $query->andWhere([$this->modelo . '.trash' => 0])
                            ->order([$data['modelo'] . '.' . $data['order'] => $data['tipoorder']])
                            ->limit($paginacion)
                            ->contain(['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados']);
                    } else {
                        $query->where([$this->modelo . '.trash' => 0])
                            ->andWhere(['Cestados.id' => 1])
                            ->order([$data['modelo'] . '.' . $data['order'] => $data['tipoorder']])
                            ->limit($paginacion)
                            ->contain(['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados']);
                    }
                } else if($data['load'] == 'busqueda') {
                    $busqueda = new EntidadcontactosController();
                    $query = $busqueda->realizarBusqueda($data, $this->modelo);
                    $query->andWhere([$this->modelo . '.trash' => 0])
                          ->order([$this->modelo . '.nombre'=>"asc"])
                          ->limit($paginacion)
                          ->contain(['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados']);
                }
            } else {
                unset($_SESSION["tabla[$this->modelo]"]);
                $arrayEntidades = $this->filtroentidades($id);

                if(count($arrayEntidades) > 0) {
                    $query->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Entidads.id IN' => $arrayEntidades])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$data['modelo'] . '.' . $data['order'] => $data['tipoorder']])
                        ->limit($paginacion)
                        ->contain(['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados']);
                } else {
                    $query->where([$this->modelo . '.id' => 0]);
                }
            }
        }
        $entidads = $this->Paginator->paginate($query);
        $cestados = $this->Entidads->Cestados->find('list')
            ->where(['Cestados.trash' => 0])
            ->andWhere(['Cestados.id IN' => $this->estadosId]);

        $user = $this->Auth->user();
        $permisos = new VerificacionPermisosController();
        $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);

        $this->set(compact('entidads', 'cestados', 'herramientas'));
        $this->set('_serialize', ['entidads']);
    }

    public function vertodos() {
        unset($_SESSION["tabla[$this->modelo]"]);
        $this->redirect(array('controller'=> 'entidadcontactos', 'action'=> "index"));
        $this->autoRender=false;
    }

    public function filtroentidades($id)
    {
        $this->loadModel('Entidadcontactos');
        $contactos = $this->Entidadcontactos->find()
            ->select(['Entidadcontactos.entidad_id'])
            ->where(['Entidadcontactos.contacto_id' => $id])
            ->all();

        $arrayEntidades = [];
        foreach ($contactos as $value) {
            array_push($arrayEntidades, $value->entidad_id);
        }

        return $arrayEntidades;
    }

    /*Función que indica si un perfil puede generar nueva carga de datos*/
    public function generarAccionViewInicial($formdinamic_id){
        $this->loadModel('wftransicions');
        /*Obtiendo wf del formulario dinámico*/
        $workflow=$this->Formdinamicwfs->find()
            ->where(['formdinamic_id'=>$formdinamic_id])
            ->andWhere(['vinculo'=>1])
            ->andWhere(['trash'=>0])
            ->toArray();

        /*Obteniendo etapa inicial del wf del formulario*/
        $etapa_inicial=$this->wftransicions->find()
            ->where(['workflow_id'=>$workflow[0]->workflow_id])
            ->andWhere(['wfetapaini'=>0])
            ->andWhere(['trash'=>0])
            ->toArray();

        $accion_style0=$this->accionPrivilegios($etapa_inicial[0]->wfetapafin,true);
        if(strcmp($accion_style0,'edit')==0) return true;
        else return false;
    }

    /**
     * View method
     *
     * @param string|null $id Entidad id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Entidads->exists(['Entidads.id' => $id, 'Entidads.trash' => 0])) {
                $entidad = $this->Entidads->get($id, [
                    'contain' => ['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados', 'Entidadcontactos']
                ]);

                $this->loadModel('Contactos');
                $contactos = [];
                foreach ($entidad->entidadcontactos as $entidadcontacto) {
                    $contacto = $this->Contactos->get($entidadcontacto->contacto_id, [
                        'contain' => ['Cdocidtipos', 'Ccontactotipos', 'Cestados']
                    ]);
                    array_push($contactos, $contacto);
                }

                //Obteniendo formularios asignados
                $json= new JsonController();
                $this->loadModel('Entidadfds');
                $formasig=$this->Entidadfds->find()
                    ->Where(['Entidadfds.centidadtipo_id' => $entidad->centidadtipo_id])
                    ->andwhere(['Entidadfds.trash'=>0])
                    ->andwhere(['Entidadfds.vinculo'=>1])
                    ->andWhere(['Formdinamics.trash'=>0])
                    ->contain(['Formdinamics']);

                $arraytipo=[];
                $arraymulti=[];
                $id_dataform=[];
                $functions=array();
                $view_inicial=[];
                $permitir_carga=0;//variable que indica si la carga de datos esta en etapa fin con multireg=true y paralelo=false
                $carga=[];//variable que contiene el estado en el que se encuentra la carga
                $cont_carga=1;//conteo para formularios de multiregistro
                //Código para identificar si el formulario asignado es multiregristro y si se ha llenado formulario, para así establecer su estilo correspondiente
                //debug($formasig->toArray());die();
                foreach ($formasig as $key ) {
                    $dataformasig=$this->Dataforms->find()
                        ->Where(['formdinamic_id'=>$key->formdinamic_id])
                        //->andWhere(['registro_id'=>$id])
                        //->andWhere(['modelo'=>$this->modelo])
                        ->andWhere(['trash'=>0]);

                    $view_inicial[$key->id]=$this->generarAccionViewInicial($key->formdinamic_id);
                    //Multireg=false sin registro de formulario lleno o llenandose
                    if(($key->formdinamic->params['FormDinamic']['items'][1]['value']==false || $key->formdinamic->params['FormDinamic']['items'][1]['value']=='0') && $dataformasig->count()==0){
                        $arraytipo[$key->id]="styleF0";
                        $id_dataform[$key->id]=0;
                        if($key->formdinamic->cestado_id==$this->estado_archivado)$functions[$key->id][0]='form_archivado';
                        else $functions[$key->id][0]='#';
                    }
                    //Multireg=false con registro de formulario lleno o llenandose
                    elseif(($key->formdinamic->params['FormDinamic']['items'][1]['value']==false || $key->formdinamic->params['FormDinamic']['items'][1]['value']=='0') && $dataformasig->count()!=0){
                        $arraytipo[$key->id]="styleF1";
                        foreach($dataformasig as $data){
                            if($this->cargaDatosPerfilComun($data->id)) $functions[$key->id][$data->id]=$this->accionPrivilegios($data->wfetapa_id);
                            else $functions[$key->id][$data->id]="show/";

                            if(strcmp($functions[$key->id][$data->id],'viewFormDinamics/')==0 || strcmp($functions[$key->id][$data->id],'observations/')==0){ //si el formulario es archivado dirigir a show si la acción es viewFormDinamics ó observations
                                if($key->formdinamic->cestado_id==$this->estado_archivado)
                                    $functions[$key->id][$data->id]='form_archivado';
                            }

                            $carga[$key->id][$data->id]=$this->Wfetapas->get($data->wfetapa_id,[
                                'contain' => ['Cestados']
                            ]);
                        }
                        $dataformasig=$dataformasig->toArray();
                        $id_dataform[$key->id]=$dataformasig[0]->id;
                        $arraymulti[$key->id]=$dataformasig[0];
                    }
                    //Multireg=true sin registro de formulario lleno o llenandose
                    elseif(($key->formdinamic->params['FormDinamic']['items'][1]['value']==true || $key->formdinamic->params['FormDinamic']['items'][1]['value']=='1') && $dataformasig->count()==0){
                        $arraytipo[$key->id]="styleF0";
                        $id_dataform[$key->id]=0;
                        if($key->formdinamic->cestado_id==$this->estado_archivado)$functions[$key->id][0]='form_archivado';
                        else $functions[$key->id][0]='#';
                    }
                    elseif(($key->formdinamic->params['FormDinamic']['items'][1]['value']==true || $key->formdinamic->params['FormDinamic']['items'][1]['value']=='1') && $dataformasig->count()!=0){
                        $arraytipo[$key->id]="styleT1";
                        $arraymulti[$key->id]=$dataformasig;
                        foreach($dataformasig as $data){
                            if($this->cargaDatosPerfilComun($data->id)) $functions[$key->id][$data->id]=$this->accionPrivilegios($data->wfetapa_id);
                            else $functions[$key->id][$data->id]="show/";

                            if($key->formdinamic->cestado_id==$this->estado_archivado)
                                $functions[$key->id][0]='form_archivado';//para enlace a nueva carga si el form esta archivado
                            else
                                $functions[$key->id][0]='#';

                            if(strcmp($functions[$key->id][$data->id],'viewFormDinamics/')==0 || strcmp($functions[$key->id][$data->id],'observations/')==0){
                                if($key->formdinamic->cestado_id==$this->estado_archivado) //si el formulario es archivado dirigir a show si la acción es viewFormDinamics
                                    $functions[$key->id][$data->id]='form_archivado';
                            }

                            $carga[$key->id][$data->id]=$this->Wfetapas->get($data->wfetapa_id,[
                                'contain' => ['Cestados']
                            ]);
                        }
                        $dataformasig=$dataformasig->toArray();
                        $id_dataform[$key->id]=$dataformasig[0]->id;

                        if($key->formdinamic->params['FormDinamic']['items'][2]['value']==false || $key->formdinamic->params['FormDinamic']['items'][2]['value']=='0'){
                            //función para conocer si al tener paralelo a false y etapa finalizado generar una nueva carga de datos
                            $dataformasigAux=$this->Dataforms->find()
                                ->Where(['formdinamic_id'=>$key->formdinamic_id])
                                //->andWhere(['registro_id'=>$id])
                                //->andWhere(['modelo'=>$this->modelo])
                                ->andWhere(['trash'=>0])
                                ->order(['id'=>'DESC'])
                                ->first();
                            $cestado=$this->Wfetapas->get($dataformasigAux->wfetapa_id,
                                ['contain'=>['cestados']]);
                            $permitir_carga=$cestado->fin;
                        }else{
                            $permitir_carga=1;
                        }
                    }
                }

                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools=$permisos->getControlToolMultipleModels($this->modelo ,["exportPdf",'imprimir'], 'Entidadcontactos');
                $nav = $permisos->getRuta('Entidadcontactos', "view", $user['perfil_id'], 'Entidads');
                $titulo = $permisos->getTitle($this->modelo);
                $function="viewFormDinamics/";

                /** Inicio de  Red de Entidades */
                $entidads = $this->Entidads->find('list')->where(['Entidads.trash' => 0])->andWhere(['Entidads.cestado_id' => $this->estadoActivo])->toArray();

                $this->loadModel('Entidadreds');
                $entidades_madre=$this->Entidadreds->find()
                    ->Where(['Entidadreds.vinculo' => 1])
                    ->andWhere(['Entidadreds.entidadhija' => $id])
                    ->all();

                $entidadreds_hijas=$this->Entidadreds->find()
                    ->Where(['Entidadreds.vinculo' => 1])
                    ->andWhere(['Entidadreds.entidadmadre' => $id])
                    ->all();

                $entidades_hijas_id = [];
                foreach ($entidadreds_hijas as $key => $entidad_hija) {
                    array_push($entidades_hijas_id, $entidad_hija->entidadhija);
                }

                if(count($entidades_hijas_id) > 0) {
                    $entidades_hijas = $this->Entidads->find()
                        ->where(['Entidads.trash' => 0])
                        ->andWhere(['Entidads.cestado_id' => $this->estadoActivo])
                        ->andWhere(['Entidads.id IN' => $entidades_hijas_id])
                        ->contain(['Centidadtipos', 'Cestados'])
                        ->all();
                } else {
                    $entidades_hijas = [];
                }
                /** Fin de  Red de Entidades */

                $this->set(compact('entidad', 'entidads', 'contactos', 'entidades_madre', 'entidades_hijas', 'controltools', 'nav', 'titulo','formasig','arraytipo','arraymulti','function','id_dataform','acciones','functions','view_inicial','permitir_carga','carga','cont_carga'));
                $this->set('_serialize', ['entidad']);
            } else {
                $this->Flash->erroracceso(__('La entidad no existe o ha sido eliminada'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
            }
        }
    }

    //Función para identificar si un perfil ya realiza la carga al formulario
    //return true -> el usuario logeado podrá realizar carga
    //return false -> el usuario logeado no podrá relizar carga
    public function cargaDatosPerfilComun($dataform_id){
        $this->loadModel('Users');
        $user=$this->Dataforms->get($dataform_id);
        if(strcmp($user->usuario,$this->Auth->user('username'))==0){
            return true;
        }else{
            $perfil=$this->Users->find()
                ->where(['username'=>$user->usuario])
                ->andwhere(['trash'=>0])
                ->toArray();
            if($perfil[0]->perfil_id==$this->Auth->user('perfil_id')){
                return false;
            }else{
                return true;
            }
        }
    }

    /*Función para obtener aquellas etapas en la que el perfil posea privilegios*/
    public function filtrarEtapasPorPrivilegios($acciones){
        $this->loadModel('Wftransicionprivs');
        $privilegios=$this->Wftransicionprivs->find()//obtenemos todas las transiciones según perfil y que posea privilegios (permitido=1)
        ->Where(['perfil_id'=>$this->Auth->user('perfil_id')])
            ->andWhere(['permitido'=>1])
            ->andWhere(['trash'=>0])
            ->toArray();
        $filtrado_acciones=array();//array de etapas ya filtradas según privilegios
        for($est=0; $est<count($acciones); $est++){
            $flag_transicion=0;//bandera para identificar que posea privilegio para esta etapa
            for($pri=0; $pri<count($privilegios); $pri++){//comparando que el id de transición de etapa este permitido para el usuario
                if($acciones[$est]['wft_id']==$privilegios[$pri]->wftransicion_id) $flag_transicion=1;
            }
            if($flag_transicion){//generando estructura del nuevo array (estructura igual al array $acciones)
                array_push($filtrado_acciones,array('id'=>$acciones[$est]['id'],
                    'nombre'=>$acciones[$est]['nombre'],
                    'color_fondo'=>$acciones[$est]['color_fondo'],
                    'color_texo'=>$acciones[$est]['color_texo'],
                    'icon'=>$acciones[$est]['icon'],
                    'wft_id'=>$acciones[$est]['wft_id']));
            }
        }
        return $filtrado_acciones;
    }

    public function viewFormDinamics($id_formdinamic=null, $id_entidad=null, $id_dataform=null,$direction=null){
        if(!empty($id_dataform)){
            if(!$this->cargaDatosPerfilComun($id_dataform)) return $this->redirect(['action' => 'view',$id_entidad]);
        }

        if(empty($id_formdinamic))
            return $this->redirect(['controller' => 'entidads', 'action' => 'view', $id_entidad]);

        $formdinamic=$this->Formdinamics->get($id_formdinamic);
        if($formdinamic->cestado_id==$this->estado_archivado) //si el formulario es archivado dirigir a show si la acción es viewFormDinamics
            return $this->redirect(['controller' => 'entidads', 'action' => 'view', $id_entidad]);

        $acciones=[];
        //obteniendo wf del formdinamic a true
        $vinculo=$this->Formdinamicwfs->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andwhere(['vinculo'=>1])
            ->andwhere(['trash'=>0])
            ->count();

        $formdinamicwf=$this->Formdinamicwfs->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andwhere(['vinculo'=>1])
            ->andwhere(['trash'=>0])
            ->first();

        $etapas_form=$this->Wfetapas->find()
            ->select(['cestado_id'])
            ->Where(['workflow_id'=>$formdinamicwf->workflow_id])
            ->andWhere(['trash'=>0])
            ->all();

        $etapasid_form = [];
        foreach ($etapas_form as $etapa) {
            array_push($etapasid_form, $etapa->cestado_id);
        }

        if($vinculo==0)return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);
        $escritura=1;
        if(!empty($id_dataform)){
            $dataform=$this->Dataforms->get($id_dataform);
            $cestado=$this->Wfetapas->get($dataform->wfetapa_id,
                ['contain'=>['cestados']]);

            $_SESSION['fd_id_dataform']=$id_dataform;
            $perfil=$this->Perfils->get($this->Auth->user('perfil_id'));
            if(strcmp("Súper Administrador",$perfil->nombre)!=0){
                $function=$this->accionPrivilegios($dataform->wfetapa_id);
                if(strcmp($function,'viewFormDinamics/')!=0) return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);
            }
            $functionbd=$this->accionPrivilegios($dataform->wfetapa_id,true);
            switch($functionbd){
                case 'edit':
                    $escritura=1;
                    break;
                case 'editlimit':
                    $escritura=0;
                    break;
            }

            if(in_array($cestado->Cestados['id'], $etapasid_form)) {
                $acciones=parent::estadosWf($id_formdinamic,'formdinamicwfs','formdinamic_id','view',$cestado->Cestados['id']);
                $acciones=$this->filtrarEtapasPorPrivilegios($acciones);
            } else {
                $escritura=0;
            }
        }

        $pass=0;
        $access=0;
        $_SESSION['fd_if_formdinamic']=$id_formdinamic;
        $_SESSION['fd_id_entidad']=$id_entidad;
        if(empty($id_dataform)){
            unset($_SESSION['fd_id_dataform']);
            $id_dataform=0;
        }
        else $access=1;//variable para identificar es ques edición parcial
        $formdinamic=$this->Formdinamics->get($id_formdinamic);

        if(empty($id_dataform)){
            $dataformasig=$this->Dataforms->find()
                ->Where(['formdinamic_id'=>$id_formdinamic])
                /*->andWhere(['registro_id'=>$id_entidad])
                ->andWhere(['modelo'=>$this->modelo])*/
                ->andWhere(['trash'=>0]);
            //Multireg=false sin registro de formulario lleno o llenandose
            if($formdinamic->params['FormDinamic']['items'][1]['value']==false && $dataformasig->count()==0)$pass=1;
            //Multireg=true sin registro de formulario lleno o llenandose
            elseif($formdinamic->params['FormDinamic']['items'][1]['value']==true && $dataformasig->count()==0)$pass=1;
            elseif($formdinamic->params['FormDinamic']['items'][1]['value']==true && $formdinamic->params['FormDinamic']['items'][2]['value']==true && $dataformasig->count()!=0) $pass=1;
            elseif($formdinamic->params['FormDinamic']['items'][1]['value']==true && $formdinamic->params['FormDinamic']['items'][2]['value']==false && $dataformasig->count()!=0){
                $dataformasigAux=$this->Dataforms->find()
                    ->Where(['formdinamic_id'=>$id_formdinamic])
                    /*->andWhere(['registro_id'=>$id_entidad])
                    ->andWhere(['modelo'=>$this->modelo])*/
                    ->andWhere(['trash'=>0])
                    ->order(['id'=>'DESC'])
                    ->first();
                $cestado=$this->Wfetapas->get($dataformasigAux->wfetapa_id,
                    ['contain'=>['cestados']]);
                $pass=$cestado->fin;
            }
        }else{
            $pass=1;
        }
        if($pass==0)
            return $this->redirect(['action'=>'view',$id_entidad]);

        $tituloobj='Formulario';
        $entidad = $this->Entidads->get($id_entidad, [
            'contain' => ['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados', 'Entidadcontactos']
        ]);

        $permisos = new VerificacionPermisosController();
        $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","add",'imprimir','view','index', 'edit']);
        $user = $this->Auth->user();
        $nav = $permisos->getRuta('Entidadcontactos', "view", $user['perfil_id'],'entidads');
        $nav['nav'][0]['alias']="Entidades";
        $nav["complemento"][0]["alias"]=$formdinamic->nombre;
        $titulo = $permisos->getTitle($this->modelo);

        $model='Formseccions';
        $form= new LlenarFormDinamicsController();
        $datos=$form->crearForm($id_formdinamic,$model,$id_dataform,$direction);

        $cont=1;
        $countfp=1;
        /*Obteniendo estado carga de datos*/
        $carga=null;
        if(!empty($id_dataform)){
            $etapa=$this->Dataforms->get($id_dataform);
            $carga=$this->Wfetapas->get($etapa->wfetapa_id,[
                'contain' => ['Cestados']
            ]);
        }
        $this->set(compact("controltools","nav",'titulo','tituloobj','entidad','formdinamic','datos','cont','countfp','id_dataform','access','acciones','escritura','carga'));
        $this->set('_serialize', ['forms']);
    }

    public function show($id_formdinamic=null, $id_entidad=null, $id_dataform=null,$direction=null){
        if(empty($id_formdinamic) || empty($id_dataform))
            return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);
        $acciones=[];
        //obteniendo wf del formdinamic a true
        $vinculo=$this->Formdinamicwfs->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andwhere(['vinculo'=>1])
            ->andwhere(['trash'=>0])
            ->count();
        $formdinamic=$this->Formdinamics->get($id_formdinamic);
        if($vinculo==0)return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);
        if(!empty($id_dataform)){
            $dataform=$this->Dataforms->get($id_dataform);
            $cestado=$this->Wfetapas->get($dataform->wfetapa_id,
                ['contain'=>['cestados']]);
            if($this->cargaDatosPerfilComun($id_dataform)){
                if($formdinamic->cestado_id!=$this->estado_archivado) { //si el formulario es archivado dirigir a show si la acción es viewFormDinamics
                    $acciones = parent::estadosWf($id_formdinamic, 'formdinamicwfs', 'formdinamic_id', 'view', $cestado->Cestados['id']);
                    $acciones = $this->filtrarEtapasPorPrivilegios($acciones);
                }
            }
            $_SESSION['fd_id_dataform']=$id_dataform;
            $perfil=$this->Perfils->get($this->Auth->user('perfil_id'));
            if(strcmp("Súper Administrador",$perfil->nombre)!=0){
                $function=$this->accionPrivilegios($dataform->wfetapa_id);
                if(strcmp($function,'show/')!=0) return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);
            }
        }

        $tituloobj='Formulario';
        $entidad = $this->Entidads->get($id_entidad, [
            'contain' => ['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados', 'Entidadcontactos']
        ]);
        $permisos = new VerificacionPermisosController();
        $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","add",'imprimir','index']);
        $user = $this->Auth->user();
        $nav = $permisos->getRuta('Entidadcontactos', "view", $user['perfil_id'],'entidads');
        $nav['nav'][0]['alias']="Entidades";
        $nav["complemento"][0]["alias"]=$formdinamic->nombre;
        $titulo = $permisos->getTitle($this->modelo);

        $model='Formseccions';
        $form= new LlenarFormDinamicsController();
        $datos=$form->crearForm($id_formdinamic,$model,$id_dataform,$direction);
        $cont=1;
        $countfp=1;
        $_SESSION['fd_if_formdinamic']=$id_formdinamic;
        $_SESSION['fd_id_entidad']=$id_entidad;
        /*Obteniendo estado carga de datos*/
        $carga=null;
        if(!empty($id_dataform)){
            $etapa=$this->Dataforms->get($id_dataform);
            $carga=$this->Wfetapas->get($etapa->wfetapa_id,[
                'contain' => ['Cestados']
            ]);
        }
        $this->set(compact("controltools","nav",'titulo','tituloobj','entidad','formdinamic','datos','cont','countfp','acciones','id_dataform','carga'));
        $this->set('_serialize', ['forms']);
    }

    public function observations($id_formdinamic=null, $id_entidad=null, $id_dataform=null,$direction=null){
        if(empty($id_formdinamic) || empty($id_dataform))
            return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);

        if(!$this->cargaDatosPerfilComun($id_dataform)) return $this->redirect(['action' => 'view',$id_entidad]);

        $formdinamic=$this->Formdinamics->get($id_formdinamic);
        if($formdinamic->cestado_id==$this->estado_archivado) //si el formulario es archivado dirigir a show si la acción es viewFormDinamics
            return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);

        $acciones=[];
        //obteniendo wf del formdinamic a true
        $vinculo=$this->Formdinamicwfs->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andwhere(['vinculo'=>1])
            ->andwhere(['trash'=>0])
            ->count();
        if($vinculo==0)return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);

        $formdinamicwf=$this->Formdinamicwfs->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andwhere(['vinculo'=>1])
            ->andwhere(['trash'=>0])
            ->first();

        $etapas_form=$this->Wfetapas->find()
            ->select(['cestado_id'])
            ->Where(['workflow_id'=>$formdinamicwf->workflow_id])
            ->andWhere(['trash'=>0])
            ->all();

        $etapasid_form = [];
        foreach ($etapas_form as $etapa) {
            array_push($etapasid_form, $etapa->cestado_id);
        }
        $congelado=0;
        if(!empty($id_dataform)){
            $dataform=$this->Dataforms->get($id_dataform);
            $cestado=$this->Wfetapas->get($dataform->wfetapa_id,
                ['contain'=>['cestados']]);

            if(in_array($cestado->Cestados['id'], $etapasid_form)) {
                $acciones=parent::estadosWf($id_formdinamic,'formdinamicwfs','formdinamic_id','view',$cestado->Cestados['id']);
                $acciones=$this->filtrarEtapasPorPrivilegios($acciones);
            } else {
                $congelado=1;
            }

            $_SESSION['fd_id_dataform']=$id_dataform;
            $perfil=$this->Perfils->get($this->Auth->user('perfil_id'));
            if(strcmp("Súper Administrador",$perfil->nombre)!=0){
                $function=$this->accionPrivilegios($dataform->wfetapa_id);
                if(strcmp($function,'observations/')!=0) return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_entidad]);
            }
        }

        $tituloobj='Formulario';
        $entidad = $this->Entidads->get($id_entidad, [
            'contain' => ['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados', 'Entidadcontactos']
        ]);

        $permisos = new VerificacionPermisosController();
        $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","add",'imprimir','index']);
        $user = $this->Auth->user();
        $nav = $permisos->getRuta('Entidadcontactos', "view", $user['perfil_id'],'entidads');
        $nav['nav'][0]['alias']="Entidades";
        $nav["complemento"][0]["alias"]=$formdinamic->nombre;
        $titulo = $permisos->getTitle($this->modelo);

        $model='Formseccions';
        $form= new LlenarFormDinamicsController();
        $datos=$form->crearForm($id_formdinamic,$model,$id_dataform,$direction);
        $cont=1;
        $countfp=1;
        $_SESSION['fd_if_formdinamic']=$id_formdinamic;
        $_SESSION['fd_id_entidad']=$id_entidad;
        /*Obteniendo estado carga de datos*/
        $carga=null;
        if(!empty($id_dataform)){
            $etapa=$this->Dataforms->get($id_dataform);
            $carga=$this->Wfetapas->get($etapa->wfetapa_id,[
                'contain' => ['Cestados']
            ]);
        }
        $this->set(compact("controltools","nav",'titulo','tituloobj','entidad','formdinamic','datos','cont','countfp','acciones','id_dataform','carga', 'congelado'));
        $this->set('_serialize', ['forms']);
    }

    public function accionPrivilegios($etapa_id, $bd=false){
        $this->loadModel('Wfformdinamicprivs');
        $accionpriv=$this->Wfformdinamicprivs->find()
            ->where(['wfetapa_id'=>$etapa_id])
            ->andwhere(['perfil_id'=>$this->Auth->user('perfil_id')])
            ->andwhere(['Wfformdinamicprivs.trash'=>0])
            ->contain('Cwfaccions');
        $accion='#';
        $ac="noaccess";
        if(count($accionpriv->toArray())>0){
            $accionpriv=$accionpriv->toArray();
            $ac=$accionpriv[0]->cwfaccion->nombre;
            switch($ac){
                case 'edit':
                    $accion="viewFormDinamics/";
                    break;
                case 'view':
                    $accion="show/";
                    break;
                case 'observ':
                    $accion="observations/";
                    break;
                case 'editlimit':
                    $accion="viewFormDinamics/";
                    break;
            }
        }
        if($bd) return $ac;//retona la acción extraída desde la bd
        else return $accion;//retorna acción para renderizar la vista adecuada
    }

    /*Ingresar observaciones*/
    public function savecargaobserv(){
        $this->autoRender=false;
        if ($this->request->is(['get','post'])) {
            $idformpregunta=$this->request->query['idformpregunta'];
            $data=$this->request->query['data'];
            $iddataform=$this->request->query['iddataform'];
            $idformrespuesta=$this->request->query['idformrespuesta'];
            if (empty($idformpregunta) || empty($data) || empty($idformrespuesta))
                echo false;
            else {
                $this->loadModel("Dataformdets");
                $this->loadModel("Formpreguntas");
                $this->loadModel("Formrespuestas");
                /*Obteniendo iddatafomdet para actualización*/
                $iddataformdet=$this->Dataformdets->find()
                    ->Where(['dataform_id'=>$iddataform])
                    ->andWhere(['pregunta_id'=>$idformpregunta])
                    ->andWhere(['trash'=>0]);
                $dataformdetsTable = TableRegistry::get('Dataformdets');
                $items=array();
                foreach ($iddataformdet as $formres) {
                    if(!empty($formres->jsonobserv)){
                        foreach($formres->jsonobserv as $jsonkey => $value){
                            for($ob=0; $ob<count($value['items']); $ob++){
                                $anteriores=array(
                                    "value"=>$value['items'][$ob]['value'],
                                    "datetime"=>$value['items'][$ob]['datetime'],
                                    "usuario"=>$value['items'][$ob]['usuario']
                                );
                                array_push($items,$anteriores);
                            }
                            $actual=array(
                                "value"=>$data,
                                "datetime"=>date("Y-m-d H:i:s"),
                                "usuario"=>$this->Auth->user('username')
                            );
                            array_push($items,$actual);

                            $json=array($jsonkey=>array("type"=>"array",
                                "items"=>$items));
                        }
                    }else{
                        $json=array($idformrespuesta=>array("type"=>"array",
                            "items"=>array(array(
                                "value"=>$data,
                                "datetime"=>date("Y-m-d H:i:s"),
                                "usuario"=>$this->Auth->user('username')
                            ))));
                    }
                }
                if(count($iddataformdet->toArray())==0){
                    $json=array($idformrespuesta=>array("type"=>"array",
                        "items"=>array(array(
                            "value"=>$data,
                            "datetime"=>date("Y-m-d H:i:s"),
                            "usuario"=>$this->Auth->user('username')
                        ))));
                    $preguntatext=$this->Formpreguntas->get($idformpregunta,[
                        'contain'=>['Fdpreguntas']
                    ]);
                    $dataformdet=$dataformdetsTable->newEntity();
                    $dataformdet->dataform_id=$iddataform;
                    $dataformdet->pregunta_id=$idformpregunta;
                    $dataformdet->pregunta=$preguntatext->fdpregunta->alias;
                    $dataformdet->jsonobserv=$json;
                    $dataformdet->created = date("Y-m-d H:i:s");
                    $dataformdet->usuario = $this->Auth->user('username');
                }else{
                    $iddataformdet=$iddataformdet->toArray();
                    $dataformdet=$this->Dataformdets->get($iddataformdet[0]->id);
                    $dataformdet->jsonobserv=$json;
                    $dataformdet->modified = date("Y-m-d H:i:s");
                    $dataformdet->usuariomodif = $this->Auth->user('username');
                }
                if($dataformdetsTable->save($dataformdet)){
                    echo json_encode(array('res'=>true));
                }else{
                    echo json_encode(array('res'=>false));
                }
            }
        }
    }

    /*Guarda carga de datos*/
    public function savecarga(){
        $this->autoRender=false;
        if ($this->request->is(['get','post'])) {
            if(count($this->request->getData())>0){
                $idformpregunta=$this->request->getData('idformpregunta');
                $iddataform=$this->request->getData('iddataform');
                $idformrespuesta=$this->request->getData('idformrespuesta');
            }
            else{
                $idformpregunta=$this->request->query['idformpregunta'];
                $data=$this->request->query['data'];
                $iddataform=$this->request->query['iddataform'];
                $idformrespuesta=$this->request->query['idformrespuesta'];
            }

            if (empty($idformpregunta) && empty($data) && empty($idformrespuesta) && count($this->request->getData())==0)
                echo false;
            else {
                $this->loadModel("Dataformdets");
                $this->loadModel("Formpreguntas");
                $this->loadModel("Formrespuestas");
                if(empty($iddataform)){
                    //Obteniendo wf del formdinamic
                    $id_workflow=$this->Formdinamicwfs->find()
                        ->where(['formdinamic_id'=>$_SESSION['fd_if_formdinamic']])
                        ->andwhere(['trash'=>0])
                        ->toArray();
                    //obteniendo etapa inicial del wf
                    $etapa_inicial=$this->Wfetapas->find()
                        ->where(['workflow_id'=>$id_workflow[0]->workflow_id])
                        ->andwhere(['trash'=>0])
                        ->order(['id'=>'ASC'])->first();
                    $dataformsTable = TableRegistry::get('Dataforms');
                    $dataform = $dataformsTable->newEntity();
                    $dataform->codigo=self::generateCod($_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_entidad']);
                    $dataform->formdinamic_id=$_SESSION['fd_if_formdinamic'];
                    $dataform->registro_id=$_SESSION['fd_id_entidad'];
                    $dataform->modelo=$this->modelo;
                    $dataform->workflow_id=$id_workflow[0]->workflow_id;
                    $dataform->wfetapa_id=$etapa_inicial->id;
                    $dataform->created = date("Y-m-d H:i:s");
                    $dataform->usuario = $this->Auth->user('username');
                    if($dataformsTable->save($dataform)){
                        $acciones=array();
                        $cestado=$this->Wfetapas->get($etapa_inicial->id,
                            ['contain'=>['cestados']]);
                        $acciones=parent::estadosWf($_SESSION['fd_if_formdinamic'],'formdinamicwfs','formdinamic_id','view',$cestado->Cestados['id']);
                        $acciones=$this->filtrarEtapasPorPrivilegios($acciones);
                        $iddataform=$dataform->id;
                        $_SESSION['fd_id_dataform']=$iddataform;
                        if(count($this->request->getData())>0) $data=$data=$_FILES['file']['name'];

                        $formrespuestas=$this->Formrespuestas->find()
                            ->where(['formpregunta_id'=>$idformpregunta]);
                        $json= array();
                        foreach ($formrespuestas as $formres) {
                            $prefijo=null;
                            $sufijo=null;
                            if(!empty($formres->params['Formrespuestas']['items'][0]['value']))$prefijo=$formres->params['Formrespuestas']['items'][0]['value'];
                            if(!empty($formres->params['Formrespuestas']['items'][1]['value']))$sufijo=$formres->params['Formrespuestas']['items'][1]['value'];
                            if($formres->id==$idformrespuesta){
                                $jsonvalor=array($formres->id=>array("type"=>"array",
                                    "items"=>array(array(
                                        "value"=>$data,
                                        "prefijo"=>$prefijo,
                                        "sufijo"=>$sufijo,
                                        "ponderacion"=>null,
                                    ))));
                                array_push($json,$jsonvalor);
                            }else{
                                $jsonnull=array($formres->id=>array("type"=>"array",
                                    "items"=>array(array(
                                        "value"=>null,
                                        "prefijo"=>$prefijo,
                                        "sufijo"=>$sufijo,
                                        "ponderacion"=>null,
                                    ))));
                                array_push($json,$jsonnull);
                            }
                        }

                        $preguntatext=$this->Formpreguntas->get($idformpregunta,[
                            'contain'=>['Fdpreguntas']
                        ]);
                        $dataformdetsTable = TableRegistry::get('Dataformdets');
                        $dataformdet=$dataformdetsTable->newEntity();
                        $dataformdet->dataform_id=$dataform->id;
                        $dataformdet->pregunta_id=$idformpregunta;
                        $dataformdet->pregunta=$preguntatext->fdpregunta->alias;
                        $dataformdet->jsonrespuesta=$json;
                        $dataformdet->created = date("Y-m-d H:i:s");
                        $dataformdet->usuario = $this->Auth->user('username');

                        if($dataformdetsTable->save($dataformdet)){
                            if(count($this->request->getData())>0){
                                $resfile=self::uploadFileDB($_FILES['file'],$dataformdet->id,$idformpregunta,$idformrespuesta);
                                if($resfile[0]){
                                    echo json_encode(array('res'=>true,
                                        'iddataform'=>$iddataform,
                                        'upload'=>true,
                                        'dataformdetfile_id'=>$resfile[1],
                                        'dataformdetfile_filename'=>$resfile[2],
                                        'dataformdetfile_fileversion'=>$resfile[3],
                                        'wf'=>true,
                                        'acciones'=>$acciones));
                                }else{
                                    echo json_encode(array('res'=>false,
                                        'upload'=>false));
                                }
                            }else {
                                echo json_encode(array('res' => true,
                                    'iddataform' => $iddataform,
                                    'wf'=>true,
                                    'acciones'=>$acciones));
                            }
                        }else{
                            echo json_encode(array('res'=>false));
                        }
                    }else{
                        echo json_encode(array('res'=>false));
                    }
                }
                else{
                    /*Obteniendo iddatafomdet para actualización*/
                    $iddataformdet=$this->Dataformdets->find()
                        ->Where(['dataform_id'=>$iddataform])
                        ->andWhere(['pregunta_id'=>$idformpregunta])
                        ->andWhere(['trash'=>0]);

                    $dataformdetsTable = TableRegistry::get('Dataformdets');
                    if(count($this->request->getData())>0) $data=$_FILES['file']['name'];
                    $json= array();
                    foreach ($iddataformdet as $formres) {
                        if(!empty($formres->jsonrespuesta)){
                            foreach($formres->jsonrespuesta as $jsonkey => $value){
                                $idformrespuestaG=key($value);
                                $tiporespuesta=$this->Formrespuestas->get($idformrespuestaG,[
                                    'contain'=>['Fdrespuestas']
                                ]);
                                $datotipo=$this->Cdatotipos->find()
                                    ->Where(['id'=>$tiporespuesta->fdrespuesta->cdatotipo_id])
                                    ->andWhere(['cestado_id'=>1])
                                    ->andWhere(['trash'=>0]);
                                $datotipo=$datotipo->toArray();
                                if(strcmp($datotipo[0]->nombre,'Selección Múltiple con cheques')==0){
                                    $find=false;
                                    $datanew="";
                                    $valuescheck=explode(',',$value[$idformrespuestaG]['items'][0]['value']);
                                    $newarray=array();
                                    for($ch=0; $ch<count($valuescheck); $ch++){
                                        if($valuescheck[$ch]!=""){
                                            if($valuescheck[$ch]!=$data)//agrega al array aquellos datos que no coinciden con el ingresado
                                                array_push($newarray,$valuescheck[$ch]);
                                            else
                                                $find=true;//bandera que indicado que el valor fue ingresado previamente y ahora se desea eliminar del registro
                                        }
                                    }

                                    for($ch=0; $ch<count($newarray); $ch++){
                                        $datanew.=$newarray[$ch];//formando data con valores separados por coma a ingresar en el value del json
                                        if(isset($newarray[$ch+1]))$datanew.=",";
                                    }

                                    if($find==false){//se ingresa el dato sólo si es no se encontró previamente
                                        if(count($newarray)>0)$datanew.=",".$data;
                                        else $datanew=$data;
                                    }
                                    $data=$datanew;
                                }

                                if($idformrespuestaG==$idformrespuesta){
                                    $jsonvalor=array($idformrespuestaG=>array("type"=>"array",
                                        "items"=>array(array(
                                            "value"=>$data,
                                            "prefijo"=>$value[$idformrespuestaG]['items'][0]['prefijo'],
                                            "sufijo"=>$value[$idformrespuestaG]['items'][0]['sufijo'],
                                            "ponderacion"=>$value[$idformrespuestaG]['items'][0]['ponderacion'],
                                        ))));
                                    array_push($json,$jsonvalor);
                                }else{
                                    $jsonnull=array($idformrespuestaG=>array("type"=>"array",
                                        "items"=>array(array(
                                            "value"=>$value[$idformrespuestaG]['items'][0]['value'],
                                            "prefijo"=>$value[$idformrespuestaG]['items'][0]['prefijo'],
                                            "sufijo"=>$value[$idformrespuestaG]['items'][0]['sufijo'],
                                            "ponderacion"=>$value[$idformrespuestaG]['items'][0]['ponderacion'],
                                        ))));
                                    array_push($json,$jsonnull);
                                }
                            }
                        }else{
                            //Ingresando respuesta si el registro solo posee observación
                            $formrespuestas=$this->Formrespuestas->find()
                                ->where(['formpregunta_id'=>$idformpregunta]);
                            $json= array();
                            foreach ($formrespuestas as $formres) {
                                $prefijo=null;
                                $sufijo=null;
                                if(!empty($formres->params['Formrespuestas']['items'][0]['value']))$prefijo=$formres->params['Formrespuestas']['items'][0]['value'];
                                if(!empty($formres->params['Formrespuestas']['items'][1]['value']))$sufijo=$formres->params['Formrespuestas']['items'][1]['value'];
                                if($formres->id==$idformrespuesta){
                                    $jsonvalor=array($formres->id=>array("type"=>"array",
                                        "items"=>array(array(
                                            "value"=>$data,
                                            "prefijo"=>$prefijo,
                                            "sufijo"=>$sufijo,
                                            "ponderacion"=>null,
                                        ))));
                                    array_push($json,$jsonvalor);
                                }else{
                                    $jsonnull=array($formres->id=>array("type"=>"array",
                                        "items"=>array(array(
                                            "value"=>null,
                                            "prefijo"=>$prefijo,
                                            "sufijo"=>$sufijo,
                                            "ponderacion"=>null,
                                        ))));
                                    array_push($json,$jsonnull);
                                }
                            }
                        }
                    }
                    $iddataformdet=$iddataformdet->toArray();
                    $preguntatext=$this->Formpreguntas->get($idformpregunta,[
                        'contain'=>['Fdpreguntas']
                    ]);
                    if(count($iddataformdet)==0){
                        $formrespuestas=$this->Formrespuestas->find()
                            ->where(['formpregunta_id'=>$idformpregunta]);
                        $json= array();
                        foreach ($formrespuestas as $formres) {
                            $prefijo=null;
                            $sufijo=null;
                            if(!empty($formres->params['Formrespuestas']['items'][0]['value']))$prefijo=$formres->params['Formrespuestas']['items'][0]['value'];
                            if(!empty($formres->params['Formrespuestas']['items'][1]['value']))$sufijo=$formres->params['Formrespuestas']['items'][1]['value'];
                            if($formres->id==$idformrespuesta){
                                $jsonvalor=array($formres->id=>array("type"=>"array",
                                    "items"=>array(array(
                                        "value"=>$data,
                                        "prefijo"=>$prefijo,
                                        "sufijo"=>$sufijo,
                                        "ponderacion"=>null,
                                    ))));
                                array_push($json,$jsonvalor);
                            }else{
                                $jsonnull=array($formres->id=>array("type"=>"array",
                                    "items"=>array(array(
                                        "value"=>null,
                                        "prefijo"=>$prefijo,
                                        "sufijo"=>$sufijo,
                                        "ponderacion"=>null,
                                    ))));
                                array_push($json,$jsonnull);
                            }
                        }
                        $dataformdet=$dataformdetsTable->newEntity();
                        $dataformdet->dataform_id=$iddataform;
                        $dataformdet->pregunta_id=$idformpregunta;
                        $dataformdet->pregunta=$preguntatext->fdpregunta->alias;
                        $dataformdet->jsonrespuesta=$json;
                        $dataformdet->created = date("Y-m-d H:i:s");
                        $dataformdet->usuario = $this->Auth->user('username');
                    }
                    else{
                        $dataformdet=$this->Dataformdets->get($iddataformdet[0]->id);
                        $dataformdet->jsonrespuesta=$json;
                        $dataformdet->modified = date("Y-m-d H:i:s");
                        $dataformdet->usuariomodif = $this->Auth->user('username');
                    }
                    /*****************************************/

                    if($dataformdetsTable->save($dataformdet)){
                        if(count($this->request->getData())>0){
                            $resfile=self::uploadFileDB($_FILES['file'],$dataformdet->id,$idformpregunta,$idformrespuesta);
                            if($resfile[0]){
                                echo json_encode(array('res'=>true,
                                    'iddataform'=>$iddataform,
                                    'upload'=>true,
                                    'dataformdetfile_id'=>$resfile[1],
                                    'dataformdetfile_filename'=>$resfile[2],
                                    'dataformdetfile_fileversion'=>$resfile[3],
                                    'wf'=>false));
                            }else{
                                echo json_encode(array('res'=>false));
                            }
                        }else{
                            echo json_encode(array('res'=>true,
                                'iddataform'=>$iddataform,
                                'upload'=>false,
                                'wf'=>false));
                        }
                    }else{
                        echo json_encode(array('res'=>false));
                    }
                }
            }
        }
    }

    public function generateCod($id_formdinamic, $id_entidad){
        $formdinamic=$this->Formdinamics->get($id_formdinamic);
        $nombre=substr($formdinamic->nombre,0,2);
        $countdataforms=$this->Dataforms->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andWhere(['registro_id'=>$id_entidad])
            ->andWhere(['modelo'=>$this->modelo])
            ->count();
        $idmas=$this->Dataforms->find()->count();
        $idmas++;
        $cadena=$nombre.$idmas."_".date('dmY')."-".($countdataforms+1);
        return $cadena;
    }

    public function uploadFileDB($file,$dataformdet_id,$formpregunta_id,$formrespuesta_id,$modify=false){
        /************obtener binario para ingresar a db************/
        $fp = fopen($file['tmp_name'], "rb");
        $binario = fread($fp, $file['size']);
        fclose($fp);
        /**************************fin*******************************/

        $this->loadModel('Dataformdetfiles');
        $version=$this->Dataformdetfiles->find()
            ->Where(['dataformdet_id'=>$dataformdet_id])
            ->andWhere(['formpregunta_id'=>$formpregunta_id])
            ->andWhere(['formrespuesta_id'=>$formrespuesta_id])
            ->andWhere(['trash'=>0])
            ->count();
        $filetype=explode('/',$file['type']);
        if(strcmp('vnd.openxmlformats-officedocument.wordprocessingml.document',$filetype[1])==0) $filetype[1]="docx";
        else if(strcmp('msword',$filetype[1])==0) $filetype[1]="doc";
        $dataformdetfilesTable = TableRegistry::get('Dataformdetfiles');
        $dataformdetfile=$dataformdetfilesTable->newEntity();
        $dataformdetfile->dataformdet_id=$dataformdet_id;
        $dataformdetfile->formpregunta_id=$formpregunta_id;
        $dataformdetfile->formrespuesta_id=$formrespuesta_id;
        $dataformdetfile->file=$binario;
        $dataformdetfile->filename=$file['name'];
        $dataformdetfile->fileversion=$version+1;
        $dataformdetfile->filetype=$filetype[1];
        $dataformdetfile->filetam=$file['size'];
        $dataformdetfile->etiquetas=$file['name'];
        $dataformdetfile->created= date("Y-m-d H:i:s");
        $dataformdetfile->usuario= $this->Auth->user('username');
        if($modify){
            $dataformdetfile->modified= date("Y-m-d H:i:s");
            $dataformdetfile->usuariomodif= $this->Auth->user('username');
        }
        $dataformdetfile->trash=0;
        if($dataformdetfilesTable->save($dataformdetfile)){
            return array(0=>true,
                1=>$dataformdetfile->id,
                2=>$dataformdetfile->filename,
                3=>$dataformdetfile->fileversion);
        }else{
            return array(0=>false);
        }
    }

    public function download($id){
        $this->autoRender=false;
        $this->loadModel('Dataformdetfiles');
        $datafile=$this->Dataformdetfiles->get($id);
        $tipo=array('jpeg'=>'image/jpeg',
            'jpg'=>'image/jpeg',
            'jpe'=>'image/jpeg',
            'png'=>'image/png',
            'gif'=>'image/gif',
            'doc'=>'application/msword',
            'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'pdf'=>'application/pdf');
        header("Content-type:".$tipo[$datafile->filetype]);
        header("Content-Disposition: attachment; filename='".$datafile->filename."'");
        ob_clean();
        flush();
        echo stream_get_contents($datafile->file);
    }

    public function showFile($id){
        $this->autoRender=false;
        $this->loadModel('Dataformdetfiles');
        $datafile=$this->Dataformdetfiles->get($id);
        $tipo=array('jpeg'=>'image/jpeg',
            'jpg'=>'image/jpeg',
            'jpe'=>'image/jpeg',
            'png'=>'image/png',
            'gif'=>'image/gif',
            'doc'=>'application/msword',
            'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'pdf'=>'application/pdf');
        header("Content-type:".$tipo[$datafile->filetype]);
        ob_clean();
        flush();
        echo stream_get_contents($datafile->file);
    }

    //función para identificar si hay respuestas para todas las preguntas requeridas
    public function answerRequiredComplete($id_formdinamic){
        $this->loadModel("Formseccions");
        $this->loadModel("Formpreguntas");
        $this->loadModel("Formrespuestas");
        $this->loadModel("Dataformdets");
        //obteniendo secciones padre del formulario indicado
        $seccionesPadre=$this->Formseccions->find('list',[
            'keyField' => 's',
            'valueField' => 'id'
        ])
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andwhere(['formseccion_id'=>0])
            ->andwhere(['trash'=>0])
            ->toArray();
        $secpreg=array();
        for($sec=0; $sec<count($seccionesPadre); $sec++){
            //obteniendo secciones hijas si es que posee
            $seccionesHijas=$this->Formseccions->find()
                ->where(['formdinamic_id'=>$id_formdinamic])
                ->andwhere(['formseccion_id'=>$seccionesPadre[$sec]])
                ->andwhere(['trash'=>0]);
            if(count($seccionesHijas->toArray())>0){
                foreach($seccionesHijas as $seckey){
                    array_push($secpreg,$seckey->id);//se agregan al array secciones hijas y no padre
                }
            }else array_push($secpreg,$seccionesPadre[$sec]);//se agregan secciones padre que no poseen secciones hijas
        }

        //preguntas según array de secciones
        $preguntas=$this->Formpreguntas->find()
            ->where(['formseccion_id IN '=>$secpreg])
            ->andwhere(['trash'=>0]);
        $requiredpreg=array();//almacenador de ids de preguntas requeridas con value a true(preguntas obligatorias de llenar)
        foreach($preguntas as $pregkey){
            $resp_preg_reg=$this->Formrespuestas->find()
                ->where(['formpregunta_id'=>$pregkey->id])
                ->where(['trash'=>0])
                ->count();
            if($resp_preg_reg>0){
                if($pregkey->params['Formpregunta']['items'][0]['value']) array_push($requiredpreg,$pregkey->id);
            }
        }
        $countpregrq=count($requiredpreg); //conteo de preguntas requeridas

        //consulta para obtener respuestas según preguntas requeridas
        $countresprq=0;//conteo de preguntas requeridas con respuesta
        if($countpregrq>0) {
            $respuestas = $this->Dataformdets->find()
                ->where(["dataform_id" => $_SESSION['fd_id_dataform']])
                ->andwhere(["pregunta_id IN " => $requiredpreg])
                ->andwhere(["jsonrespuesta IS NOT" => null])
                ->andwhere(["trash" => 0]);
            foreach ($respuestas as $respkey) {
                $flag = true;
                $contparcial = 0;
                foreach ($respkey->jsonrespuesta as $jsonkey) {
                    foreach ($jsonkey as $itemskey) {
                        if ($itemskey['items'][0]['value'] == null || $itemskey['items'][0]['value'] == "") $flag = false;
                        else $contparcial++;
                    }
                }
                $countresprq += $contparcial;
            }
        }

        if($countpregrq>$countresprq) return false;
        else return true;
    }

    public function changeStateWf($id, $action='viewFormDinamics'){
        $this->autoRender=false;
        if(isset($_SESSION['fd_id_dataform'])){
            $formdinamic=$this->Formdinamics->get($_SESSION['fd_if_formdinamic']);
            if($formdinamic->cestado_id==$this->estado_archivado) //si el formulario es archivado dirigir al action
                return $this->redirect(['action' => 'view',$_SESSION['fd_id_entidad']]);

            if(!$this->cargaDatosPerfilComun($_SESSION['fd_id_dataform'])) return $this->redirect(['action' => 'view',$_SESSION['fd_id_entidad']]);
            $this->loadModel('Wftransicionprivs');
            $dataform=$this->Dataforms->get($_SESSION['fd_id_dataform']);
            $cestado=$this->Wfetapas->get($dataform->wfetapa_id,
                ['contain'=>['cestados']]);
            $acciones=parent::estadosWf($_SESSION['fd_if_formdinamic'],'formdinamicwfs','formdinamic_id','view',$cestado->Cestados['id']);
            $permitidowft=[];
            for($p=0; $p<count($acciones); $p++){
                if($id==$acciones[$p]['id']){
                    $nombretr=$acciones[$p]['nombre'];
                    $permitidowft=$this->Wftransicionprivs->find()
                        ->where(['wftransicion_id'=>$acciones[$p]['wft_id']])
                        ->andwhere(['perfil_id'=>$this->Auth->user('perfil_id')])
                        ->andwhere(['trash'=>0])
                        ->toArray();
                }

            }
            if(count($permitidowft)>0){
                if($permitidowft[0]->permitido){
                    if(strcmp($nombretr,"Evaluación")==0){//identificando que la siguiente etapa es evaluacion y validar que todas las preguntas requeridas posean respuestas para pasar a la etapa evaluación
                        if($this->answerRequiredComplete($_SESSION['fd_if_formdinamic'])){
                            $array=[];
                            foreach($acciones as $key)array_push($array,$key['id']);
                            if(in_array($id,$array)){
                                $idetapa=$this->Wfetapas->find()
                                    ->where(['workflow_id'=>$dataform->workflow_id])
                                    ->andWhere(['cestado_id'=>$id])
                                    ->andWhere(['trash'=>0]);
                                $idetapa=$idetapa->toArray();
                                $dataformreg=$this->Dataforms->get($_SESSION['fd_id_dataform']);
                                $dataformreg->wfetapa_id=$idetapa[0]->id;
                                $dataformreg->modified = date("Y-m-d H:i:s");
                                $dataformreg->usuariomodif = $this->Auth->user('username');
                                if ($this->Dataforms->save($dataformreg)) {
                                    $_SESSION["entidad-save"]=2;
                                    return $this->redirect(['action' => 'view',$_SESSION['fd_id_entidad']]);
                                }
                                else{
                                    $_SESSION["wf-save"]=0;
                                    return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_entidad'],$_SESSION['fd_id_dataform']]);

                                }
                            }else{
                                return $this->redirect(['action' => 'view',$_SESSION['fd_id_entidad']]);
                            }
                        }else{
                            $_SESSION["wf-save"]=-1;
                            return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_entidad'],$_SESSION['fd_id_dataform']]);
                        }
                    }else{//ejecutar cambio de etapas para opciones que no sean evaluación
                        $array=[];
                        foreach($acciones as $key)array_push($array,$key['id']);
                        if(in_array($id,$array)){
                            $idetapa=$this->Wfetapas->find()
                                ->where(['workflow_id'=>$dataform->workflow_id])
                                ->andWhere(['cestado_id'=>$id])
                                ->andWhere(['trash'=>0]);
                            $idetapa=$idetapa->toArray();
                            $dataformreg=$this->Dataforms->get($_SESSION['fd_id_dataform']);
                            $dataformreg->wfetapa_id=$idetapa[0]->id;
                            $dataformreg->modified = date("Y-m-d H:i:s");
                            $dataformreg->usuariomodif = $this->Auth->user('username');
                            if ($this->Dataforms->save($dataformreg)) {
                                $_SESSION["entidad-save"]=2;
                                return $this->redirect(['action' => 'view',$_SESSION['fd_id_entidad']]);
                            }
                            else{
                                $_SESSION["wf-save"]=0;
                                return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_entidad'],$_SESSION['fd_id_dataform']]);

                            }
                        }else{
                            return $this->redirect(['action' => 'view',$_SESSION['fd_id_entidad']]);
                        }
                    }
                }else{
                    $_SESSION["wf-save"]=0;
                    return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_entidad'],$_SESSION['fd_id_dataform']]);
                }
            }else{
                $_SESSION["wf-save"]=0;
                return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_entidad'],$_SESSION['fd_id_dataform']]);
            }
        }else{
            return $this->redirect(['action' => 'view',$_SESSION['fd_id_entidad']]);
        }

    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if(!$this->accesoPantalla("Entidads", 'add')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $entidad = $this->Entidads->newEntity();
            if ($this->request->is('post')) {
                $tipodoc=$this->entTipoDocumento($this->request->getData()['nacional']);
                $no_disponible = 0;
                if($this->request->getData()['docidnull'] == '0') {
                    $docid=$this->request->getData()['docid'];
                    $docid=str_replace("-", "", $docid);
                } else {
                    $no_disponible = 1;
                    $docid=$this->request->getData()['codigo'];
                }

                $entidad = $this->Entidads->patchEntity($entidad, $this->request->getData());
                $entidad->usuario = $this->Auth->user('username');
                $entidad->created = date('Y-m-d H:i:s');
                $entidad->docid = $docid;
                $entidad->cdocidtipo_id = $tipodoc;
                $entidad->trash = 0;
                $entidad->modified = null;

                if ($this->Entidads->save($entidad)) {
                    if(isset($this->request->getData()['entidad'])) {
                        foreach ($this->request->getData()['entidad'] as $key => $entidadred_hija) {
                            if($entidadred_hija != '') {
                                $entidadreds = TableRegistry::get('Entidadreds');
                                $entidadred  = new Entidadred();
                                $entidadred->entidadmadre = $entidad->id;
                                $entidadred->entidadhija = $entidadred_hija;
                                $entidadred->vinculo = 1;
                                $entidadred->usuario = $this->Auth->user('username');
                                $entidadred->created = date('Y-m-d H:i:s');
                                $entidadreds->save($entidadred);
                            }
                        }
                    }

                    if($no_disponible == 1) {
                        if ($this->Entidads->exists(['Entidads.docid' => $entidad->codigo . $entidad->id])) {
                            $max_corr = $this->Entidads->find()
                                ->select(['docid_max' => 'MAX(Entidads.docid)'])
                                ->where(['Entidads.docid LIKE' => $entidad->codigo . $entidad->id . '%'])
                                ->toArray();

                            $correlativo = (int)(substr($max_corr[0]->docid_max, strlen($entidad->codigo . $entidad->id))) + 1;
                            if($correlativo < 10) {
                                $entidad->docid = $entidad->docid . $entidad->id . '0' . $correlativo;
                            } else {
                                $entidad->docid = $entidad->docid . $entidad->id . $correlativo;
                            }
                        } else {
                            $entidad->docid = $entidad->docid . $entidad->id;
                        }

                        $this->Entidads->save($entidad);
                    }

                    //$this->Flash->success(__('The entidad has been saved.'));
                    $_SESSION["entidad-save"] = 1;
                    return $this->redirect(['action' => 'view', $entidad->id]);
                }
                //$this->Flash->error(__('The entidad could not be saved. Please, try again.'));
            }
            $cdocidtipos = $this->Entidads->Cdocidtipos->find('list')
                ->where(['Cdocidtipos.trash' => 0])
                ->andWhere(['Cdocidtipos.id IN' => $this->docidtipos]);
            $centidadtipos = $this->Entidads->Centidadtipos->find('list')
                ->where(['Centidadtipos.trash' => 0])
                ->andWhere(['Centidadtipos.cestado_id' => $this->estadoActivo]);
            $centidadrols = $this->Entidads->Centidadrols->find('list')
                ->where(['Centidadrols.trash' => 0])
                ->andWhere(['Centidadrols.cestado_id' => $this->estadoActivo]);
            $cestados = $this->Entidads->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $entidads = $this->Entidads->find('list')
                ->where(['Entidads.trash' => 0])
                ->andWhere(['Entidads.cestado_id' => $this->estadoActivo]);

            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools=$permisos->getControlToolMultipleModels($this->modelo ,["exportPdf","add",'imprimir','edit'], 'Entidadcontactos');

            $nav = $permisos->getRuta('Entidadcontactos', "add", $user['perfil_id'], 'Entidads');
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('entidad', 'cdocidtipos', 'centidadtipos', 'centidadrols', 'cestados', 'entidads', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['entidad']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Entidad id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Entidads->exists(['Entidads.id' => $id, 'Entidads.trash' => 0])) {
                $entidad = $this->Entidads->get($id, [
                    'contain' => ['Centidadtipos']
                ]);

                $this->loadModel('Entidadreds');
                $entidadreds = $this->Entidadreds->find()->where(['Entidadreds.entidadmadre' => $id])->all();

                if ($this->request->is(['patch', 'post', 'put'])) {
                    $data = $this->request->getData();
                    $docidnull = ($data['docidnull'] == '1') ? true : false;
                    $tipodoc = $this->entTipoDocumento($data['nacional']);

                    // Si el campo documento no disponible no estaba chequeado y fue chequeado
                    if(($entidad->docidnull != $docidnull) && ($docidnull)) {
                        if ($this->Entidads->exists(['Entidads.docid' => $entidad->codigo . $entidad->id])) {
                            $max_corr = $this->Entidads->find()
                                ->select(['docid_max' => 'MAX(Entidads.docid)'])
                                ->where(['Entidads.docid LIKE' => $entidad->codigo . $entidad->id . '%'])
                                ->toArray();

                            $correlativo = (int)(substr($max_corr[0]->docid_max, strlen($entidad->codigo . $entidad->id))) + 1;
                            if($correlativo < 10) {
                                $docid = $entidad->codigo . $entidad->id . '0' . $correlativo;
                            } else {
                                $docid = $entidad->codigo . $entidad->id . $correlativo;
                            }
                        } else {
                            $docid = $entidad->codigo . $entidad->id;
                        }
                    } elseif(($entidad->docidnull != $docidnull) && (!$docidnull)) {
                        $docid = $data['docid'];
                        $docid = str_replace("-", "", $docid);
                    } else {
                        $docid = $entidad->docid;
                    }

                    $entidad = $this->Entidads->patchEntity($entidad, $data);
                    $entidad->usuariomodif = $this->Auth->user('username');
                    $entidad->modified = date('Y-m-d H:i:s');
                    $entidad->docid = $docid;
                    $entidad->cdocidtipo_id=$tipodoc;

                    if ($this->Entidads->save($entidad)) {
                        if(isset($this->request->getData()['entidad'])) {
                            $ban = 0;
                            if(count($this->request->getData()['entidad']) == count($entidadreds)) {
                                foreach ($entidadreds as $key => $entidadred) {
                                    //if($data['entidad'][$key] != '') {
                                        if($data['entidad'][$key] != $entidadred->entidadhija) {
                                            $ban++;
                                        }
                                    //}
                                }
                            } else {
                                if(count($data['entidad']) > 0) {
                                    $ban = 1;
                                }
                            }

                            if($ban > 0) {
                                foreach ($entidadreds as $entidadred) {
                                    $this->Entidadreds->delete($entidadred);
                                }

                                foreach ($this->request->getData()['entidad'] as $entidad_hija) {
                                    if($entidad_hija != '') {
                                        $entidadreds = TableRegistry::get('Entidadreds');
                                        $entidadred  = new Entidadred();
                                        $entidadred->entidadmadre = $entidad->id;
                                        $entidadred->entidadhija = $entidad_hija;
                                        $entidadred->vinculo = 1;
                                        $entidadred->usuario = $this->Auth->user('username');
                                        $entidadred->created = date('Y-m-d H:i:s');
                                        $entidadreds->save($entidadred);
                                    }
                                }
                            }
                        }

                        //$this->Flash->success(__('The entidad has been saved.'));
                        $_SESSION["entidad-save"] = 1;
                        return $this->redirect(['action' => 'view', $entidad->id]);
                    }
                    //$this->Flash->error(__('The entidad could not be saved. Please, try again.'));
                }
                $cdocidtipos = $this->Entidads->Cdocidtipos->find('list')
                    ->where(['Cdocidtipos.trash' => 0])
                    ->andWhere(['Cdocidtipos.id IN' => $this->docidtipos]);
                $centidadtipos = $this->Entidads->Centidadtipos->find('list')
                    ->where(['Centidadtipos.trash' => 0])
                    ->andWhere(['Centidadtipos.cestado_id' => $this->estadoActivo]);
                $centidadrols = $this->Entidads->Centidadrols->find('list')
                    ->where(['Centidadrols.trash' => 0])
                    ->andWhere(['Centidadrols.cestado_id' => $this->estadoActivo]);
                $cestados = $this->Entidads->Cestados->find('list')
                    ->where(['Cestados.trash' => 0])
                    ->andWhere(['Cestados.id IN' => $this->estadosId]);

                $entidads = $this->Entidads->find('list')
                    ->where(['Entidads.trash' => 0])
                    ->andWhere(['Entidads.id !=' => $id])
                    ->andWhere(['Entidads.cestado_id' => $this->estadoActivo]);

                $estilo_no_disponible = ($entidad->centidadtipo->docidreq) ? 'style = "display: none"' : '';

                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools=$permisos->getControlToolMultipleModels($this->modelo ,["exportPdf", "add",'imprimir','edit'], 'Entidadcontactos');
                $nav = $permisos->getRuta('Entidadcontactos',"edit",$user['perfil_id'], 'Entidads');
                $titulo = $permisos->getTitle($this->modelo);

                $this->set(compact('entidad', 'entidads', 'cdocidtipos', 'centidadtipos', 'centidadrols', 'estilo_no_disponible', 'cestados', 'entidadreds', 'controltools', 'nav', 'titulo'));
                $this->set('_serialize', ['entidad']);
            } else {
                $this->Flash->erroracceso(__('La entidad no existe o ha sido eliminada'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
            }
        }
    }

    public function valunique() {
        $campo = $_POST['campo'];
        $id = $_POST['id'];
        $tipo= $_POST['tipo'];

        $flag=0;
        $msj="";
        $label ="";
        switch($tipo){
            case 'nombre':
                $flag=1;
                $msj="El ";
                $label ="nombre corto";
                break;
            case 'codigo':
                $flag=1;
                $msj="El ";
                $label ="código";
                break;
            case 'docid':
                $flag=1;
                $msj="El ";
                $label ="documento de identidad";
                break;
            default:
                $flag=0;
        }

        if($flag==1){
            $this->Entidads->recursive=-1;
            if($id != 0){
                $info = $this->Entidads->find("all")
                    ->where(['Entidads.'.$tipo=>$campo, 'Entidads.id !='=>$id, 'Entidads.trash'=>0]);
            }else{
                $info = $this->Entidads->find("all")
                    ->where(['Entidads.'.$tipo=>$campo, 'Entidads.trash'=>0]);
            }
            if($info->count()>0){
                echo json_encode(["error"=>1,"msj"=>$msj.$label." de la entidad ya existe."]);
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
     * @param string|null $id Entidad id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $this->loadModel('Entidadcontactos');
            $entidadcontacto = $this->Entidadcontactos->find()
                ->select(['Entidadcontactos.contacto_id'])
                ->where(['Entidadcontactos.entidad_id' => $id])
                ->all()
                ->toArray();

            $conn = ConnectionManager::get('dbpriv');
            $esquema = $conn->config()["database"];
            if(count($entidadcontacto) > 0) {
                $_SESSION["ExiteContactos"] = 1;
                return $this->redirect(['controller' => 'entidadcontactos', 'action' => 'index']);
            } else {
                $conn->execute("CALL sp_eliminar_registro('".$esquema."','entidads',".$id.")");
                $_SESSION["EntidadDele"] = 1;

                return $this->redirect(['controller' => 'entidadcontactos', 'action' => 'index']);
            }
        }
    }

    public function getTipoDocumento() {
        $id = $_POST['id'];
        $nacional="";
        switch ($id)
        {
            case 0:
                $nacional="docidentidadext";
                break;
            case 1:
                $nacional="docidentidadnac";
                break;
        }

        $conn = ConnectionManager::get('dbtransac');
        $stmt = $conn->execute("SELECT json_extract(params,CONCAT('$[0].','','".$nacional."')) as doc FROM cpreferences where json_extract(params,CONCAT('$[0].','','".$nacional."')) IS NOT NULL");
        $idDoc = $stmt ->fetchAll('assoc')[0]['doc'];

        $this->loadModel('Cdocidtipos');
        $this->Cdocidtipos->recursive = -1;
        $cdocidtipo = $this->Cdocidtipos->find()
            ->select(['Cdocidtipos.mascara'])
            ->where(['Cdocidtipos.id' => intval($idDoc)])
            ->first();

        if(count($cdocidtipo) > 0){

            echo json_encode(["error" => 0, "msj" => "", "data" => $cdocidtipo]);
        } else {
            echo json_encode(["error" => 1, "msj" => "Tipo de documento seleccionado no existe"]);
        }

        $this->autoRender = false;
    }

    public function entTipoDocumento($id) {
        $nacional="";
        switch ($id)
        {
            case 0:
                $nacional="docidentidadext";
                break;
            case 1:
                $nacional="docidentidadnac";
                break;
        }

        $conn = ConnectionManager::get('dbtransac');
        $stmt = $conn->execute("SELECT json_extract(params,CONCAT('$[0].','','".$nacional."')) as doc FROM cpreferences where json_extract(params,CONCAT('$[0].','','".$nacional."')) IS NOT NULL");
        $idDoc = $stmt ->fetchAll('assoc')[0]['doc'];

        $this->loadModel('Cdocidtipos');
        $this->Cdocidtipos->recursive = -1;

        $cdocidtipo = $this->Cdocidtipos->find()
            ->select(['Cdocidtipos.id'])
            ->where(['Cdocidtipos.id' => intval($idDoc)])
            ->first();
        $cdocid="";
        if(count($cdocidtipo) > 0){

            $cdocid=$cdocidtipo->id ;
        }
      return $cdocid;
    }

    public function verifyrequireddocid() {
        $id = $_POST['centidadtipo_id'];

        $this->loadModel('Centidadtipos');
        $this->Centidadtipos->recursive = -1;
        $centidadtipo = $this->Centidadtipos->find()
            ->where(['Centidadtipos.id' => $id])
            ->andWhere(['Centidadtipos.trash' => 0])
            ->first();

        if(count($centidadtipo) > 0){

            echo json_encode(["error" => 0, "msj" => "", "data" => $centidadtipo]);
        } else {
            echo json_encode(["error" => 1, "msj" => "Tipo de entidad seleccionado no existe"]);
        }

        $this->autoRender = false;
    }

    public function saveallquestions(){
        $formdinamic_id = $_POST['formdinamic_id'];
        $dataform_id = (isset($_SESSION['fd_id_dataform'])) ? $_SESSION['fd_id_dataform'] : $_POST['dataform_id'];

        if($dataform_id > 0) {
            $this->loadModel("Formseccions");
            $this->loadModel("Formpreguntas");
            $this->loadModel("Formrespuestas");
            $this->loadModel("Dataformdets");
            //obteniendo secciones padre del formulario indicado
            $seccionesPadre=$this->Formseccions->find('list',[
                'keyField' => 's',
                'valueField' => 'id'
            ])
                ->where(['formdinamic_id'=>$formdinamic_id])
                ->andwhere(['formseccion_id'=>0])
                ->andwhere(['trash'=>0])
                ->toArray();
            $secpreg=array();
            for($sec=0; $sec<count($seccionesPadre); $sec++){
                //obteniendo secciones hijas si es que posee
                $seccionesHijas=$this->Formseccions->find()
                    ->where(['formdinamic_id'=>$formdinamic_id])
                    ->andwhere(['formseccion_id'=>$seccionesPadre[$sec]])
                    ->andwhere(['trash'=>0]);
                if(count($seccionesHijas->toArray())>0){
                    foreach($seccionesHijas as $seckey){
                        array_push($secpreg,$seckey->id);//se agregan al array secciones hijas y no padre
                    }
                }else array_push($secpreg,$seccionesPadre[$sec]);//se agregan secciones padre que no poseen secciones hijas
            }

            //preguntas según array de secciones
            $preguntas=$this->Formpreguntas->find()
                ->where(['formseccion_id IN '=>$secpreg])
                ->andwhere(['trash'=>0]);
            $requiredpreg=array();//almacenador de ids de preguntas requeridas con value a true(preguntas obligatorias de llenar)
            foreach($preguntas as $pregkey){
                $resp_preg_reg=$this->Formrespuestas->find()
                    ->where(['formpregunta_id'=>$pregkey->id])
                    ->where(['trash'=>0])
                    ->count();
                if($resp_preg_reg>0){
                    if($pregkey->params['Formpregunta']['items'][0]['value']) array_push($requiredpreg,$pregkey->id);
                }
            }
            $countpregrq=count($requiredpreg); //conteo de preguntas requeridas

            //consulta para obtener respuestas según preguntas requeridas
            $countresprq=0;//conteo de preguntas requeridas con respuesta
            if($countpregrq>0) {
                $respuestas = $this->Dataformdets->find()
                    ->where(["dataform_id" => $_SESSION['fd_id_dataform']])
                    ->andwhere(["pregunta_id IN " => $requiredpreg])
                    ->andwhere(["jsonrespuesta IS NOT" => null])
                    ->andwhere(["trash" => 0]);
                foreach ($respuestas as $respkey) {
                    $flag = true;
                    $contparcial = 0;
                    foreach ($respkey->jsonrespuesta as $jsonkey) {
                        foreach ($jsonkey as $itemskey) {
                            if ($itemskey['items'][0]['value'] == null || $itemskey['items'][0]['value'] == "") $flag = false;
                            else $contparcial++;
                        }
                    }
                    $countresprq += $contparcial;
                }
            }

            if ($countpregrq > $countresprq) {
                $requeridas = false;
            } else {
                $_SESSION['formcompleted'] = 1;
                $requeridas = true;
            }
        } else {
            $requeridas = false;
            $countpregrq = 0;
            $countresprq = 0;
        }

        echo json_encode(["dataform_id" => $dataform_id, "requeridas" => $requeridas, "countpregrq" => $countpregrq, "countresprq" => $countresprq]);

        $this->autoRender = false;
    }
}
