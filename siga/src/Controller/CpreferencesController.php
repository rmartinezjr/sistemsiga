<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;

/**
 * Cpreferences Controller
 *
 * @property \App\Model\Table\CpreferencesTable $Cpreferences
 *
 * @method \App\Model\Entity\Cpreference[] paginate($object = null, array $settings = [])
 */
class CpreferencesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Cpreferences";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($band = null)
    {
        if (!$this->accesoPantalla($this->modelo, 'index')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $paginacion = ($band == null) ? 20 : 1000000;
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                $busqueda = new BusquedaController();
                $query = $busqueda->busqueda($this->modelo, 'Cpreferences.nombre', 'asc', $data, $paginacion);
            } elseif (isset($_SESSION["tabla[$this->modelo]"])) {
                $session = $_SESSION["tabla[$this->modelo]"];
                $data = $session['data'];
                $busqueda = new BusquedaController();
                if (isset($this->request->getQuery()["sort"]) && count($this->request->getQuery()) > 0) {
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $busqueda->busqueda($this->modelo, 'Cpreferences.nombre', 'asc', $data, $paginacion, $field, $direction);
                } else {
                    $query = $busqueda->busqueda($this->modelo, 'Cpreferences.nombre', 'asc', $data, $paginacion);
                }
            } else {
                if (isset($this->request->getQuery()["sort"]) && count($this->request->getQuery()) > 0) {
                    $field = $this->request->getQuery()["sort"];
                    $direction = $this->request->getQuery()["direction"];
                    $query = $this->Cpreferences->find()
                        ->where(['Cpreferences.trash' => 0])
                        ->order([$field => $direction])
                        ->limit($paginacion);
                } else {
                    $query = $this->Cpreferences->find()
                        ->where([$this->modelo . '.trash' => 0])
                        ->order([$this->modelo . '.nombre' => "asc"])
                        ->limit($paginacion);
                }
            }
            $cpreferences = $this->Paginator->paginate($query);

            $user = $this->Auth->user();

            $permisos = new VerificacionPermisosController();
            $herramientas = $permisos->herramientasEdicion($this->modelo, $user['perfil_id']);
            $controltools = $permisos->getControlTool($this->modelo, ["index",'edit']);
            $nav = $permisos->getRuta($this->modelo, null, $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('cpreferences', 'herramientas', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['cpreferences']);
        }
    }

    public function vertodos()
    {
        unset($_SESSION["tabla[$this->modelo]"]);
        $this->redirect(array('controller' => $this->modelo, 'action' => "index"));
        $this->autoRender = false;
    }

    // Metodo para mostrar impresion desde el index
    public function imprimir()
    {
        $this->index(1);
        $this->viewBuilder()->setLayout("imprimir");
    }

    /**
     * View method
     *
     * @param string|null $id Cpreference id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if (!$this->accesoPantalla($this->modelo, 'view')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {

            if ($this->Cpreferences->exists(['Cpreferences.id' => $id, 'Cpreferences.trash' => 0])) {
                $cpreference = $this->Cpreferences->get($id, [
                    'contain' => []
                ]);

                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools = $permisos->getControlTool($this->modelo, ["exportPdf", 'imprimir']);
                $nav = $permisos->getRuta($this->modelo, "view", $user['perfil_id']);
                $titulo = $permisos->getTitle($this->modelo);

                $this->set(compact('cpreference', 'controltools', 'nav', 'titulo'));
                $this->set('_serialize', ['cpreference']);
            } else {
                return $this->redirect(['action' => 'index']);
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
        if (!$this->accesoPantalla($this->modelo, 'add')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $cpreference = $this->Cpreferences->newEntity();
            if ($this->request->is('post')) {

                $cpreference = $this->Cpreferences->patchEntity($cpreference, $this->request->getData());
                $cpreference->usuario = $this->Auth->user('username');
                $cpreference->created = date('Y-m-d H:i:s');
                $cpreference->trash = 0;
                $cpreference->params = json_decode($this->request->getData()['params'], true);
                $cpreference->modified = null;

                if ($this->Cpreferences->save($cpreference)) {
                    // $this->Flash->success(__('The cpreference has been saved.'));
                    $_SESSION["cpreference-save"] = 1;
                    return $this->redirect(['action' => 'view', $cpreference->id]);
                }
                //$this->Flash->error(__('The cpreference could not be saved. Please, try again.'));
            }
            $permisos = new VerificacionPermisosController();
            $user = $this->Auth->user();

            $controltools = $permisos->getControlTool($this->modelo, ["exportPdf", "add", 'imprimir','edit']);
            $nav = $permisos->getRuta($this->modelo, "add", $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact('cpreference', 'controltools', 'nav', 'titulo'));
            $this->set('_serialize', ['cpreference']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Cpreference id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if (!$this->accesoPantalla($this->modelo, 'edit')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {

            if ($this->Cpreferences->exists(['Cpreferences.id' => $id, 'Cpreferences.trash' => 0])) {
                $cpreference = $this->Cpreferences->get($id, [
                    'contain' => []
                ]);

                if ($this->request->is(['patch', 'post', 'put'])) {

                    $cpreference = $this->Cpreferences->patchEntity($cpreference, $this->request->getData());
                    $cpreference->usuariomodif = $this->Auth->user('username');
                    $cpreference->modified = date('Y-m-d H:i:s');
                    $cpreference->params = json_decode($this->request->getData()['params'], true);
                    if ($this->Cpreferences->save($cpreference)) {
                        //$this->Flash->success(__('The cpreference has been saved.'));
                        $_SESSION["cpreference-save"] = 1;
                        return $this->redirect(['action' => 'view', $cpreference->id]);
                    }
                    //$this->Flash->error(__('The cpreference could not be saved. Please, try again.'));
                }
                $permisos = new VerificacionPermisosController();
                $user = $this->Auth->user();

                $controltools = $permisos->getControlTool($this->modelo, ["exportPdf", "add", 'imprimir','edit']);
                $nav = $permisos->getRuta($this->modelo, "edit", $user['perfil_id']);
                $titulo = $permisos->getTitle($this->modelo);

                $this->set(compact('cpreference', 'controltools', 'nav', 'titulo'));
                $this->set('_serialize', ['cpreference']);
            } else {
                return $this->redirect(['action' => 'index']);
            }

        }
    }

    public function valunique()
    {
        $campo = trim($_POST['campo']);
        $id = $_POST['id'];
        $tipo = $_POST['tipo'];
        $flag = 0;
        $msj = "";
        switch ($tipo) {
            case 'nombre':
                $flag = 1;
                $msj = "El ";
                break;
            case 'codigo':
                $flag = 1;
                $msj = "El ";
                break;
            case 'etiqueta':
                $flag = 1;
                $msj = "La ";
                break;
            default:
                $flag = 0;
        }

        if ($flag == 1) {
            $this->Cpreferences->recursive = -1;
            if ($id != 0) {
                $info = $this->Cpreferences->find("all")
                    ->where(['Cpreferences.' . $tipo => $campo, 'Cpreferences.id !=' => $id, 'Cpreferences.trash' => 0]);
            } else {
                $info = $this->Cpreferences->find("all")
                    ->where(['Cpreferences.' . $tipo => $campo, 'Cpreferences.trash' => 0]);
            }
            if ($info->count() > 0) {
                echo json_encode(["error" => 1, "msj" => $msj . $tipo . " de la preferencia o configuración ya existe."]);
            } else {
                echo json_encode(["error" => 0, "msj" => ""]);
            }
        } else {
            echo json_encode(["error" => 0, "msj" => ""]);
        }
        $this->autoRender = false;
    }

    /**
     * Delete method
     *
     * @param string|null $id Cpreference id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if (!$this->accesoPantalla($this->modelo, 'delete')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
            $conn = ConnectionManager::get('dbtransac');
            $esquema = $conn->config()["database"];
            $conn->execute("CALL sp_eliminar_registro('" . $esquema . "','cpreferences'," . $id . ")");
            $_SESSION["CpreferenceDele"] = 1;

            return $this->redirect(['action' => 'index']);
        }
    }

    public function getPref()
    {
        if ($this->Cpreferences->exists(['Cpreferences.id' => $_POST['pref'], 'Cpreferences.trash' => 0])) {
            $cpreference = $this->Cpreferences->get($_POST['pref'], [
                'contain' => []
            ]);

            if (($_POST['pram']!='') &&(json_encode($cpreference['params'], true) !=trim($_POST['pram']))) {
                echo json_encode(["error" => 1]);
            }
            else{ echo json_encode(["error" => 0]);}

        } else {
            echo json_encode(["error" => 2]);
        }
        $this->autoRender = false;
    }

    public function editPref($id = null)
    {
        if ($this->Cpreferences->exists(['Cpreferences.id' => $id, 'Cpreferences.trash' => 0])) {
            $cpreference = $this->Cpreferences->get($id, [
                'contain' => []
            ]);

            if (($this->request->getData()['params']!='') &&(json_encode($cpreference['params'], true) !=$this->request->getData()['params'])) {
                $cpreference = $this->Cpreferences->patchEntity($cpreference, $this->request->getData());
                $cpreference->usuariomodif = $this->Auth->user('username');
                $cpreference->modified = date('Y-m-d H:i:s');
                $cpreference->params = json_decode($this->request->getData()['params'], true);
                if ($this->Cpreferences->save($cpreference)) {
                    //$this->Flash->success(__('The cpreference has been saved.'));
                    $_SESSION["cpreference-edit"] = 1;

                }
                //$this->Flash->error(__('The cpreference could not be saved. Please, try again.'));
            }

            return $this->redirect(['action' => 'index']);
        } else {
            return $this->redirect(['action' => 'index']);
        }
        $this->autoRender = false;
    }
}
