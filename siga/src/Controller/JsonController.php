<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

class JsonController extends AppController {
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadModel('Cpreferences');
    }

    /*Función para obtener el Json de la tabla preferencias de nivel 1 con un retorno de array simple
      $nombre -> valor para realizar filtro
    */
    public function preferencesLevel1($nombre, $tipo=null, $option_tipo=null){
        $array=[];
        $json=$this->Cpreferences->find('list',[
            'keyField' => '0',
            'valueField' => 'params'
        ])
            ->where(['nombre in'=>[$nombre]])
            ->toArray();
        switch($tipo){
            case 'publicado':
                return $json[0]['estadopublicado'];
                break;
            case 'archivado':
                return $json[0]['estadoarchivo'];
                break;
            case 'borrador':
                return $json[0]['estadoborrador'];
                break;
            case 'general':
                return $json[0]['formatomoneda'];
            case 'dashboard':
                if(isset($json[0][$option_tipo])) return $json[0][$option_tipo];
                else return [];
                break;
            case 'fichatecnicaPDLS':
                if(isset($json[0][$option_tipo])) return $json[0][$option_tipo];
                else return [];
                break;
            default:
                foreach ($json as $key) {
                    foreach($key as $second){
                        if(!is_array($second))
                            array_push($array,$second);
                    }
                }
                return $array;
        }
    }

    public function getEstadosConfGeneral($estado = null)
    {
        if ($this->Cpreferences->exists(['Cpreferences.id' => 1, 'Cpreferences.trash' => 0])) {
            $general = $this->Cpreferences->find()
                ->select(['Cpreferences.params'])
                ->where(['Cpreferences.id' => 1])
                ->first()
                ->toArray();
            $configuraciones = $general['params'];

            $estadoactivo = (isset($configuraciones['estadoactivo'])) ? $configuraciones['estadoactivo'] : null;
            $estadoinactivo = (isset($configuraciones['estadoinactivo'])) ? $configuraciones['estadoinactivo'] : null;
            $estadoesperaaprobacion = (isset($configuraciones['estadoesperandoaprobacion'])) ? $configuraciones['estadoesperandoaprobacion'] : null;
            $estadoesperaverificacion = (isset($configuraciones['estadoesperandoverificacion'])) ? $configuraciones['estadoesperandoverificacion'] : null;
            $estadocontactorechazado = (isset($configuraciones['estadocontactorechazado'])) ? $configuraciones['estadocontactorechazado'] : null;

            if(is_null($estado)) {
                return [$estadoactivo, $estadoinactivo];
            } elseif($estado == 'activo') {
                return $estadoactivo;
            } elseif($estado == 'inactivo') {
                return $estadoinactivo;
            } elseif($estado == 'esperaaprobacion') {
                return $estadoesperaaprobacion;
            } elseif($estado == 'esperaverificacion') {
                return $estadoesperaverificacion;
            } elseif($estado == 'contactorechazado') {
                return $estadocontactorechazado;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getPreferenciaConvocatorias($nombre)
    {
        if ($this->Cpreferences->exists(['Cpreferences.nombre' => $nombre, 'Cpreferences.trash' => 0])) {
            $preferencia_convocatoria = $this->Cpreferences->find()
                ->where(['Cpreferences.nombre'=>$nombre])
                ->andWhere(['Cpreferences.trash' => 0])
                ->first();

            $convocatoria_ids = (isset($preferencia_convocatoria->params['recursoestructuras'])) ? $preferencia_convocatoria->params['recursoestructuras'] : [];
            $msj = (isset($preferencia_convocatoria->params['recursoestructuras']))
                ? "" :
                '<strong>Parámetro no definido:</strong><br>En el registro de configuración <strong>' . $nombre . '</strong>, el parámetro <strong>recursoestructuras</strong> no ha sido definido.';
        } else {
            $msj = '<strong>Configuración Incompleta:</strong> No existe el registro de configuración <strong>' . $nombre . '</strong>.';
            $convocatoria_ids = [];
        }

        return ["convocatoria_ids" => $convocatoria_ids, "msj" => $msj];
    }
}