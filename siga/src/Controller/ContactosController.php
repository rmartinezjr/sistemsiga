<?php
namespace App\Controller;

use App\Model\Entity\Entidadcontacto;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Contactos Controller
 *
 * @property \App\Model\Table\ContactosTable $Contactos
 *
 * @method \App\Model\Entity\Contacto[] paginate($object = null, array $settings = [])
 */
class ContactosController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Contactos";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";

        $cestados = new CestadosController();
        // Se obtienen los estados activo e inactivo de la configuracion general
        $this->estadosId = $cestados->getEstados(false);

        // Se obtiene el estado activo de la configuracion general
        $this->estadoActivo = $cestados->getEstados(true);

        $pages = new PagesController();
        // Se obtiene el estado en espera de aprobacion de la configuracion general
        $this->estadoEsperaAprobacion = $pages->getEstadoAprobacion();

        // Se obtienen los tipos de documento para contactos de la configuacion general
        $docidtipos = new CdocidtiposController();
        $this->docidtiposcontactos = $docidtipos->getTipoDocumento('Contactos');

        $this->docidtipos = $docidtipos->getTipoDocumento('Contactos');

        // Se obtienen los tipos de documento para entidades de la configuacion general
        $docidtipos = new CdocidtiposController();
        $this->docidtiposentidades = $docidtipos->getTipoDocumento('Entidads');

        //cargando modelos para la carga de datos
        $this->loadModel('Dataforms');
        $this->loadModel('Formdinamics');
        $this->loadModel('Formdinamicwfs');
        $this->loadModel('Wfetapas');
        $this->loadModel('Perfils');
        $this->loadModel('Cdatotipos');
        $this->loadModel('Contactofds');

        $valid= new JsonController();
        $this->estado_archivado=$valid->preferencesLevel1("Planificación", "archivado");
    }

    /**
     * Index method
     * @param string|null $id Entidad id.
     *
     * @return \Cake\Http\Response|void
     */
    public function index($id = null, $band = null)
    {
        $paginacion = ($band==null)?20:1000000;

        $query = $this->Contactos->find();
        if(count($this->request->getData()) == 0) {
            if(is_null($id)) {
                if(isset($_SESSION["tabla[$this->modelo]"])) {
                    $data = $_SESSION["tabla[$this->modelo]"]['data'];
                    $busqueda = new EntidadcontactosController();
                    $query = $busqueda->realizarBusqueda($data, $this->modelo);
                    $query->andWhere([$this->modelo . '.trash' => 0])
                          ->order([$this->modelo . '.nombres'=>"asc"])
                          ->limit($paginacion)
                          ->contain(['Cdocidtipos', 'Ccontactotipos', 'Cestados']);
                } else {
                    $query->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$this->modelo . '.nombres'=>"asc"])
                        ->limit($paginacion)
                        ->contain(['Cdocidtipos', 'Ccontactotipos', 'Cestados']);
                }
            } else {
                unset($_SESSION["tabla[$this->modelo]"]);
                $arrayContactos = $this->filtrocontactos($id);

                $query = $this->Contactos->find();
                if(count($arrayContactos) > 0) {
                    $query->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Contactos.id IN' => $arrayContactos])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$this->modelo . '.nombres'=>"asc"])
                        ->limit($paginacion)
                        ->contain(['Cdocidtipos', 'Ccontactotipos', 'Cestados']);
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
                            ->contain(['Cdocidtipos', 'Ccontactotipos', 'Cestados']);
                    } else {
                        $query->where([$this->modelo . '.trash' => 0])
                            ->andWhere(['Cestados.id' => 1])
                            ->order([$data['modelo'] . '.' . $data['order'] => $data['tipoorder']])
                            ->limit($paginacion)
                            ->contain(['Cdocidtipos', 'Ccontactotipos', 'Cestados']);
                    }
                }  else if($data['load'] == 'busqueda') {
                    $busqueda = new EntidadcontactosController();
                    $query = $busqueda->realizarBusqueda($data, $this->modelo);
                    $query->andWhere([$this->modelo . '.trash' => 0])
                          ->order([$this->modelo . '.nombres'=>"asc"])
                          ->limit($paginacion)
                          ->contain(['Cdocidtipos', 'Ccontactotipos', 'Cestados']);
                }
            } else {
                unset($_SESSION["tabla[$this->modelo]"]);
                $arrayContactos = $this->filtrocontactos($id);

                $query = $this->Contactos->find();
                if(count($arrayContactos) > 0) {
                    $query->where([$this->modelo . '.trash' => 0])
                        ->andWhere(['Contactos.id IN' => $arrayContactos])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$data['modelo'] . '.' . $data['order'] => $data['tipoorder']])
                        ->limit($paginacion)
                        ->contain(['Cdocidtipos', 'Ccontactotipos', 'Cestados']);
                } else {
                    $query->where([$this->modelo . '.id' => 0]);
                }
            }
        }
        $contactos = $this->Paginator->paginate($query);

        $cestados = $this->Contactos->Cestados->find('list')
            ->where(['Cestados.trash' => 0])
            ->andWhere(['Cestados.id IN' => $this->estadosId]);

        $user = $this->Auth->user();
        $permisos = new VerificacionPermisosController();
        $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);

        $this->set(compact('contactos', 'cestados', 'herramientas'));
        $this->set('_serialize', ['contactos']);
    }

    public function vertodos() {
        unset($_SESSION["tabla[$this->modelo]"]);
        $this->redirect(array('controller'=> 'entidadcontactos', 'action'=> "index"));
        $this->autoRender=false;
    }

    public function filtrocontactos($id = null)
    {
        $this->loadModel('Entidadcontactos');
        $contactos = $this->Entidadcontactos->find()
            ->select(['Entidadcontactos.contacto_id'])
            ->where(['Entidadcontactos.entidad_id' => $id])
            ->all();

        $arrayContactos = [];
        foreach ($contactos as $value) {
            array_push($arrayContactos, $value->contacto_id);
        }

        return $arrayContactos;
        $this->autoRender=false;
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
     * @param string|null $id Contacto id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Contactos->exists(['Contactos.id' => $id, 'Contactos.trash' => 0])) {
                $contacto = $this->Contactos->get($id, [
                    'contain' => ['Cdocidtipos', 'Ccontactotipos', 'Cestados', 'Entidadcontactos', 'Users', "Cpaises"]
                ]);

                $this->loadModel('Entidads');
                $entidads = [];
                foreach ($contacto->entidadcontactos as $entidadcontacto) {
                    $entidad = $this->Entidads->get($entidadcontacto->entidad_id, [
                        'contain' => ['Cdocidtipos', 'Centidadtipos', 'Centidadrols', 'Cestados']
                    ]);
                    array_push($entidads, $entidad);
                }

                //Obteniendo formularios asignados
                $json= new JsonController();
                $formasig=$this->Contactofds->find()
                    ->Where(['Contactofds.ccontactotipo_id' => $contacto->ccontactotipo_id])
                    ->andwhere(['Contactofds.trash'=>0])
                    ->andwhere(['Contactofds.vinculo'=>1])
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
                foreach ($formasig as $key ) {
                    $dataformasig=$this->Dataforms->find()
                        ->Where(['formdinamic_id'=>$key->formdinamic_id])
                        ->andWhere(['registro_id'=>$id])
                        ->andWhere(['modelo'=>$this->modelo])
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
                                ->andWhere(['registro_id'=>$id])
                                ->andWhere(['modelo'=>$this->modelo])
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
                $nav = $permisos->getRuta('Entidadcontactos', "view", $user['perfil_id'], 'Contactos');
                $titulo = $permisos->getTitle($this->modelo);
                $function="viewFormDinamics/";

                $this->set(compact('contacto', 'entidads', 'controltools', 'nav', 'titulo','formasig','arraytipo','arraymulti','function','id_dataform','acciones','functions','view_inicial','permitir_carga','carga','cont_carga'));
                $this->set('_serialize', ['contacto']);
            } else {
                $this->Flash->erroracceso(__('El contacto no existe o ha sido eliminado'));
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

    /*Función para generar Formularios Dinamicos*/
    public function viewFormDinamics($id_formdinamic=null, $id_contacto=null, $id_dataform=null,$direction=null){
        if(!empty($id_dataform)){
            if(!$this->cargaDatosPerfilComun($id_dataform)) return $this->redirect(['action' => 'view',$id_contacto]);
        }

        if(empty($id_formdinamic))
            return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);

        $formdinamic=$this->Formdinamics->get($id_formdinamic);
        if($formdinamic->cestado_id==$this->estado_archivado) //si el formulario es archivado dirigir a show si la acción es viewFormDinamics
            return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);

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

        if($vinculo==0)return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);
        $escritura=1;
        if(!empty($id_dataform)){
            $dataform=$this->Dataforms->get($id_dataform);
            $cestado=$this->Wfetapas->get($dataform->wfetapa_id,
                ['contain'=>['cestados']]);
            
            $_SESSION['fd_id_dataform']=$id_dataform;
            $perfil=$this->Perfils->get($this->Auth->user('perfil_id'));
            if(strcmp("Súper Administrador",$perfil->nombre)!=0){
                $function=$this->accionPrivilegios($dataform->wfetapa_id);
                if(strcmp($function,'viewFormDinamics/')!=0) return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);
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
        $_SESSION['fd_id_contacto']=$id_contacto;
        if(empty($id_dataform)){
            unset($_SESSION['fd_id_dataform']);
            $id_dataform=0;
        }
        else $access=1;//variable para identificar es ques edición parcial
        $formdinamic=$this->Formdinamics->get($id_formdinamic);

        if(empty($id_dataform)){
            $dataformasig=$this->Dataforms->find()
                ->Where(['formdinamic_id'=>$id_formdinamic])
                ->andWhere(['registro_id'=>$id_contacto])
                ->andWhere(['modelo'=>$this->modelo])
                ->andWhere(['trash'=>0]);
            //Multireg=false sin registro de formulario lleno o llenandose
            if($formdinamic->params['FormDinamic']['items'][1]['value']==false && $dataformasig->count()==0)$pass=1;
            //Multireg=true sin registro de formulario lleno o llenandose
            elseif($formdinamic->params['FormDinamic']['items'][1]['value']==true && $dataformasig->count()==0)$pass=1;
            elseif($formdinamic->params['FormDinamic']['items'][1]['value']==true && $formdinamic->params['FormDinamic']['items'][2]['value']==true && $dataformasig->count()!=0) $pass=1;
            elseif($formdinamic->params['FormDinamic']['items'][1]['value']==true && $formdinamic->params['FormDinamic']['items'][2]['value']==false && $dataformasig->count()!=0){
                $dataformasigAux=$this->Dataforms->find()
                    ->Where(['formdinamic_id'=>$id_formdinamic])
                    ->andWhere(['registro_id'=>$id_contacto])
                    ->andWhere(['modelo'=>$this->modelo])
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
            return $this->redirect(['action'=>'view',$id_contacto]);

        $tituloobj='Formulario';
        $contacto = $this->Contactos->get($id_contacto, [
            'contain' => ['Cdocidtipos', 'Ccontactotipos', 'Cestados', 'Entidadcontactos', 'Users']
        ]);

        $permisos = new VerificacionPermisosController();
        $controltools=$permisos->getControlToolMultipleModels($this->modelo ,["exportPdf","add",'imprimir','index','edit'], 'Entidadcontactos');
        $user = $this->Auth->user();
        //$nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
        $nav = $permisos->getRuta('Entidadcontactos', "view", $user['perfil_id'],'contactos');
        $nav['nav'][0]['alias']="Contactos";
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
        $this->set(compact("controltools","nav",'titulo','tituloobj','contacto','formdinamic','datos','cont','countfp','id_dataform','access','acciones','escritura','carga'));
        $this->set('_serialize', ['forms']);
    }

    public function show($id_formdinamic=null, $id_contacto=null, $id_dataform=null,$direction=null){
        if(empty($id_formdinamic) || empty($id_dataform))
            return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);
        $acciones=[];
        //obteniendo wf del formdinamic a true
        $vinculo=$this->Formdinamicwfs->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andwhere(['vinculo'=>1])
            ->andwhere(['trash'=>0])
            ->count();
        $formdinamic=$this->Formdinamics->get($id_formdinamic);
        if($vinculo==0)return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);
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
                if(strcmp($function,'show/')!=0) return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);
            }
        }

        $tituloobj='Formulario';
        $contacto = $this->Contactos->get($id_contacto, [
            'contain' => ['Cdocidtipos', 'Ccontactotipos', 'Cestados', 'Entidadcontactos', 'Users']
        ]);
        $permisos = new VerificacionPermisosController();
        $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","add",'imprimir','index']);
        $user = $this->Auth->user();
        $nav = $permisos->getRuta('Entidadcontactos', "view", $user['perfil_id'],'contactos');
        $nav['nav'][0]['alias']="Contactos";
        $nav["complemento"][0]["alias"]=$formdinamic->nombre;
        $titulo = $permisos->getTitle($this->modelo);

        $model='Formseccions';
        $form= new LlenarFormDinamicsController();
        $datos=$form->crearForm($id_formdinamic,$model,$id_dataform,$direction);
        $cont=1;
        $countfp=1;
        $_SESSION['fd_if_formdinamic']=$id_formdinamic;
        $_SESSION['fd_id_contacto']=$id_contacto;
        /*Obteniendo estado carga de datos*/
        $carga=null;
        if(!empty($id_dataform)){
            $etapa=$this->Dataforms->get($id_dataform);
            $carga=$this->Wfetapas->get($etapa->wfetapa_id,[
                'contain' => ['Cestados']
            ]);
        }
        $this->set(compact("controltools","nav",'titulo','tituloobj','contacto','formdinamic','datos','cont','countfp','acciones','id_dataform','carga'));
        $this->set('_serialize', ['forms']);
    }

    public function observations($id_formdinamic=null, $id_contacto=null, $id_dataform=null,$direction=null){
        if(empty($id_formdinamic) || empty($id_dataform))
            return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);

        if(!$this->cargaDatosPerfilComun($id_dataform)) return $this->redirect(['action' => 'view',$id_contacto]);

        $formdinamic=$this->Formdinamics->get($id_formdinamic);
        if($formdinamic->cestado_id==$this->estado_archivado) //si el formulario es archivado dirigir a show si la acción es viewFormDinamics
            return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);

        $acciones=[];
        //obteniendo wf del formdinamic a true
        $vinculo=$this->Formdinamicwfs->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andwhere(['vinculo'=>1])
            ->andwhere(['trash'=>0])
            ->count();
        if($vinculo==0)return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);

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
                if(strcmp($function,'observations/')!=0) return $this->redirect(['controller' => 'contactos', 'action' => 'view', $id_contacto]);
            }
        }

        $tituloobj='Formulario';
        $contacto = $this->Contactos->get($id_contacto, [
            'contain' => ['Cdocidtipos', 'Ccontactotipos', 'Cestados', 'Entidadcontactos', 'Users']
        ]);

        $permisos = new VerificacionPermisosController();
        $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","add",'imprimir','index']);
        $user = $this->Auth->user();
        //$nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
        $nav = $permisos->getRuta('Entidadcontactos', "view", $user['perfil_id'],'contactos');
        $nav['nav'][0]['alias']="Contactos";
        $nav["complemento"][0]["alias"]=$formdinamic->nombre;
        $titulo = $permisos->getTitle($this->modelo);

        $model='Formseccions';
        $form= new LlenarFormDinamicsController();
        $datos=$form->crearForm($id_formdinamic,$model,$id_dataform,$direction);
        $cont=1;
        $countfp=1;
        $_SESSION['fd_if_formdinamic']=$id_formdinamic;
        $_SESSION['fd_id_contacto']=$id_contacto;
        /*Obteniendo estado carga de datos*/
        $carga=null;
        if(!empty($id_dataform)){
            $etapa=$this->Dataforms->get($id_dataform);
            $carga=$this->Wfetapas->get($etapa->wfetapa_id,[
                'contain' => ['Cestados']
            ]);
        }
        $this->set(compact("controltools","nav",'titulo','tituloobj','contacto','formdinamic','datos','cont','countfp','acciones','id_dataform','carga', 'congelado'));
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
                    $dataform->codigo=self::generateCod($_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_contacto']);
                    $dataform->formdinamic_id=$_SESSION['fd_if_formdinamic'];
                    $dataform->registro_id=$_SESSION['fd_id_contacto'];
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

    public function generateCod($id_formdinamic, $id_contacto){
        $formdinamic=$this->Formdinamics->get($id_formdinamic);
        $nombre=substr($formdinamic->nombre,0,2);
        $countdataforms=$this->Dataforms->find()
            ->where(['formdinamic_id'=>$id_formdinamic])
            ->andWhere(['registro_id'=>$id_contacto])
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
                return $this->redirect(['action' => 'view',$_SESSION['fd_id_contacto']]);

            if(!$this->cargaDatosPerfilComun($_SESSION['fd_id_dataform'])) $this->redirect(['action' => 'view',$_SESSION['fd_id_contacto']]);
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
                                    $_SESSION["contacto-save"]=2;
                                    return $this->redirect(['action' => 'view',$_SESSION['fd_id_contacto']]);
                                }
                                else{
                                    $_SESSION["wf-save"]=0;
                                    return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_contacto'],$_SESSION['fd_id_dataform']]);

                                }
                            }else{
                                return $this->redirect(['action' => 'view',$_SESSION['fd_id_contacto']]);
                            }
                        }else{
                            $_SESSION["wf-save"]=-1;
                            return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_contacto'],$_SESSION['fd_id_dataform']]);
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
                                $_SESSION["contacto-save"]=2;
                                return $this->redirect(['action' => 'view',$_SESSION['fd_id_contacto']]);
                            }
                            else{
                                $_SESSION["wf-save"]=0;
                                return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_contacto'],$_SESSION['fd_id_dataform']]);

                            }
                        }else{
                            return $this->redirect(['action' => 'view',$_SESSION['fd_id_contacto']]);
                        }
                    }
                }else{
                    $_SESSION["wf-save"]=0;
                    return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_contacto'],$_SESSION['fd_id_dataform']]);
                }
            }else{
                $_SESSION["wf-save"]=0;
                return $this->redirect(['action'=>$action,$_SESSION['fd_if_formdinamic'],$_SESSION['fd_id_contacto'],$_SESSION['fd_id_dataform']]);
            }
        }else{
            return $this->redirect(['action' => 'view',$_SESSION['fd_id_contacto']]);
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
            $contacto = $this->Contactos->newEntity();
            if ($this->request->is('post')) {

                $nacional2="";
                $docid=$this->request->getData()['docid'];

                switch ($this->request->getData()['nacional'])
                {
                    case 0:
                        $nacional2="Extranjero";
                        break;
                    case 1:
                        $nacional2="Salvadoreño";
                        break;
                }

                $tipodoc=$this->conTipoDocumento($this->request->getData()['nacional']);
                $contacto = $this->Contactos->patchEntity($contacto, $this->request->getData());
                $contacto->usuario = $this->Auth->user('username');
                $contacto->nacional=$nacional2;
                $contacto->cdocidtipo_id=$tipodoc;
                $contacto->created = date('Y-m-d H:i:s');
                $contacto->docid =str_replace("-", "", $docid);
                $contacto->trash = 0;
                $contacto->modified = null;

                 if ($this->Contactos->save($contacto)) {
                     if(isset($this->request->getData()['entidad'])) {
                         foreach ($this->request->getData()['entidad'] as $entidad) {
                             $entidadcontactos = TableRegistry::get('Entidadcontactos');
                             $entidadcontacto  = new Entidadcontacto();
                             $entidadcontacto->contacto_id = $contacto->id;
                             $entidadcontacto->entidad_id = $entidad;
                             $entidadcontacto->usuario = $this->Auth->user('username');
                             $entidadcontacto->created = date('Y-m-d H:i:s');
                             $entidadcontactos->save($entidadcontacto);
                         }
                     }

                    //$this->Flash->success(__('The contacto has been saved.'));
                    $_SESSION["contacto-save"] = 1;
                    return $this->redirect(['action' => 'view', $contacto->id]);
                }
                //$this->Flash->error(__('The contacto could not be saved. Please, try again.'));
            }
            $cdocidtipos = $this->Contactos->Cdocidtipos->find('list')
                ->where(['Cdocidtipos.trash' => 0])
                ->andWhere(['Cdocidtipos.id IN' => $this->docidtipos]);
            $ccontactotipos = $this->Contactos->Ccontactotipos->find('list')
                ->where(['Ccontactotipos.trash' => 0])
                ->andWhere(['Ccontactotipos.cestado_id' => $this->estadoActivo]);
            $cestados = $this->Contactos->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $this->loadModel('Entidads');
            $entidads = $this->Entidads->find('list')
                ->where(['Entidads.trash' => 0])
                ->andWhere(['Entidads.cestado_id' => $this->estadoActivo]);
            $cpaises = $this->Contactos->Cpaises->find("list")
                ->where(["Cpaises.cestado_id"=>$this->estadoActivo,"Cpaises.trash"=>0]);
            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools=$permisos->getControlToolMultipleModels($this->modelo ,["exportPdf","add",'imprimir','edit'], 'Entidadcontactos');
            $nav = $permisos->getRuta('Entidadcontactos', "add",$user['perfil_id'], 'Contactos');
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('cpaises','contacto', 'entidads', 'cdocidtipos', 'ccontactotipos', 'cestados', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['contacto']);
        }
    }
    public function getTipoDocumento($modelo)
    {
        $this->loadModel('Cpreferences');
        if ($this->Cpreferences->exists(['Cpreferences.id' => 1, 'Cpreferences.trash' => 0])) {
            $general = $this->Cpreferences->find()
                ->select(['Cpreferences.params'])
                ->where(['Cpreferences.id' => 1])
                ->first()
                ->toArray();
            $configuraciones = $general['params'];

            if($modelo == 'Entidads') {
                return [$configuraciones['docidentidadnac'], $configuraciones['docidentidadext']];
            } elseif($modelo == 'Contactos') {
                return [$configuraciones['docidtipocontactonac'], $configuraciones['docidtipocontactoext']];
            }
        } else {
            return [0];
        }
    }
    /**
     * Edit method
     *
     * @param string|null $id Contacto id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            if ($this->Contactos->exists(['Contactos.id' => $id, 'Contactos.trash' => 0])) {
                $contacto = $this->Contactos->get($id, [
                    'contain' => []
                ]);

                $this->loadModel('Entidadcontactos');
                $entidadcontactos = $this->Entidadcontactos->find()
                    ->where(['Entidadcontactos.contacto_id' => $id])
                    ->all();

                if ($this->request->is(['patch', 'post', 'put'])) {
                    $contacto = $this->Contactos->patchEntity($contacto, $this->request->getData());
                    $user = [];
                    $nacional2="";

                    $docid=$this->request->getData()['docid'];

                    switch ($this->request->getData()['nacional'])
                    {
                        case 0:
                            $nacional2="Extranjero";

                            break;
                        case 1:
                            $nacional2="Salvadoreño";

                            break;
                    }
                    $tipodoc=$this->conTipoDocumento($this->request->getData()['nacional']);
                    if($contacto->cestado_id != $this->estadoActivo) {
                        $this->loadModel('Users');
                        $user = $this->Users->find()
                            ->where(['Users.contacto_id' => $id])
                            ->andWhere(['Users.trash' => 0])
                            ->all()
                            ->toArray();
                    }

                    if(count($user) > 0) {
                        $_SESSION["UserRelated"] = 1;
                    } else {
                       // $tipodoc=$this->getTipoDocumento($this->modelo);

                        $contacto->usuariomodif = $this->Auth->user('username');
                        $contacto->modified = date('Y-m-d H:i:s');
                        $contacto->nacional=$nacional2;
                        $contacto->cdocidtipo_id=$tipodoc;
                        $contacto->created = date('Y-m-d H:i:s');
                        $contacto->docid =str_replace("-", "", $docid);
                        if ($this->Contactos->save($contacto)) {
                            if(isset($this->request->getData()['entidad'])) {
                                foreach ($entidadcontactos as $entidadcontacto) {
                                    $this->Entidadcontactos->delete($entidadcontacto);
                                }

                                foreach ($this->request->getData()['entidad'] as $entidad) {
                                    $entidadcontactos = TableRegistry::get('Entidadcontactos');
                                    $entidadcontacto  = new Entidadcontacto();
                                    $entidadcontacto->contacto_id = $contacto->id;
                                    $entidadcontacto->entidad_id = $entidad;
                                    $entidadcontacto->usuario = $this->Auth->user('username');
                                    $entidadcontacto->created = date('Y-m-d H:i:s');
                                    $entidadcontactos->save($entidadcontacto);
                                }
                            }

                            //$this->Flash->success(__('The contacto has been saved.'));
                            $_SESSION["contacto-save"] = 1;
                            return $this->redirect(['action' => 'view', $contacto->id]);
                        }
                        //$this->Flash->error(__('The contacto could not be saved. Please, try again.'));
                    }
                }
                $cdocidtipos = $this->Contactos->Cdocidtipos->find('list')
                    ->where(['Cdocidtipos.trash' => 0])
                    ->andWhere(['Cdocidtipos.id IN' => $this->docidtipos]);
                $ccontactotipos = $this->Contactos->Ccontactotipos->find('list')
                    ->where(['Ccontactotipos.trash' => 0])
                    ->andWhere(['Ccontactotipos.cestado_id' => $this->estadoActivo]);
                $cestados = $this->Contactos->Cestados->find('list')
                    ->where(['Cestados.trash' => 0])
                    ->andWhere(['Cestados.id IN' => $this->estadosId]);

                $this->loadModel('Entidads');
                $entidads = $this->Entidads->find('list')
                    ->where(['Entidads.trash' => 0])
                    ->andWhere(['Entidads.cestado_id' => $this->estadoActivo]);
                $cpaises = $this->Contactos->Cpaises->find("list")
                    ->where(["Cpaises.cestado_id"=>$this->estadoActivo,"Cpaises.trash"=>0]);
                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools=$permisos->getControlToolMultipleModels($this->modelo ,["exportPdf","add",'imprimir','edit'], 'Entidadcontactos');
                $nav = $permisos->getRuta('Entidadcontactos',"edit",$user['perfil_id'], 'Contactos');
                $titulo = $permisos->getTitle($this->modelo);




                $this->set(compact('cpaises','contacto', 'entidads', 'entidadcontactos', 'cdocidtipos', 'ccontactotipos', 'cestados', 'controltools', 'nav', 'titulo'));
                $this->set('_serialize', ['contacto']);
            } else {
                $this->Flash->erroracceso(__('El contacto no existe o ha sido eliminado'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
            }
        }
    }

    public function conTipoDocumento($id) {
        $nacional="";
        switch ($id)
        {
            case 0:
                $nacional="docidtipocontactoext";
                break;
            case 1:
                $nacional="docidtipocontactonac";
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
                $label ="nombre";
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
            $this->Contactos->recursive=-1;
            if($id != 0){
                $info = $this->Contactos->find("all")
                    ->where(['Contactos.'.$tipo=>$campo, 'Contactos.id !='=>$id, 'Contactos.trash'=>0]);
            }else{
                $info = $this->Contactos->find("all")
                    ->where(['Contactos.'.$tipo=>$campo, 'Contactos.trash'=>0]);
            }
            if($info->count()>0){
                echo json_encode(["error"=>1,"msj"=>$msj.$label." del contacto ya existe."]);
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
     * @param string|null $id Contacto id.
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
                ->where(['Entidadcontactos.contacto_id' => $id])
                ->all()
                ->toArray();

            $this->loadModel('Users');
            $user = $this->Users->find()
                ->where(['Users.contacto_id' => $id])
                ->andWhere(['Users.trash' => 0])
                ->all()
                ->toArray();

            $conn = ConnectionManager::get('dbpriv');
            $esquema = $conn->config()["database"];

            if(count($user) > 0) {
                $_SESSION["ExiteUsuario"] = 1;
                return $this->redirect(['controller' => 'entidadcontactos', 'action' => 'index']);
            } elseif(count($entidadcontacto) > 1) {
                $_SESSION["ExiteEntidades"] = 1;
                return $this->redirect(['controller' => 'entidadcontactos', 'action' => 'index']);
            } else {
                $conn->execute("CALL sp_eliminar_registro('".$esquema."','contactos',".$id.")");
                $_SESSION["ContactoDele"] = 1;

                if(count($entidadcontacto) == 1) {
                    foreach ($entidadcontacto as $row) {
                        $this->Entidadcontactos->delete($row);
                    }
                }

                return $this->redirect(['controller' => 'entidadcontactos', 'action' => 'index']);
            }
        }
    }

    public function existencontactos($id = null)
    {
        $estado_id = $_POST['estado_id'];
        $id = $_POST['id'];

        if($estado_id != $this->estadoActivo && $estado_id != '') {
            $this->loadModel('Users');
            $user = $this->Users->find()
                ->where(['Users.contacto_id' => $id])
                ->all()
                ->toArray();

            if(count($user) > 0) {
                echo json_encode([
                    "error" => 1,
                    "activo" => $this->estadoActivo,
                    "msj"=>"El contacto no puede estar inactivo, tiene usuario relacionado"
                ]);
            } else {
                echo json_encode(["error" => 0, "activo" => $this->estadoActivo, "msj"=>""]);
            }
        } else {
            echo json_encode(["error" => 0, "activo" => $this->estadoActivo, "msj"=>""]);
        }

        $this->autoRender=false;
    }

    public function aprobacioncuenta($entidadId = null, $contactoId = null)
    {
        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $this->loadModel('Cpreferences');
            if ($this->Cpreferences->exists(['Cpreferences.id' => 1, 'Cpreferences.trash' => 0])) {
                $general = $this->Cpreferences->find()
                    ->select(['Cpreferences.params'])
                    ->where(['Cpreferences.id' => 1])
                    ->first()
                    ->toArray();
                $configuraciones = $general['params'];

                if ($this->Contactos->Entidadcontactos->exists(['Entidadcontactos.entidad_id' => $entidadId, 'Entidadcontactos.contacto_id' => $contactoId])) {
                    $perfildefaultregistro = (isset($configuraciones['perfildefaultregistro'])) ? $configuraciones['perfildefaultregistro'] : null;
                    $perfilesentidadcontactoid = (isset($configuraciones['perfilesentidadcontacto'])) ? $configuraciones['perfilesentidadcontacto'] : [];

                    $this->loadModel('Perfils');
                    $perfilesentidadcontacto = $this->Perfils->find('list')
                        ->where(['Perfils.trash' => 0])
                        ->andWhere(['Perfils.id IN' => $perfilesentidadcontactoid]);

                    $contacto_org = $this->Contactos->Entidadcontactos->find()
                        ->where(['Entidadcontactos.entidad_id' => $entidadId])
                        ->andWhere(['Entidadcontactos.contacto_id' => $this->Auth->user('contacto_id')])
                        ->first();

                    if($this->Auth->user('perfil_id') == $perfildefaultregistro) {
                        if (count($contacto_org) > 0) {
                            $entidadcontacto = $this->Contactos->Entidadcontactos->find()
                                ->select([
                                    'Entidads.id', 'Entidads.nombre', 'Entidads.nombrelargo','Entidads.docid','Entidads.nacional', 'Entidads.cdocidtipo_id',
                                    'Contactos.id' ,'Contactos.nombres','Contactos.apellidos', 'Contactos.email', 'Contactos.cestado_id', 'Contactos.cdocidtipo_id', 'Contactos.created', 'Contactos.docid',
                                    'Cestados.id','Cestados.nombre','Cestados.colorbkg','Cestados.colortext','Ccontactotipos.nombre','Centidadtipos.nombre','Centidadrols.nombre'
                                ])
                                ->where(['Entidadcontactos.entidad_id' => $entidadId])
                                ->andWhere(['Entidadcontactos.contacto_id' => $contactoId])
                                ->contain(['Contactos', 'Entidads', 'Contactos.Cestados', 'Contactos.Ccontactotipos', 'Entidads.Centidadtipos', 'Entidads.Centidadrols'])
                                ->first()
                                ->toArray();

                            $estadoEsperaAprobacion = $this->estadoEsperaAprobacion;

                            $json = new JsonController();
                            $estadoContactorechazado = $json->getEstadosConfGeneral('contactorechazado');

                            $docidtiposcontactos = $this->Contactos->Cdocidtipos->find('list')
                                ->where(['Cdocidtipos.trash' => 0])
                                ->andWhere(['Cdocidtipos.id IN' => $this->docidtiposcontactos])
                                ->toArray();


                            $docidtiposentidades = $this->Contactos->Cdocidtipos->find('list')
                                ->where(['Cdocidtipos.trash' => 0])
                                ->andWhere(['Cdocidtipos.id IN' => $this->docidtiposentidades])
                                ->toArray();

                            $this->set(compact('entidadcontacto', 'docidtiposcontactos', 'docidtiposentidades', 'estadoEsperaAprobacion', 'estadoContactorechazado', 'perfilesentidadcontacto', 'controltools', 'nav', 'titulo'));
                            $this->set('_serialize', ['contacto']);
                        } else {
                            $this->Flash->erroracceso(__('La solicitud de aprobación del contacto no pertenece a su organización.'));
                            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
                        }
                    } else {
                        $this->Flash->erroracceso(__($this->errorAcceso));
                        return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
                    }
                } else {
                    $this->Flash->erroracceso(__('El contacto no se encuentra en esa organización'));
                    return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
                }
            } else {
                $this->Flash->erroracceso(__('No existe el registro de configuración general. Comuníquese con el administrador.'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
            }
        }
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
            if($countpregrq>0){
                $respuestas=$this->Dataformdets->find()
                    ->where(["dataform_id"=>$dataform_id])
                    ->andwhere(["pregunta_id IN "=>$requiredpreg])
                    ->andwhere(["jsonrespuesta IS NOT"=>null])
                    ->andwhere(["trash"=>0]);
                foreach($respuestas as $respkey){
                    $flag=true;
                    $contparcial=0;
                    foreach($respkey->jsonrespuesta as $jsonkey){
                        foreach($jsonkey as $itemskey){
                            if($itemskey['items'][0]['value']==null || $itemskey['items'][0]['value']=="") $flag=false;
                            else $contparcial++;
                        }
                    }
                    $countresprq+=$contparcial;
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

        echo json_encode(["requeridas" => $requeridas, "countpregrq" => $countpregrq, "countresprq" => $countresprq]);

        $this->autoRender = false;
    }
}
