<?php
namespace App\Controller;

use Cake\Event\Event;

/**
 * Notificacions Controller
 *
 * @property \App\Model\Table\NotificacionsTable $Notificacions
 *
 * @method \App\Model\Entity\Notificacion[] paginate($object = null, array $settings = [])
 */
class NotificacionsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->modelo = "Notificacions";
        $this->modelo_extra = "Notificacioncolas";
        $this->loadModel('Notificacioncolas');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($band = null)
    {
        $dirdefault='asc';
        $paginacion = ($band==null)?20:1000000;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data= $this->request->getData();
            $data['Notificacioncolas']['trash']='1';
            $busqueda = new BusquedaController();
            $query = $busqueda->busqueda($this->modelo_extra, 'Notificacioncolas.created', 'asc', $data, $paginacion);
        } elseif (isset($_SESSION["tabla[$this->modelo_extra]"])) {
            $session =  $_SESSION["tabla[$this->modelo_extra]"];
            $data = $session['data'];
            $busqueda = new BusquedaController();
            if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                $field = $this->request->getQuery()["sort"];
                $direction = $this->request->getQuery()["direction"];
                $flagdirection =parent::dirSort($direction);
                if(!empty($flagdirection))
                    $dirdefault=$flagdirection;
                $query = $busqueda->busqueda($this->modelo_extra, 'Notificacioncolas.created', 'asc', $data, $paginacion, $field, $direction);
            } else {
                $query = $busqueda->busqueda($this->modelo_extra, 'Notificacioncolas.created', 'asc', $data, $paginacion);
            }
        } else {
            if(isset($this->request->getQuery()["sort"]) && count($this->request->getQuery())>0){
                $field = $this->request->getQuery()["sort"];
                $direction = $this->request->getQuery()["direction"];
                $flagdirection =parent::dirSort($direction);
                if(!empty($flagdirection))
                    $dirdefault=$flagdirection;
                $query = $this->Notificacioncolas->find()
                    ->where(['Notificacioncolas.user_id' => $this->Auth->user('id')])
                    ->andwhere(['Notificacioncolas.enviada' => 1])
                    ->order([$field => $direction])
                    ->limit($paginacion);
            }else {
                $query = $this->Notificacioncolas->find()
                    ->where(['Notificacioncolas.user_id' => $this->Auth->user('id')])
                    ->andwhere(['Notificacioncolas.enviada' => 1])
                    ->order(['Notificacioncolas.created'=>"asc"])
                    ->limit($paginacion);
            }
        }
        $query->contain('Notificacions');
        $notificacions = $this->Paginator->paginate($query);
        $titulo[0]['alias'] = "Notificaciones";
        $meses=['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $nav="Notificaciones";
        $userName=$this->Auth->user('username');
        $this->set(compact('notificacions','titulo', 'meses', 'nav', 'dirdefault','userName'));
        $this->set('_serialize', ['notificacions']);
    }

    public function vertodos() {
        unset($_SESSION["tabla[$this->modelo_extra]"]);
        $this->redirect(array('controller'=> $this->modelo, 'action'=> "index"));
        $this->autoRender=false;
    }
}
