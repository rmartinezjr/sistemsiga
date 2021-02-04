<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;

/**
 * Entidadcontactos Controller
 *
 * @property \App\Model\Table\EntidadcontactosTable $Entidadcontactos
 *
 * @method \App\Model\Entity\Entidadcontacto[] paginate($object = null, array $settings = [])
 */
class EntidadcontactosController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Entidadcontactos";
        $this->errorAcceso = "No tiene acceso a esa pantalla. Comuníquese con el administrador.";

        $json = new JsonController();
        $this->estadoEsperaVerificacion = $json->getEstadosConfGeneral('esperaverificacion');

        $this->Auth->allow(['getTipoDocumento', 'verifydocid', 'getentidadcontacto', 'isUnique','enttipodocument','TipoDocumentolcount','getCountDocument']);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        if(!$this->accesoPantalla('Entidads', 'index')) {
            $this->Flash->erroracceso(__($this->errorAcceso));
            return $this->redirect(['controller' => 'Pages', 'action' => 'index']);
        } else {
           $user = $this->Auth->user();
            $permisos = new VerificacionPermisosController();
            $controltools=$permisos->getControlTool('Entidads', ["index",'edit']);
            $nav = $permisos->getRuta('Entidadcontactos',null, $user['perfil_id']);
            $titulo = $permisos->getTitle($this->modelo);

            $this->set(compact( 'controltools', 'nav','titulo'));
        }
    }

    // Metodo para mostrar impresion desde el index
    public function imprimir(){
        $this->index(1);
        $this->viewBuilder()->setLayout("imprimir");
    }

    //
    public function getTipoDocumento() {
        $id = $_POST['id'];

        $this->loadModel('Cdocidtipos');
        $this->Cdocidtipos->recursive = -1;
        $cdocidtipo = $this->Cdocidtipos->find()
            ->select(['Cdocidtipos.mascara'])
            ->where(['Cdocidtipos.id' => $id])
            ->first();

        if(count($cdocidtipo) > 0){

            echo json_encode(["error" => 0, "msj" => "", "data" => $cdocidtipo]);
        } else {
            echo json_encode(["error" => 1, "msj" => "Tipo de documento seleccionado no existe"]);
        }

        $this->autoRender = false;
    }
    public function TipoDocumentolcount() {
        $nacional="";
        $id = $_POST['id'];
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

    public function realizarBusqueda($parametros, $modelo)
    {
        $busquedaText = [];
        $busquedaSelect = [];
        $session = [];

        $this->loadModel($modelo);
        $query = $this->$modelo->find()->where([]);

        // Se obtienen los campos para busqueda de lo ingresado en campo de texto
        $campos = explode(',', $parametros['parametros']);

        // Se realiza filtro segun lo ingresado en campo de texto
        $session['search_text'] = $parametros['search_text'];

        foreach ($campos as $campo) {
            if ($campo == "id") {
                $id = (string)$parametros['search_text'];
                array_push($busquedaText, [$modelo . '.' . $campo => $id]);
            } else {
                array_push($busquedaText, [$modelo . '.' . $campo . ' LIKE' => '%' . (string)$parametros['search_text'] . '%']);
            }
        }
        $query->andWhere(['OR' => $busquedaText]);

        // Se realiza filtro segun lo seleccionado en el campo estado
        if ($parametros['estado'] != '') {
            array_push($busquedaSelect, [$modelo . '.cestado_id' => $parametros['estado']]);
        } else {
            array_push($busquedaSelect, [$modelo . ".cestado_id" => 1]);
        }
        $query->andWhere(['AND' => $busquedaSelect]);

        $session['data'] = $parametros;
        $_SESSION['tabla[' . $modelo . ']'] = $session;

        return $query;
    }

    public function verifydocid() {
        $modelo = $_POST['modelo'];
        $docid = $_POST['docid'];

        $this->loadModel($modelo);
        $this->$modelo->recursive = -1;
        $entidads = $this->$modelo->find()
            ->where([$modelo . '.docid' => $docid])
            ->first();

        if(count($entidads) > 0) {
            echo json_encode(["error" => 1, "msj" => "El número de documento ingresado ya existe"]);
        } else {
            echo json_encode(["error" => 0, "msj" => ""]);
        }

        $this->autoRender = false;
    }

    public function getentidadcontacto() {
        $modelo = $_POST['modelo'];
        $docid = $_POST['docid'];

        if($modelo == 'Entidads') {
            $docid = str_replace("-", "",  $docid);
        }

        $this->loadModel($modelo);
        $this->$modelo->recursive = -1;
        $info = $this->$modelo->find()
            ->where([$modelo . '.docid' => $docid])
            ->first();

        if(count($info) > 0) {
            echo json_encode(["data" => 1, "registro" => $info, "verificacion" => $this->estadoEsperaVerificacion]);
        } else {
            echo json_encode(["data" => 0]);
        }

        $this->autoRender = false;
    }

    public function isUnique() {
        $modelo = $_POST['modelo'];
        $valor = $_POST['valor'];
        $campo = $_POST['campo'];
        $label = $_POST['label'];

        $valor = str_replace("-", "",  $valor);

        $this->loadModel($modelo);
        $this->$modelo->recursive = -1;
        $info = $this->$modelo->find()
            ->where([$modelo . '.' . $campo => $valor])
            ->andWhere([$modelo . '.trash' => 0])
            ->first();

        if(count($info) > 0) {
            echo json_encode(["error" => 1, "msj" => "El valor ingresado en el campo " . $label . " ya existe."]);
        } else {
            if($modelo == 'Contactos') {
                $modelo = 'Users';
                $this->loadModel($modelo);
                $this->$modelo->recursive = -1;
                $info_us = $this->$modelo->find()
                    ->where([$modelo . '.' . $campo => $valor])
                    ->andWhere([$modelo . '.trash' => 0])
                    ->first();

                if(count($info_us) > 0) {
                    echo json_encode(["error" => 1, "msj" => "El valor ingresado en el campo " . $label . " ya existe."]);
                } else {
                    echo json_encode(["error" => 0]);
                }
            } else {
                echo json_encode(["error" => 0]);
            }
        }

        $this->autoRender = false;
    }
    public function getCountDocument() {


        $nacional="";
        $id = $_POST['id'];

        if($_POST['mod']=='Contactos')
        {
            switch ($id)
            {
                case 0:
                    $nacional="docidtipocontactoext";
                    break;
                case 1:
                    $nacional="docidtipocontactonac";
                    break;
            }
        }
        else if($_POST['mod']=='Entidads')
        {
            switch ($id)
            {
                case 0:
                    $nacional="docidentidadext";
                    break;
                case 1:
                    $nacional="docidentidadnac";
                    break;
            }
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

        $cdocidtipo2=strlen(str_replace("-", "", $cdocidtipo->mascara));
        if(count($cdocidtipo2) > 0){

            echo json_encode(["error" => 0, "msj" => "", "data" => $cdocidtipo2]);
        } else {
            echo json_encode(["error" => 1, "msj" => "Tipo de documento seleccionado no existe"]);
        }

        $this->autoRender = false;
    }
    // Validar documento contacto
    public function enttipodocument() {
        $nacional="";
        $id = $_POST['id'];
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
}
