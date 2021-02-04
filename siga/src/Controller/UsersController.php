<?php
namespace App\Controller;

use App\Files\File;
use App\Notifications\NotificacionesEmail;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[] paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Paginator');


    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        $this->Auth->allow(['logout', 'sendemail', 'contrasenia']);
        $this->modelo = "Users";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";

    }

    public function login()
    {
        if ($this->request->is('post')) {
            // Verifica la informacion ingresada por el usuario para autentificarse
            $user = $this->Auth->identify();


            if($user) {

                // Verifica si el usuario logueado no ha sido eliminado
                if(!$user['trash']) {

                    // Verifica si el usuario logueado se encuentra activo
                    if($user['cestado_id'] == 1) {

                        // Verifica si el perfil del usuario logueado no se ha eliminado
                        $perfil = $this->Users->Perfils->get($user['perfil_id']);
                        if(!$perfil['trash']) {

                            $ingreso = "Correcto";
                            $this->lastLogin($user['id']);

                            $this->Auth->setUser($user);
                            $perfil=$this->Auth->user()["perfil_id"];
                            $_SESSION['menus']=parent::menu($perfil);

                            $files = new File();
                            // $files->downloadIcons();
                            $files->extraZip();
                            return $this->redirect($this->Auth->redirectUrl());
                        } else {
                            $ingreso = "Incorrecto. Perfil eliminado";
                            // $this->registroLogs($user, $ingreso);
                            $this->Flash->errorlogin(__('El perfil del usuario ha sido eliminado', ['key' => 'auth' ]));
                        }
                    } else {
                        $ingreso = "Incorrecto. Usuario inactivo";
                        // $this->registroLogs($user, $ingreso);
                        $this->Flash->errorlogin(__('El usuario está inactivo', ['key' => 'auth' ]));
                    }
                } else {
                    $ingreso = "Incorrecto. Usuario eliminado";
                    // $this->registroLogs($user, $ingreso);
                    $this->Flash->errorlogin(__('El usuario ha sido eliminado', ['key' => 'auth' ]));
                }
            } else {
                $ingreso = "Incorrecto. Datos incorrectos";
                // $this->registroLogs($user, $ingreso);
                $this->Flash->errorlogin(__('Usuario y/o contraseña incorrecto. Intente nuevamente', ['key' => 'auth' ]));
            }
        }

        $this->viewBuilder()->setLayout( 'inicioSesion');
        $title = 'Iniciar Sesión';
        $this->set(compact('title'));
    }

    public function home()
    {

    }

    public function logout()
    {
        unset($_SESSION['menus']);
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($band=null)
    {
        $json = new JsonController();
        $this->estadosId = $json->getEstadosConfGeneral();
        $this->estadoActivo = $json->getEstadosConfGeneral('activo');
        $this->idselect_validos = $this->estadosId;

        if(!$this->accesoPantalla($this->modelo, 'index')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $dirdefault='asc';
            $paginacion = ($band==null)?20:1000000;
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data= $this->request->getData();
                $busqueda = new BusquedaController();
                $query = $busqueda->busqueda($this->modelo, 'Users.username', 'asc', $data,$paginacion);
                $query->contain(['Cestados','Perfils','Contactos']);

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

                    $query = $busqueda->busqueda($this->modelo, 'Users.username', 'asc', $data,$paginacion, $field, $direction);
                }else{
                    $query = $busqueda->busqueda($this->modelo, 'Users.username', 'asc', $data,$paginacion);
                }
                $query->contain(['Cestados' ,'Perfils','Contactos']);

            } else {
                if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                    $field = $this->request->getQuery()["sort"];

                    $direction = $this->request->getQuery()["direction"];
                    $flagdirection =parent::changeSort($field,$direction);
                    if(!empty($flagdirection))
                        $dirdefault=$flagdirection;
                    $query = $this->Users->find()
                        ->where(['Users.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order([$field=>$direction])
                        ->limit($paginacion);
                    $query->contain(['Cestados','Perfils','Contactos']);
                }else {

                    $query = $this->Users->find( )
                        ->where(['Users.trash' => 0])
                        ->andWhere(['Cestados.id' => 1])
                        ->order(['Users.username'=>"asc"])
                        ->limit($paginacion);
                    $query->contain(['Cestados','Perfils','Contactos']);
                }
            }

            $users= $this->Paginator->paginate($query);

            $cestados = $this->Users->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);


            $perfils = $this->Users->Perfils->find('list')
                ->where(['Perfils.trash' => 0]);

            $contactos= $this->Users->Contactos->find('list')
                ->select(['Contactos.id'])
                ->Where(['Contactos.trash ' => 0]);

            $cont = $contactos->func()->concat(['Contactos.nombres'=>'identifier',' ','Contactos.apellidos'=>'identifier']);
            $contactos->select(['nombres' => $cont]);

            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools=$permisos->getControlTool($this->modelo, ["index","edit"]);
            $nav = $permisos->getRuta($this->modelo, null, $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);



            $this->set(compact('users','herramientas','controltools','nav','titulo','cestados','perfils','contactos','dirdefault'));
            $this->set('_serialize', ['users']);
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
    public function reestablecer(){
        $this->index(1);
        $this->viewBuilder()->setLayout("imprimir");
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $json = new JsonController();
        $this->estadosId = $json->getEstadosConfGeneral();
        $this->estadoActivo = $json->getEstadosConfGeneral('activo');
        $this->idselect_validos = $this->estadosId;

        if(!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $valid= new ValidacionesController();
            if(!$valid->AccessRegisterView($this->modelo, $id, 'index',0)){
                return $this->redirect(['action'=>'index']);
            }

            $user = $this->Users->get($id,[
                'contain'=>['Cestados' => [
                    'strategy' => 'select', 'queryBuilder' => function ($q) {
                        return $q->order(['Cestados.id' =>'ASC'])->limit(1);}],'Perfils','Contactos']
            ]);


            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf",'imprimir']);

            $cestados = $this->Users->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);


            $userAut = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "view", $userAut['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $this->set('user', $user);

            $this->set('_serialize', ['user']);
            $this->set(compact("controltools","nav",'titulo','cestados'));
        }


    }

    /* public function valunique(){
         $nombre = trim($_POST['campo']);
         $id = $_POST['id'];
         $this->Users->recursive = -1;
         if($id != 0){
             $info = $this->Users->find("all")
                 ->where(['Users.email'=>$nombre, 'Users.id !='=>$id, 'Users.trash'=>0]);
         }else{
             $info = $this->Users->find("all")
                 ->where(['Users.email'=>$nombre, 'Users.trash'=>0]);
         }
         if($info->count()>0){
             echo json_encode(["error"=>1,"msj"=>"El correo electrónico ya existe."]);
         }else{
             echo json_encode(["error"=>0,"msj"=>""]);
         }



         $this->autoRender=false;
     }*/
    public function valunique(){
        $campo = trim($_POST['campo']);
        $id = $_POST['id'];
        $tipo= $_POST['tipo'];
        $nombreCampo="";
        $flag=0;
        $msj="";
        switch($tipo){
            case 'nombre':
                $flag=1;
                $nombreCampo=$tipo;
                $msj="El ";
                break;
            case 'email':
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
            $this->Users->recursive=-1;
            if($id != 0){
                $info = $this->Users->find("all")
                    ->where(['Users.'.$tipo=>$campo, 'Users.id !='=>$id, 'Users.trash'=>0]);
            }else{
                $info = $this->Users->find("all")
                    ->where(['Users.'.$tipo=>$campo, 'Users.trash'=>0]);
            }
            if($info->count()>0){
                if($nombreCampo == 'email') {
                    echo json_encode(["error"=>1,"msj"=>"El correo electrónico del usuario  ya existe."]);
                } else {
                    echo json_encode(["error"=>1,"msj"=>$msj.$nombreCampo." de usuario  ya existe."]);
                }
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

        $json = new JsonController();
        $this->estadosId = $json->getEstadosConfGeneral();
        $this->estadoActivo = $json->getEstadosConfGeneral('activo');
        $this->idselect_validos = $this->estadosId;

        if(!$this->accesoPantalla($this->modelo, 'add')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $user = $this->Users->newEntity();
            if ($this->request->is('post')) {
                $user = $this->Users->patchEntity($user, $this->request->getData());
                $user->usuario = "admin";
                $user->created = date("Y-m-d H:i:s");
                if ($this->Users->save($user)) {
                    $_SESSION["users-save"]=1;
                    return $this->redirect(['action' => 'view',$user->id]);
                }else{
                    $_SESSION["users-save"]=1;
                    return $this->redirect(['action' => 'add']);
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add",'imprimir',"edit"]);
            $users = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "add",$users['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            $perfils = $this->Users->Perfils->find('list', ['limit' => 200])
                ->where(['Perfils.trash' => 0]);

            $cestados = $this->Users->Cestados->find('list', ['limit' => 200])
                ->Where(['Cestados.trash ' => 0])
                ->andWhere(['Cestados.id IN' => $this->estadosId]);

            $contactos= $this->Users->Contactos->find('list')
                ->select(['Contactos.id'])
                ->Where(['Contactos.trash ' => 0])
                ->AndWhere(['Contactos.cestado_id' =>$this->estadoActivo]);


            $cont = $contactos->func()->concat(['Contactos.nombres'=>'identifier',' ','Contactos.apellidos'=>'identifier']);
            $contactos->select(['nombres' => $cont]);


            $this->set(compact('user','controltools','nav','titulo','cestados','perfils','contactos'));
            $this->set('_serialize', ['user']);
        }

    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $json = new JsonController();
        $this->estadosId = $json->getEstadosConfGeneral();
        $this->estadoActivo = $json->getEstadosConfGeneral('activo');
        $this->idselect_validos = $this->estadosId;

        if(!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {


            $valid= new ValidacionesController();
            if(!$valid->AccessRegisterView($this->modelo, $id, 'index',0)){
                return $this->redirect(['action'=>'index']);
            }
            $user = $this->Users->get($id, [
                'contain'=>['Cestados' => [
                    'strategy' => 'select', 'queryBuilder' => function ($q) {
                        return $q->order(['Cestados.id' =>'ASC'])->limit(1);}],'Perfils','Contactos']
            ]);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $id_select=$this->request->getData('cestado_id');
                $valid= new ValidacionesController();


                if($valid->RegistrosIdValidos($id_select,$this->estadosId,'ccontactotipo-save',0)){
                    $user = $this->Users->patchEntity($user, $this->request->getData());
                    $user->modified = date("Y-m-d H:i:s");
                    $user->usuariomodif = "admin";

                    if ($this->Users->save($user)) {
                        $_SESSION["users-save"]=1;
                        return $this->redirect(['action' => 'view',$id]);
                    }else{
                        $_SESSION["users-save"]=0;
                        return $this->redirect(['action' => 'view',$id]);
                    }
                }
            }
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool($this->modelo, ["exportPdf","exportarExcel","add","edit",'imprimir']);
            $users = $this->Auth->user();
            $nav = $permisos->getRuta($this->modelo, "edit",$users['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);
            /*Elementos para select*/
            $cestados = $this->Users->Cestados->find('list')
                ->where(['Cestados.trash' => 0])
                ->andwhere(['Cestados.id IN'=>[1,2]]);

            $perfils = $this->Users->Perfils->find('list', ['limit' => 200])
                ->where(['Perfils.trash' => 0]);

            $contactos= $this->Users->Contactos->find('list')
                ->select(['Contactos.id'])
                ->Where(['Contactos.trash ' => 0])
                ->AndWhere(['Contactos.cestado_id' =>$this->estadoActivo]);

            $cont = $contactos->func()->concat(['Contactos.nombres'=>'identifier',' ','Contactos.apellidos'=>'identifier']);
            $contactos->select(['nombres' => $cont]);



            $this->set(compact('user','controltools','nav',"titulo", 'cestados','perfils','contactos'));
            $this->set('_serialize', ['user']);
        }

    }

    // Funcion para reestablecer la contraseÃ±a desde el listado de usuarios
    use MailerAwareTrait;
    public function reset($id = null){
        $id = trim($id);
        $msj = "";
        if($this->Users->exists(['id' => $id])) {
            $user = $this->Users->get($id);
            //Si el usuario se encuentra activo

            // Definicion de variables

            $random = md5(rand(100,1000));
            $para = $user->email;
            $url = Router::url(['controller' => 'users', 'action' => 'contrasenia', $id, $random], true);

            // Actualizando la informacion del usuario
            $user->numrandom = $random;
            $user->lastreset = date('Y-m-d H:i:s');
            $this->Users->save($user);



            $this->loadModel('CorreoPlantillas');
            if($this->CorreoPlantillas->exists(['CorreoPlantillas.nombre' => 'Plantilla para restablecer password','CorreoPlantillas.trash' => 0])) {
                $correoPlantilla = $this->CorreoPlantillas->find()
                    ->where(['CorreoPlantillas.nombre' => 'Plantilla para restablecer password'])
                    ->andWhere(['CorreoPlantillas.trash' => 0])
                    ->first();
                $html = $correoPlantilla->contenido;
                $this->loadModel('Contactos');
                $info = $this->Contactos->find("all")
                    ->where(['Contactos.id'=>$user->contacto_id])
                    ->first();
                $full_name = (isset($info->nombres) && $info->nombres!='')?$info->nombres." ".$info->apellidos:"";
                $subject = 'Restablecer contraseÃ±a';
                $variables = [
                    'nombrecompleto' => $full_name,
                    'institucion' => 'Tec101', // Cambiarlo
                    'org' => 'Tecnologias 101',
                    'url' => $url,
                    'user' =>$user->username
                ];


                $notificacion = new NotificacionesEmail();

                $result = $notificacion->sendEmail('soporte@tecnologias101.com',$para, $subject, $html, $variables);

                if($result) {
                    $this->Flash->success(__('Se ha envíado un correo a: ' . $para));


                    $this->redirect(array('controller'=>"users",'action'=> "index"));
                } else {
                    $this->Flash->error(__('No se pudo enviar el correo electrónico. Intente nuevamente.'));
                    $this->redirect(array('controller'=>"users",'action'=> "index"));
                }
            } else {
                $this->Flash->error(__('No se ha encontrado la plantilla del correo electrónico.'));
                $this->redirect(array('controller'=>"users",'action'=> "index"));
            }


        } else {
            // El usuario no existe
            $this->Flash->error(__('El usuario no existe.'));
            $this->redirect(array('controller'=>"users",'action'=> "index"));
        }

        echo $msj;
        $this->autoRender=false;
    }


    // Funcion para reestablecer la contraseña desde el inicio de sesion
    public function sendemail() {
        // Entidad que tiene la informacion de quien envia correo
        //$this->loadModel('Organizacions'); // Cambiarlo
        if ($this->request->is('post')) {
            $usuario = $this->request->getData()['usuario'];
            $conditions = [
                ['Users.username' => $usuario],
                ['Users.email' => $usuario]
            ];

            if ($this->Users->exists(['OR' => $conditions])) {
                $user = $this->Users->find()->where(['OR' => $conditions])
                    ->contain(['Contactos'])
                    ->first();

                if (!$user->trash) {
                    if ($user->cestado_id == 1) {
                        if ($user->email) {
                            //if($this->Organizacions->exists(['id' => 1])) {
                            // Definicion de variables
                            //$organizacion = $this->Organizacions->find()->first();

                            $random = md5(rand(100,1000));
                            $para = $user->email;
                            $url = Router::url(['controller' => 'users', 'action' => 'contrasenia', $user->id, $random], true);

                            // Actualizando la informacion del usuario
                            $user->numrandom = $random;
                            $user->lastreset = date('Y-m-d H:i:s');
                            $this->Users->save($user);

                            $this->loadModel('CorreoPlantillas');
                            if($this->CorreoPlantillas->exists(['CorreoPlantillas.nombre' => 'Plantilla para restablecer password','CorreoPlantillas.trash' => 0])) {
                                $correoPlantilla = $this->CorreoPlantillas->find()
                                    ->where(['CorreoPlantillas.nombre' => 'Plantilla para restablecer password'])
                                    ->andWhere(['CorreoPlantillas.trash' => 0])
                                    ->first();
                                $html = $correoPlantilla->contenido;
                                $subject = 'Restablecer contraseña';
                                $variables = [
                                    'nombrecompleto' => $user->contacto->nombres . ' ' . $user->contacto->apellidos,
                                    'institucion' => 'ANSP', // Cambiarlo
                                    //'org' => $organizacion->organizacion,
                                    'org' => 'Academia Nacional de Seguridad publica', // Cambiarlo
                                    'url' => $url,
                                    'user'=>$user->username
                                ];

                                // $emailnotification = $organizacion->emailnotification;
                                $emailnotification = 'renemartinez508@gmail.com';

                                $notificacion = new NotificacionesEmail();
                                $result = $notificacion->sendEmail($emailnotification, $para, $subject, $html, $variables);

                                if ($result) {
                                    $this->Flash->successlogin(__('Se ha envíado un correo a: ' . $para));
                                    $this->redirect(array('controller' => "users", 'action' => "login"));
                                } else {
                                    $this->Flash->errorlogin(__('No se pudo enviar el correo electrónico. Intente nuevamente.'));
                                    $this->redirect(array('controller' => "users", 'action' => "login"));
                                }
                            } else {
                                $this->Flash->errorlogin(__('No se ha encontrado la plantilla del correo electrónico.'));
                                $this->redirect(array('controller'=>"users",'action'=> "login"));
                            }
                            /*} else {
                                $this->Flash->errorlogin(__('No existe una organización.'));
                                $this->redirect(array('controller'=>"users",'action'=> "login"));
                            }*/
                        } else {
                            $this->Flash->errorlogin(__('El correo electrónico no existe, consulte con el administrador.'));
                            $this->redirect(['action' => 'login']);
                        }
                    } else {
                        $this->Flash->errorlogin(__('Usuario inactivo, consulte con el administrador.'));
                        $this->redirect(['action' => 'login']);
                    }
                } else {
                    $this->Flash->errorlogin(__('El usuario ha sido eliminado, consulte con el administrador.'));
                    $this->redirect(['action' => 'login']);
                }
            } else {
                $this->Flash->errorlogin(__('El usuario no existe, consulte con el administrador.'));
                $this->redirect(['action' => 'login']);
            }
        }

        $this->viewBuilder()->setLayout( 'inicioSesion');

        $title = 'Recuperar Contraseña';
        $this->set(compact('title'));
    }

    public function contrasenia($id = null, $hash = null)
    {
        // Entidad que tiene la informacion de quien envia correo
        //$this->loadModel('Organizacions'); //Cambiarlo
        $this->viewBuilder()->setLayout( 'inicioSesion');
        if($this->Users->exists(['id' => $id])) {
            $user = $this->Users->get($id, [
                'contain' => ['Contactos']
            ]);

            $resetDate = strtotime($user->lastreset);
            $now = time();
            $days = ($now - $resetDate) / 86400;
            if ($hash == $user->numrandom) {
                if ($days < 1) {
                    if ($this->request->is('post')) {
                        // Actualizando la informacion del usuario
                        $data = $this->request->getData();
                        $user->password = $data['password'];
                        if ($this->Users->save($user)) {
                            // if($this->Organizacions->exists(['id' => 1])) {
                            // Definicion de variables
                            // $organizacion = $this->Organizacions->find()->first();
                            $para = $user->email;

                            $this->loadModel('CorreoPlantillas');
                            if($this->CorreoPlantillas->exists(['CorreoPlantillas.nombre' => 'Plantilla para password restablecido','CorreoPlantillas.trash' => 0])) {
                                $correoPlantilla = $this->CorreoPlantillas->find()
                                    ->where(['CorreoPlantillas.nombre' => 'Plantilla para password restablecido'])
                                    ->andWhere(['CorreoPlantillas.trash' => 0])
                                    ->first();

                                $html = $correoPlantilla->contenido;
                                $subject = 'Contraseña restablecida';
                                $variables = [
                                    'nombrecompleto' => $user->contacto->nombres . ' ' . $user->contacto->apellidos,
                                    'institucion' => 'ANSP', // Cambiarlo
                                    //'org' => $organizacion->organizacion
                                    'org' => 'ANSP'
                                ];

                                // $emailnotification = $organizacion->emailnotification;
                                $emailnotification = 'renemartinez508@gmail.com';

                                $notificacion = new NotificacionesEmail();
                                $result = $notificacion->sendEmail($emailnotification, $para, $subject, $html, $variables);

                                if ($result) {
                                    // $this->Flash->successlogin(__('Contraseña restablecida correctamente'));
                                    $this->Flash->success(__('Contraseña restablecida correctamente'));
                                    $this->redirect(array('controller' => "users", 'action' => "login"));
                                } else {
                                    $this->Flash->errorlogin(__('No se pudo enviar el correo electrónico. Intente nuevamente.'));
                                    $this->redirect(array('controller' => "users", 'action' => "login"));
                                }
                            } else {
                                $this->Flash->errorlogin(__('No se ha encontrado la plantilla del correo electrónico.'));
                                $this->redirect(array('controller'=>"users",'action'=> "login"));
                            }
                            /*} else {
                                $this->Flash->errorlogin(__('No existe una organización.'));
                                $this->redirect(array('controller'=>"users",'action'=> "login"));
                            }*/

                            $this->redirect(['action' => 'login']);
                        } else {
                            $this->Flash->errorlogin(__('La contraseña no pudo ser restablecida. Intente nuevamente.'));
                            $this->redirect(array('controller'=>"users",'action'=> "login"));
                        }
                    } else {
                        $this->set(compact('id', 'hash'));
                    }
                } else {
                    $this->Flash->errorlogin(__('El enlace para restablecer contraseña ha expirado.'));
                    $this->redirect(array('controller'=>"users",'action'=> "login"));
                }
            } else {
                $this->Flash->errorlogin(__('Operación no permitida, enlace invalido.'));
                $this->redirect(array('controller'=>"users",'action'=> "login"));
            }
        } else {
            $this->Flash->errorlogin(__('El usuario no existe, consulte con el administrador.'));
            $this->redirect(['action' => 'login']);
        }

        $title = 'Restablecer Contraseña';
        $this->set(compact('title'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
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
            parent::callprocedureDelete('dbpriv',$this->modelo,$id,'UsersDele');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function lastLogin($userId)
    {
        $users = TableRegistry::get('Users');
        $user = $this->Users->get($userId);
        $user->lastlogin = date('Y-m-d H:i:s');
        $users->save($user);
    }
    /****
     * Author: Manuel Anzora
     * date: 30-05-2019
     * description: metodo para verificar que un contacto no sea asignado a más de un usuario
     *****/
    public function validContact(){
        $this->autoRender=false;
        $contacto   = $_POST["contacto"];
        $id         = $_POST["id"];
        $datos      = [];
        if($id>0){
            //si el id es mayor a cero es un edit
            $datos = $this->Users->find()
                ->where(["contacto_id"=>$contacto])
                ->andwhere(["id !="=>$id])
                ->andWhere(["trash"=>0])
                ->first();
        }else{
            $datos = $this->Users->find()
                ->where(["contacto_id"=>$contacto])
                ->andWhere(["trash"=>0])
                ->first();
        }
        $band=0;
        if(count($datos)>0)
            $band=1;

        echo $band;
    }
}
