<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Routing\Router;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use App\Notifications\NotificacionesEmail;
use App\Model\Entity\Contacto;
use App\Model\Entity\Entidad;
use App\Model\Entity\Entidadcontacto;
use Cake\Datasource\ConnectionManager;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{

    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Network\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display(...$path)
    {
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            if($path[0] == 'solicitud-registro') {
                $this->viewBuilder()->setLayout( 'inicioSesion' );
                $title = 'Solicitar Registro';
                $this->set(compact('title'));
            } else if($path[0] == 'home') {

                $this->viewBuilder()->setLayout( false );
            } else if($path[0] == 'registro-organizaciones') {
                $this->registroorganizaciones();
            } else if($path[0] == 'verificacion-cuenta') {
                $this->verificacioncuenta();
            }

            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }

        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    public function index()
    {
            $this->viewBuilder()->setLayout('home');
    }

    public function img($id=null)
    {
        header("Content-type: image/png");
        $data=parent::imgs($id);
        echo $data;
        $this->autoRender=false;
    }

    use MailerAwareTrait;
    // Funcion que sirve para el registro de organizaciones y sus contactos
    public function registroorganizaciones()
    {
        $this->viewBuilder()->setLayout('registro');
        $title = 'Registro de Organizaciones';

        $cestados = new CestadosController();
        $estadoActivo = $cestados->getEstados(true);
        $estadoAprobacion = $this->getEstadoAprobacion();

        $json = new JsonController();
        $estadoEsperaVerificacion = $json->getEstadosConfGeneral('esperaverificacion');
        $estadoContactoRechazado = $json->getEstadosConfGeneral('contactorechazado');

        $docid_org = '';
        $docid_persona = '';
        $info = [];

        // Verifica si no se accede a travez de la url
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Si se recibe la informacion desde la pantalla de 'Solicitud de Registro'
            if(isset($data['docorganizacion']) || isset($data['docindividual'])) {
                $docid_org = (isset($data['docorganizacion'])) ? trim($data['docorganizacion']) : '';
                $docid_persona = (isset($data['docindividual'])) ? trim($data['docindividual']) : '';
                $tipo_registro = ($docid_org != '') ? 1 : 2;
                $this->loadModel('Entidadcontactos');

                // Si no se ha ingresado ningun documento de identidad
                if($docid_org == '' && $docid_persona == '') {
                    $_SESSION["solicitud-errorempty"]=1;
                    $this->redirect(array('controller' => "pages", 'action' => "solicitud_registro"));

                // Si el documento de identidad ingresado fue el de la organizacion
                } elseif($docid_org != '') {
                    $info = $this->Entidadcontactos->Entidads->find()
                        ->where(['Entidads.docid' => $docid_org, 'Entidads.trash'=>0])
                        ->first();

                // Si el documento de identidad ingresado fue el de la persona contacto
                } elseif($docid_persona != '') {
                    $info = $this->Entidadcontactos->Contactos->find()
                        ->where(['Contactos.docid' => $docid_persona, 'Contactos.trash'=>0])
                        ->first();
                }

            // Si se recibe la informacion desde la pantalla de 'Registro de organizacion'
            } elseif(isset($data['Entidads']) && isset($data['Contactos'])) {
                $tipo_registro = $data['tipo_registro'];

                if($tipo_registro == '1') {
                    $docid_org = str_replace("-", "",  $data['Entidads']['docid']);
                } elseif($tipo_registro == '2') {
                    $docid_persona = $data['Contactos']['docid'];
                }

                // Verifica la informacion ingresada de la organizacion
                if($data['Entidads']['existe'] != '') {
                    $info_valida_entidades = $this->isValida('Entidads', $data['Entidads'], []);
                } else {
                    $info_valida_entidades = $this->isValida('Entidads', $data['Entidads'], ['nombre']);
                }

                // Verifica la informacion ingresada de la persona contacto
                if($data['Contactos']['existe'] != '') {
                    $info_valida_contactos = $this->isValida('Contactos', $data['Contactos'], []);
                } else {
                    $info_valida_contactos = $this->isValida('Contactos', $data['Contactos'], ['email']);
                }

                
                // Si la informacion ingresada de la organizacion no es correcta
                if(!$info_valida_entidades['isValida']) {
                    $this->Flash->errorlogin(__('El valor del campo ' . $info_valida_entidades['campo'] . ' ya existe'));

                // Si la informacion ingresada de la persona contacto no es correcta
                } elseif(!$info_valida_contactos['isValida']) {
                    $this->Flash->errorlogin(__('El valor del campo ' . $info_valida_contactos['campo'] . 'ya existe'));

                // Si la informacion ingresada es correcta
                } else {
                    $this->loadModel('Entidads');
                    $entidad = $this->Entidads->find()
                        ->where(['Entidads.docid' => str_replace("-", "",  $data['Entidads']['docid']), 'Entidads.trash'=>0])
                        ->first();

                    $existeEntidad = false;
                    $tipodoc=$this->entTipoDocumento($data['Entidads']['nacional']);

                    // Si la organizacion ingresada no existe
                    if(count($entidad) < 1) {
                        $org = TableRegistry::get('Entidads');


                        $entidad = new Entidad();
                        $entidad->cestado_id = $estadoEsperaVerificacion;
                        $entidad->nombre = $data['Entidads']['nombre'];
                        $entidad->nombrelargo = $data['Entidads']['nombrelargo'];
                        $entidad->nacional =  $data['Entidads']['nacional'];
                        $entidad->cdocidtipo_id = $tipodoc;
                        $entidad->docid =str_replace("-", "",  $data['Entidads']['docid']);
                        $entidad->centidadtipo_id = $data['Entidads']['centidadtipo_id'];
                        $entidad->centidadrol_id = $data['Entidads']['centidadrol_id'];
                        $entidad->usuario = 'Registro plataforma';
                        $entidad->created = date('Y-m-d H:i:s');
                        $entidad->trash = 0;
                        $entidad->modified = null;

                        if(!$org->save($entidad)) {
                            $entidad = [];
                        }
                    // Si la organizacion ingresada existe
                    } else {
                        $existeEntidad = true;
                    }

                    // Verifica que haya una entidad u organizacion
                    if(count($entidad) > 0) {
                        $this->loadModel('Entidadcontactos');


                       // Nombre de la organizacion a donde se envia la solicitud de aprobacion de la cuenta
                        $nombre_entidad = '';

                        // Correo electronico a donde se envia la solicitud de aprobacion de la cuenta
                        $email_organizacion = '';

                        // Nombre de la persona a quien se le envia la solicitud de aprobacion de la cuenta
                        $personaentidad = '';

                        // Verifica si la organizacion tiene usuario creado
                        $org_vinculada = 0;
                        if($this->Entidadcontactos->exists(['Entidadcontactos.entidad_id' => $entidad->id])) {
                            $entidadcontacto = $this->Entidadcontactos->find()
                                ->select(['Entidads.nombre', 'Contactos.id' ,'Contactos.nombres','Contactos.apellidos', 'Contactos.email', 'Contactos.cestado_id'
                                ])
                                ->where(['Entidadcontactos.entidad_id' => $entidad->id])
                                ->contain(['Contactos', 'Entidads', 'Contactos.Users'])
                                ->order(['Entidadcontactos.id'])
                                ->first()
                                ->toArray();

                            $this->loadModel('Users');
                            $usercontacto = $this->Users->find()
                                ->select(['Contactos.id' ,'Contactos.nombres','Contactos.apellidos', 'Contactos.email', 'Contactos.cestado_id', 'Users.id', 'Users.email'])
                                ->where(['Users.contacto_id' => $entidadcontacto['Contactos']['id']])
                                ->andWhere(['Users.trash' => 0])
                                ->andWhere(['Contactos.trash' => 0])
                                ->contain(['Contactos'])
                                ->first();

                            $nombre_entidad = $entidadcontacto['Entidads']['nombre'];
                            if(count($entidadcontacto) > 1) {
                                $org_vinculada = 1;
                                $email_organizacion = $usercontacto->email;
                                $personaentidad = $usercontacto->contacto->nombres . ' ' . $usercontacto->contacto->apellidos;
                            } elseif(count($usercontacto) > 0) {
                                $email_organizacion = $usercontacto->email;
                                $personaentidad = $usercontacto->contacto->nombres . ' ' . $usercontacto->contacto->apellidos;
                                $org_vinculada = 1;
                            } else {
                                $org_vinculada = 0;
                            }
                        }


                        // Si es una nueva organizacion o si la organizacion asociada tiene usuario creado
                        if($org_vinculada == 1 || !$existeEntidad) {


                            $this->loadModel('Contactos');
                            $contacto = $this->Contactos->find()
                                ->where(['Contactos.docid' => $data['Contactos']['docid'], 'Contactos.trash'=>0])
                                ->first();

                            $existeContacto = false;
                            if(count($contacto) < 1 ) {
                                $persona = TableRegistry::get('Contactos');
                                $contacto = new Contacto();
                                $tipodocont=$this->conTipoDocumento($data['Contactos']['nacional2']);

                                $nacional2="";

                                switch ($data['Contactos']['nacional2'])
                                {
                                    case 0:
                                        $nacional2="Extranjero";
                                        break;
                                    case 1:
                                        $nacional2="Salvadoreño";
                                        break;
                                }
                                $contacto->nombres = $data['Contactos']['nombres'];
                                $contacto->apellidos = $data['Contactos']['apellidos'];
                                $contacto->email = $data['Contactos']['email'];
                                $contacto->nacional = $nacional2;
                                $contacto->cdocidtipo_id = $tipodocont;
                                $contacto->docid =str_replace("-", "",  $data['Contactos']['docid']);
                                $contacto->ccontactotipo_id = $data['Contactos']['ccontactotipo_id'];

                                if($existeEntidad) {
                                    $contacto->cestado_id = $estadoAprobacion;
                                } else {
                                    $contacto->cestado_id = $estadoEsperaVerificacion;
                                }

                                $contacto->usuario = 'Registro plataforma';
                                $contacto->created = date('Y-m-d H:i:s');
                                $contacto->trash = 0;
                                $contacto->modified = null;

                                if(!$persona->save($contacto)) {
                                    $contacto = [];
                                }
                            } else {
                                $existeContacto = true;
                            }

                            // Verifica que haya un contacto
                            if(count($contacto) > 0) {
                                $persona_org = TableRegistry::get('Entidadcontactos');
                                $entidad_contacto = new Entidadcontacto();
                                $entidad_contacto->contacto_id = $contacto->id;
                                $entidad_contacto->entidad_id = $entidad->id;
                                $entidad_contacto->usuario = 'Registro plataforma';
                                $entidad_contacto->created = date('Y-m-d H:i:s');
                                $persona_org->save($entidad_contacto);

                                $this->loadModel('CorreoPlantillas');
                                // Si la organizacion no se habia registrado con anterioridad
                                if(!$existeEntidad) {
                                    if($this->CorreoPlantillas->exists(['CorreoPlantillas.nombre' => 'Plantilla para verificar cuenta','CorreoPlantillas.trash' => 0])) {
                                        $correoPlantilla = $this->CorreoPlantillas->find()
                                            ->where(['CorreoPlantillas.nombre' => 'Plantilla para verificar cuenta'])
                                            ->andWhere(['CorreoPlantillas.trash' => 0])
                                            ->first();

                                        $para = $contacto->email;
                                        $url = Router::url(['controller' => 'cuentaregistro', 'action' => 'crearusuario', $entidad->id, $contacto->id], true);
                                        $html = $correoPlantilla->contenido;
                                        $subject = 'Registro-Verificación de cuenta';
                                        $variables = [
                                            'nombrecompleto' => $contacto->nombres . ' ' . $contacto->apellidos,
                                            'entidad' => $entidad->nombre,
                                            'url' => $url,
                                            'unidad' => 'Unidad de Comunicaciones',
                                            'org' => 'FIAES',
                                            'institucion' => 'Fondo de la Iniciativa para las Américas'
                                        ];

                                        $this->loadModel('Cpreferences');
                                        if ($this->Cpreferences->exists(['Cpreferences.id' => 1, 'Cpreferences.trash' => 0])) {
                                            $general = $this->Cpreferences->find()
                                                ->select(['Cpreferences.params'])
                                                ->where(['Cpreferences.id' => 1])
                                                ->first()
                                                ->toArray();
                                            $configuraciones = $general['params'];

                                            $emailnotification = (isset($configuraciones['emailnotification'])) ? $configuraciones['emailnotification'] : 'soporte@tecnologias101.com';
                                        } else {
                                            $emailnotification = 'soporte@tecnologias101.com';
                                        }

                                        $notificacion = new NotificacionesEmail();
                                        $result = $notificacion->sendEmail($emailnotification, $para, $subject, $html, $variables);

                                        if ($result) {

                                            $_SESSION["exito-correo"]='Se ha envíado un correo a: ' . $para;

                                            //$this->Flash->successlogin(__('Se ha envíado un correo a: ' . $para));
                                            $this->redirect(array('controller' => "pages", 'action' => "home"));
                                        } else {
                                            $_SESSION["fallo-correo"]='No se pudo enviar el correo electrónico. Intente nuevamente.';
                                            //$this->Flash->errorlogin(__('No se pudo enviar el correo electrónico. Intente nuevamente.'));
                                            $this->redirect(array('controller' => "pages", 'action' => "home"));
                                        }
                                    } else {
                                        $_SESSION["fallo-correo"]='No se ha encontrado la plantilla del correo electrónico.';
                                        //$this->Flash->errorlogin(__('No se ha encontrado la plantilla del correo electrónico.'));
                                        $this->redirect(array('controller' => "pages", 'action' => "home"));
                                    }

                                    // Si la organizacion se habia registrado con anterioridad
                                } else {
                                    if($this->CorreoPlantillas->exists(['CorreoPlantillas.nombre' => 'Plantilla para solicitud de aprobacion de cuenta','CorreoPlantillas.trash' => 0])) {
                                        $correoPlantilla = $this->CorreoPlantillas->find()
                                            ->where(['CorreoPlantillas.nombre' => 'Plantilla para solicitud de aprobacion de cuenta'])
                                            ->andWhere(['CorreoPlantillas.trash' => 0])
                                            ->first();

                                        $para = $email_organizacion;
                                        $url = Router::url(['controller' => 'contactos', 'action' => 'aprobacioncuenta', $entidad->id, $contacto->id], true);
                                        $html = $correoPlantilla->contenido;
                                        $variables = [
                                            'personaentidad' => $personaentidad,
                                            'entidad' => $nombre_entidad,
                                            'nombrecompleto' => $contacto->nombres . ' ' . $contacto->apellidos,
                                            'url' => $url,
                                            'unidad' => 'Unidad de Comunicaciones',
                                            'org' => 'FIAES',
                                            'institucion' => 'Fondo de la Iniciativa para las Américas'
                                        ];
                                        $subject = 'Registro-Solicitud de aprobación de cuenta de ' . $contacto->nombres . ' ' . $contacto->apellidos;

                                        $this->loadModel('Cpreferences');
                                        if ($this->Cpreferences->exists(['Cpreferences.id' => 1, 'Cpreferences.trash' => 0])) {
                                            $general = $this->Cpreferences->find()
                                                ->select(['Cpreferences.params'])
                                                ->where(['Cpreferences.id' => 1])
                                                ->first()
                                                ->toArray();
                                            $configuraciones = $general['params'];

                                            $emailnotification = (isset($configuraciones['emailnotification'])) ? $configuraciones['emailnotification'] : 'soporte@tecnologias101.com';
                                        } else {
                                            $emailnotification = 'soporte@tecnologias101.com';
                                        }

                                        $notificacion = new NotificacionesEmail();
                                        $result = $notificacion->sendEmail($emailnotification, $para, $subject, $html, $variables);

                                        if ($result) {
                                            $_SESSION["exito-correo"]='Se ha envíado un correo a: ' . $para;
                                            //$this->Flash->successlogin(__('Se ha envíado un correo a: ' . $para));
                                            $this->redirect(array('controller' => "pages", 'action' => "home"));
                                        } else {
                                            $_SESSION["fallo-correo"]='No se pudo enviar el correo electrónico. Intente nuevamente.';
                                            //$this->Flash->errorlogin(__('No se pudo enviar el correo electrónico. Intente nuevamente.'));
                                            $this->redirect(array('controller' => "pages", 'action' => "home"));
                                        }
                                    } else {
                                        $_SESSION["fallo-correo"]='No se ha encontrado la plantilla del correo electrónico.';
                                        //$this->Flash->errorlogin(__('No se ha encontrado la plantilla del correo electrónico.'));
                                        $this->redirect(array('controller' => "pages", 'action' => "home"));
                                    }

                                    if($this->CorreoPlantillas->exists(['CorreoPlantillas.nombre' => 'Plantilla para espera de aprobacion de la cuenta','CorreoPlantillas.trash' => 0])) {
                                        $correoPlantilla = $this->CorreoPlantillas->find()
                                            ->where(['CorreoPlantillas.nombre' => 'Plantilla para espera de aprobacion de la cuenta'])
                                            ->andWhere(['CorreoPlantillas.trash' => 0])
                                            ->first();

                                        $para = $contacto->email;
                                        //$url = Router::url(['controller' => 'contactos', 'action' => 'aprobacioncuenta', $entidad->id, $contacto->id], true);
                                        $html = $correoPlantilla->contenido;
                                        $variables = [
                                            'nombrecompleto' => $contacto->nombres . ' ' . $contacto->apellidos,
                                            'entidad' => $nombre_entidad,
                                            //'url' => $url,
                                            'unidad' => 'Unidad de Comunicaciones',
                                            'org' => 'FIAES',
                                            'institucion' => 'Fondo de la Iniciativa para las Américas'
                                        ];
                                        $subject = 'Registro-Solicitud de aprobación de cuenta de ' . $contacto->nombres . ' ' . $contacto->apellidos;

                                        $this->loadModel('Cpreferences');
                                        if ($this->Cpreferences->exists(['Cpreferences.id' => 1, 'Cpreferences.trash' => 0])) {
                                            $general = $this->Cpreferences->find()
                                                ->select(['Cpreferences.params'])
                                                ->where(['Cpreferences.id' => 1])
                                                ->first()
                                                ->toArray();
                                            $configuraciones = $general['params'];

                                            $emailnotification = (isset($configuraciones['emailnotification'])) ? $configuraciones['emailnotification'] : 'soporte@tecnologias101.com';
                                        } else {
                                            $emailnotification = 'soporte@tecnologias101.com';
                                        }

                                        $notificacion = new NotificacionesEmail();
                                        $result = $notificacion->sendEmail($emailnotification, $para, $subject, $html, $variables);

                                        if ($result) {
                                            $_SESSION["exito-correo"]='Se ha envíado un correo a: ' . $para;
                                            //$this->Flash->successlogin(__('Se ha envíado un correo a: ' . $para));
                                            $this->redirect(array('controller' => "pages", 'action' => "home"));
                                        } else {
                                            $_SESSION["fallo-correo"]='No se pudo enviar el correo electrónico. Intente nuevamente.';
                                            //$this->Flash->errorlogin(__('No se pudo enviar el correo electrónico. Intente nuevamente.'));
                                            $this->redirect(array('controller' => "pages", 'action' => "home"));
                                        }
                                    } else {
                                        $_SESSION["fallo-correo"]='No se ha encontrado la plantilla del correo electrónico.';
                                        //$this->Flash->errorlogin(__('No se ha encontrado la plantilla del correo electrónico.'));
                                        $this->redirect(array('controller' => "pages", 'action' => "home"));
                                    }
                                }

                                return $this->redirect(['action' => 'home']);

                            // Si la variable que contiene al contacto esta vacia
                            } else {
                               /* $_SESSION["fallo-correo"]=' Si la variable que contiene al contacto esta vacia.';
                                $this->redirect(array('controller' => "pages", 'action' => "home"));*/

                            }
                        // Si a la organizacion asociada al contacto no se le ha creado un usuario
                        } else {
                            $_SESSION["fallo-correo"]='Su cuenta no puede ser registrada porque no se le ha creado un usuario a la organización asociada.';
                            $this->redirect(array('controller' => "pages", 'action' => "home"));
                        }
                    // Si la variable que contiene a la entidad esta vacia
                    } else {

                    }
                }
            } else {
                $_SESSION["solicitud-error"] = 1;
                $this->redirect(array('controller' => "pages", 'action' => "solicitud_registro"));
            }

            $this->loadModel('Ccontactotipos');
            $ccontactotipos = $this->Ccontactotipos->find('list')
                ->where(['Ccontactotipos.trash' => 0])
                ->andWhere(['Ccontactotipos.cestado_id' => $estadoActivo]);

            $docidtipos = new CdocidtiposController();
            $docidtiposPersonaId = $docidtipos->getTipoDocumento('Contactos');
            $docidtiposOrgId = $docidtipos->getTipoDocumento('Entidads');

            $this->loadModel('Cdocidtipos');
            $cdocidtipospersona = $this->Cdocidtipos->find('list')
                ->where(['Cdocidtipos.trash' => 0])
                ->andWhere(['Cdocidtipos.id IN' => $docidtiposPersonaId]);

            $cdocidtiposorg = $this->Cdocidtipos->find('list')
                ->where(['Cdocidtipos.trash' => 0])
                ->andWhere(['Cdocidtipos.id IN' => $docidtiposOrgId]);

            $this->loadModel('Centidadtipos');
            $centidadtipos = $this->Centidadtipos->find('list')
                ->where(['Centidadtipos.trash' => 0])
                ->andWhere(['Centidadtipos.cestado_id' => $estadoActivo]);

            $this->loadModel('Centidadrols');
            $centidadrols = $this->Centidadrols->find('list')
                ->where(['Centidadrols.trash' => 0])
                ->andWhere(['Centidadrols.cestado_id' => $estadoActivo]);

            $active = 'registro';
            
            $this->set(compact('title', 'cdocidtipospersona', 'cdocidtiposorg', 'ccontactotipos', 'centidadtipos', 'centidadrols', 'docid_org', 'docid_persona', 'info', 'tipo_registro', 'active'));
            
        // Si se ha accedido a la pantalla a travez de la url
        } else {
            $_SESSION["solicitud-errorpost"] = 1;
            //$this->Flash->errorlogin(__('No se ha podido ingresar a esa pantalla.'));
            $this->redirect(array('controller' => "pages", 'action' => "solicitud_registro"));
        }
    }
// validar documento
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
    // Funcion que verifica si la informacion ingresada es correcta
    public function isValida($modelo, $data, $unicos, $multiple = null) {
        $info_valida = [];

        foreach($unicos as $unico) {
            $isunique = $this->isUnique($modelo, $unico, $data[$unico]);

            if(!$isunique) {
                $info_valida['isValida'] = false;
                $info_valida['campo'] = $unico;
                break;
            }
        }


        // Si la informacion ingresada es correcta
        if(!isset($info_valida['isValida'])) {
            $info_valida['isValida'] = true;
            $info_valida['campo'] = null;
        }

        return $info_valida;
    }

    // Funcion que verifica si el valor ingresado en un campo determinado es unico
    public function isUnique($modelo, $campo, $valor) {
        $this->loadModel($modelo);
        $info = $this->$modelo->find("all")
            ->where([$modelo . '.' . $campo => $valor, $modelo . '.trash'=>0]);

        if($info->count()>0){
            $isunique = false;
        } else {
            $isunique = true;
        }

        return $isunique;
    }

    // Funcion que obtiene el estado de espera de aprobacion de la configuracion general
    public function getEstadoAprobacion()
    {
        $this->loadModel('Cpreferences');
        if ($this->Cpreferences->exists(['Cpreferences.id' => 1, 'Cpreferences.trash' => 0])) {
            $general = $this->Cpreferences->find()
                ->select(['Cpreferences.params'])
                ->where(['Cpreferences.id' => 1])
                ->first()
                ->toArray();
            $configuraciones = $general['params'];

            $estadoaprobacion = (isset($configuraciones['estadoesperandoaprobacion'])) ? $configuraciones['estadoesperandoaprobacion'] : null;

            return $estadoaprobacion;
        } else {
            return null;
        }
    }

    public function cakePdfDownload($name = null)
    {
        Configure::write('CakePdf.download', true);
        Configure::write('CakePdf.filename', "MyCustomName.pdf");
        Configure::write("CakePdf.engine", "CakePdf.Dompdf");

    }
}
