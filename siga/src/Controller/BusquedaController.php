<?php

namespace App\Controller;

class BusquedaController extends AppController
{
    //PARAMETROS: modelo, order by, tipo ordenamiento, data, paginacion
    //Parametro extras agregados el 13/oct/17: Para cuando se ha realizado una b�squeda y luego se requiera ordenar por el paginator->sort ubicados en los th de la tabla
    //  $sort_extra: campo por el que se ordenaran los registros
    //  $direction_extra: asc/desc
    public function busqueda($modelo = null, $orderby = null, $tipoordenamiento, $data = null, $pag=null, $sort_extra=null, $direction_extra=null, $only_active = null) {
        $busquedaText = [];
        $busquedaSelect = [];
        $session = [];
        $typeField=[];//array para indicar le nuevo tipo del campo, llenado con string para utilizarlo en consultas like de campos numéricos(integer,decimal(12,2))
        
        // Se obtienen los campos para realizar filtro segun lo ingresado en campo de texto
        $campos = explode(',', $data[$modelo]['parametro']);
        $this->loadModel($modelo);
        $query = $this->$modelo->find()->where([]);

        // Debe buscar solo los registro que tengan 0 en el campo trash
        if($data[$modelo]['trash'] == '0') {
            $query->andWhere([$modelo . '.trash' => 0]);
            $session['activo'] = false;
        } else {
            $session['activo'] = true;
        }

        //Filtro para checkbox
        $session['checked']=0;
        if(isset($data[$modelo]['checkbox'])){
            foreach ($data[$modelo]['checkbox'] as $key => $value) {
                if($value==1){//se realizara query s�lo si ha checkeado el elemento
                    $value=(int) $value;
                    array_push($busquedaText,[$modelo . '.' . $key => 1]);
                    $query->andWhere([$busquedaText]);
                    $session['checked']=$value;//sesion que indica si estara o no checkeado el elemento, lo que viaja es 0/1
                }else{
                    $session['checked']=$value;
                }
            }

        }


        // Se realiza filtro segun lo ingresado en campo de texto
        if($data[$modelo]['search_text']!="") {
            $session['search_text'] = $data[$modelo]['search_text'];
        }
        else if($data[$modelo]['search_text2']!="") {
            $session['search_text'] = $data[$modelo]['search_text2'];
        }
        else
        {
            $session['search_text']="";
        }
        foreach ($campos as $campo) {
            if($campo == "id" || $campo=='version'  || $campo == "idaccion" || $campo == "accion_id"){
                if(strcmp($modelo,'ViewDataProyecto')===0 || strcmp($modelo,'VlistaAccion')===0){
                    $id = (string)  $session['search_text'];
                    array_push($busquedaText, [$modelo . '.' . $campo  =>  $id]);
                }else{
                    $typeField[$modelo . '.' . $campo]='string';//se indica el nuevo tipo del campo para realizar consulta
                    //el nuevo llenado de busquedaText comprende como índice el modelo.campo like y su valor a contener es lo proveniente del input
                    $busquedaText[$modelo . '.' . $campo . ' LIKE' ]='%' . (string)  $session['search_text'] . '%';
                }
            }
            else{
                if(count(explode('.', $campo))>1)
                {
                    /// Para consultar otro modelo
                    array_push($busquedaText, [explode('.', $campo)[0] . '.' . explode('.', $campo)[1] . ' LIKE' => '%' . (string)  $session['search_text'] . '%']);
                }
                else{
                    if(strcmp($modelo,'ViewDataProyecto')===0 || strcmp($modelo,'VlistaAccion')===0){
                        array_push($busquedaText, [$modelo . '.' . $campo . ' LIKE' => '%' . (string)  $session['search_text'] . '%']);
                    }else{
                        $typeField[$modelo . '.' . $campo]='string';
                        $busquedaText[$modelo . '.' . $campo . ' LIKE' ]='%' . (string)  $session['search_text'] . '%';
                    }
                }
            }
        }
        if(strcmp($modelo,'ViewDataProyecto')===0 || strcmp($modelo,'VlistaAccion')===0)$query->andWhere(['OR' => $busquedaText]);
        else $query->andWhere(['OR' => $busquedaText],$typeField);//se asigna el filtro like junto con el nuevo tipo de los campos a buscar

        // Se realiza filtro segun lo seleccionado en cada select
        if(isset($data[$modelo]['select'])) {
            foreach ($data[$modelo]['select'] as $key => $value) {
                if($value != '') {
                    if($key == "departamento" || $key == "careaapoyo_id"){
                        $modeltmp = "";
                        if($key=="departamento") {
                            $modeltmp = "Provserviciocoberturas";
                            $key = "departamento_id";
                        }
                        else {
                            $modeltmp = "Provservicioareaapoyos";
                            $key = "careaapoyo_id";
                        }
                        array_push($busquedaSelect, [$modeltmp . '.' . $key  => $value]);
                    }else{
                        if($key =="tipo" || $key == "area"){
                            array_push($busquedaSelect, [$modelo . '.' . $key.' LIKE'  => '%'.$value.'%']);
                        }else{
                            array_push($busquedaSelect, [$modelo . '.' . $key  => $value]);
                        }
                    }


                }
            }

            if (isset($data[$modelo]['select']['cestado_id']) && $data[$modelo]['select']['cestado_id'] == ''){
                $m=strtolower($modelo);
                if(strcmp($m,'datarecursos')!=0 && strcmp($m,'recursoestructuras')!=0 && strcmp($m,'formdinamics')!=0 && strcmp($m,'vaccions')!=0 && strcmp($m,'workflows')!=0){
                    $festado=[];
                    //array_push($festado, [$modelo.".cestado_id"=>1]);
                    //$query->andWhere(['AND'=>$festado]);
                }
            }

            if($only_active){
                array_push($busquedaSelect, [$modelo . '.cestado_id'   => 1]);
            }
            $query->andWhere(['AND' => $busquedaSelect]);
        }
        //debug($data[$modelo]['select']);
        //debug($busquedaSelect).die();
        // Se realiza filtro segun el rango de fechas seleccionado
        if(isset($data[$modelo]['fecha'])) {
            if(!empty($data[$modelo]['desde'])){
                $session['desde'] = $data[$modelo]['desde'];
                $desde = explode('/', $data[$modelo]['desde']);

                if($data[$modelo]['hasta'] != '') {
                    $session['hasta'] = $data[$modelo]['hasta'];
                    $hasta = explode('/', $data[$modelo]['hasta']);
                    if($data[$modelo]['fechahora'] == '1') {
                        $query->andWhere([
                            $modelo . '.' . $data[$modelo]['fecha'] . " >=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0] . " 00:00:00",
                        ]);

                        $query->andWhere([
                            $modelo . '.' . $data[$modelo]['fecha'] . " <=" => $hasta[2] . "-" . $hasta[1] . "-" . $hasta[0] . " 23:59:59"
                        ]);
                    } else {
                        $query->andWhere([
                            $modelo . '.' . $data[$modelo]['fecha'] . " >=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0]
                        ]);

                        $query->andWhere([
                            $modelo . '.' . $data[$modelo]['fecha'] . " <=" => $hasta[2] . "-" . $hasta[1] . "-" . $hasta[0]
                        ]);
                    }
                } else {
                    if($data[$modelo]['desde'] != ''){
                        if($data[$modelo]['fechahora'] == '1') {
                            $query->andWhere([
                                $modelo . '.' . $data[$modelo]['fecha'] . " >=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0] . " 00:00:00"
                            ]);
                            $query->andWhere([
                                $modelo . '.' . $data[$modelo]['fecha'] . " <=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0] . " 23:59:59"
                            ]);
                        } else {
                            $query->andWhere([
                                $modelo . '.' . $data[$modelo]['fecha'] . " >=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0]
                            ]);
                            $query->andWhere([
                                $modelo . '.' . $data[$modelo]['fecha'] . " <=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0]
                            ]);
                        }
                    }
                }
            }
        }


        // Seg�n el tipo de ordenamiento se realiza el order by de los datos
        if(!empty($sort_extra) && !empty($direction_extra)){
            $query->order([$sort_extra=>$direction_extra]);

        }else{
            if($tipoordenamiento == 'asc') {
                $query->orderAsc($orderby)
                    ->limit($pag);
            } else {
                $query->orderDesc($orderby)
                    ->limit($pag);
            }
        }


        $session['data'] = $data;
        $_SESSION['tabla['.$data[$modelo]['controller'].']'] = $session;

        return $query;
    }

    // Galeria
    public function busquedaGale($modelo = null, $orderby = null, $tipoordenamiento, $data = null, $pag=null, $sort_extra=null, $direction_extra=null) {
        $busquedaText = [];
        $busquedaSelect = [];
        $session = [];


        // Se obtienen los campos para realizar filtro segun lo ingresado en campo de texto
        $campos = explode(',', $data[$modelo]['parametro']);


        $this->loadModel($modelo);
        $query = $this->$modelo->find()->where([]);

        // Debe buscar solo los registro que tengan 0 en el campo trash
        if($data[$modelo]['trash'] == '0') {
            $query->andWhere([$modelo . '.trash' => 0]);
            $session['activo'] = false;
        } else {
            $session['activo'] = true;
        }

        //Filtro para checkbox
        $session['checked']=0;
        if(isset($data[$modelo]['checkbox'])){
            foreach ($data[$modelo]['checkbox'] as $key => $value) {
                if($value==1){//se realizara query s�lo si ha checkeado el elemento
                    $value=(int) $value;
                    array_push($busquedaText,[$modelo . '.' . $key => 1]);
                    $query->andWhere([$busquedaText]);
                    $session['checked']=$value;//sesion que indica si estara o no checkeado el elemento, lo que viaja es 0/1
                }else{
                    $session['checked']=$value;
                }
            }

        }


        // Se realiza filtro segun lo ingresado en campo de texto
        if($data[$modelo]['search_text']!="") {

            $arrayEtiquetas=[];
            $arrayEtiquetasId=[];

            $dataEtiquetas = $this->loadModel('Cetiquetas')->find('all')
                ->where(['Cetiquetas.trash' => 0,'Cetiquetas.cestado_id'=>1])
                ->andWhere(['Cetiquetas.id IN' =>$data[$modelo]['search_text']])
                ->toArray();

                foreach ($dataEtiquetas as $items)
                {
                 array_push( $arrayEtiquetasId,$items->id);
                 array_push( $arrayEtiquetas,$items->nombre);
                }
          //  $etiquetas=implode(",", $arrayEtiquetas);

            $session['search_text'] = implode(",", $arrayEtiquetas);
            $session['etiquetas']=$arrayEtiquetasId;
        }
        else if($data[$modelo]['search_text2']!="") {

            $arrayEtiquetas=[];
            $arrayEtiquetasId=[];

            $dataEtiquetas = $this->loadModel('Cetiquetas')->find('all')
                ->where(['Cetiquetas.trash' => 0,'Cetiquetas.cestado_id'=>1])
                ->andWhere(['Cetiquetas.id IN' =>$data[$modelo]['search_text']])
                ->toArray();

            foreach ($dataEtiquetas as $items)
            {
                array_push( $arrayEtiquetasId,$items->id);
                array_push( $arrayEtiquetas,$items->nombre);
            }
            //  $etiquetas=implode(",", $arrayEtiquetas);

            $session['search_text'] = implode(",", $arrayEtiquetas);
            $session['etiquetas']=$arrayEtiquetasId;
        }
        else
        {
            $session['search_text']="";
        }

        foreach ($campos as $campo) {
            if($campo == "id" || $campo=='version'  || $campo == "idaccion"){
                $id = (string)  $session['search_text'];
                array_push($busquedaText, [$modelo . '.' . $campo  =>  $id]);
            }
            else{
                if(count(explode('.', $campo))>1)
                {
                    /// Para consultar otro modelo
                    array_push($busquedaText, [explode('.', $campo)[0] . '.' . explode('.', $campo)[1] . ' LIKE' => '%' . (string)  $session['search_text'] . '%']);
                }
               else if($session['search_text']!=""){
                    array_push($busquedaText, [$modelo . '.' . $campo . ' LIKE' => '%' . (string)  $session['search_text'] . '%']);
                }
            }
        }

        $query->andWhere(['OR' => $busquedaText]);


        // Se realiza filtro segun lo seleccionado en cada select

        if(isset($data[$modelo]['select'])) {
            foreach ($data[$modelo]['select'] as $key => $value) {


                if($value != '') {
                    if($key != 'filename') {
                        array_push($busquedaSelect, [$modelo . '.' . $key => $value]);
                    }
                }
            }


            if (isset($data[$modelo]['select']['filename']) && $data[$modelo]['select']['filename'] != ''){
                $m=strtolower($modelo);
                if(strcmp($m,'ViewGaleria')!=0 && strcmp($m,'ViewGaleria')!=0){
                    $festado=[];
                  /*  if($data[$modelo]['select']['filename']=='excel')
                    {
                        array_push($festado, [$modelo.".filename LIKE '%application/vnd.ms-excel%' OR ".$modelo.".filetype LIKE '%application/vnd.openxmlformats-officedocument.spreadsheetml.sheet%'"]);

                        $query->andWhere(['OR'=>$festado]);
                    }
                    if($data[$modelo]['select']['filetype']=='excel')
                    {
                        array_push($festado, [$modelo.".filetype LIKE '%png%' OR ".$modelo.".filetype LIKE '%jpeg%'"]);

                        $query->andWhere(['OR'=>$festado]);
                    }
                  else
                  {*/
                      array_push($festado, [$modelo.".filename".' LIKE'=>'%'.$data[$modelo]['select']['filename']]);
                      $query->andWhere(['AND'=>$festado]);
              //    }

                }
            }


          $query->andWhere(['AND' => $busquedaSelect]);

        }

        // Se realiza filtro segun el rango de fechas seleccionado
        if(isset($data[$modelo]['fecha'])) {
            if(!empty($data[$modelo]['desde'])){
                $session['desde'] = $data[$modelo]['desde'];
                $desde = explode('/', $data[$modelo]['desde']);

                if($data[$modelo]['hasta'] != '') {
                    $session['hasta'] = $data[$modelo]['hasta'];
                    $hasta = explode('/', $data[$modelo]['hasta']);
                    if($data[$modelo]['fechahora'] == '1') {
                        $query->andWhere([
                            $modelo . '.' . $data[$modelo]['fecha'] . " >=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0] . " 00:00:00",
                        ]);

                        $query->andWhere([
                            $modelo . '.' . $data[$modelo]['fecha'] . " <=" => $hasta[2] . "-" . $hasta[1] . "-" . $hasta[0] . " 23:59:59"
                        ]);
                    } else {
                        $query->andWhere([
                            $modelo . '.' . $data[$modelo]['fecha'] . " >=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0]
                        ]);

                        $query->andWhere([
                            $modelo . '.' . $data[$modelo]['fecha'] . " <=" => $hasta[2] . "-" . $hasta[1] . "-" . $hasta[0]
                        ]);
                    }
                } else {
                    if($data[$modelo]['desde'] != ''){
                        if($data[$modelo]['fechahora'] == '1') {
                            $query->andWhere([
                                $modelo . '.' . $data[$modelo]['fecha'] . " >=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0] . " 00:00:00"
                            ]);
                            $query->andWhere([
                                $modelo . '.' . $data[$modelo]['fecha'] . " <=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0] . " 23:59:59"
                            ]);
                        } else {
                            $query->andWhere([
                                $modelo . '.' . $data[$modelo]['fecha'] . " >=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0]
                            ]);
                            $query->andWhere([
                                $modelo . '.' . $data[$modelo]['fecha'] . " <=" => $desde[2] . "-" . $desde[1] . "-" . $desde[0]
                            ]);
                        }
                    }
                }
            }
        }

        // Seg�n el tipo de ordenamiento se realiza el order by de los datos
        if(!empty($sort_extra) && !empty($direction_extra)){
            $query->order([$sort_extra=>$direction_extra]);

        }else{
            if($tipoordenamiento == 'asc') {
                $query->orderAsc($orderby)
                    ->limit($pag);
            } else {
                $query->orderDesc($orderby)
                    ->limit($pag);
            }
        }

        $session['data'] = $data;
        $_SESSION['tabla['.$data[$modelo]['controller'].']'] = $session;
        return $query;
    }


}