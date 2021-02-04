<?php
namespace App\Notifications;

use App\Controller\AppController;
use Cake\Network\Http\Client;

class NotificacionesEmail
{
    /*************************************************************************************/
    /* Funcion para envio de correo electronicos.                                        */
    /* Recibe los parametros:                                                            */
    /* + from: es un string que contiene quien envia el correo electronico.              */
    /* + to: es un string que contiene hacia quien va dirigido el correo.                */
    /* + subject: es un string que contiene el asunto del correo electronico.            */
    /* + html: es un string que contiene la plantilla a utilizar para mostrar el         */
    /*   cuerpo del correo electronico.                                                  */
    /* + variables: es un array que contiene las variables que son usadas en el cuerpo   */
    /*   del correo electronico.                                                         */
    /*   $variables = ['var1' => 'val1', 'var2' => 'val2', ...  ]                        */
    /*************************************************************************************/
    public function sendEmail($from, $to, $subject, $html, $variables = null) {
        //$sender = 'ANSP';
        $app = new AppController();

        $app->loadModel('Cpreferences');
        if ($app->Cpreferences->exists(['Cpreferences.nombre' => 'General', 'Cpreferences.trash' => 0])) {
            $general = $app->Cpreferences->find()
                ->select(['Cpreferences.params'])
                ->where(['Cpreferences.nombre' => 'General'])
                ->first()
                ->toArray();
            $configuraciones = $general['params'];
            $sender = (isset($configuraciones['sender'])) ? $configuraciones['sender'] : 'Academia Nacional de Seguridad Publica de El salvador';
        } else {
            $sender = 'ANSP';
        }

        if((!is_null($variables)) && (count($variables) > 0)) {
            foreach ($variables as $key => $variable) {
                $html = str_replace('$' . $key, $variable, $html);
            }
        }

        $merge_data = new \stdClass();
        $merge_data->PerMessage =
            array(
                array(
                    array(
                        'Field'=>'DeliveryAddress',
                        'Value'=> $to  // Correo electronico destino
                    )
                )
            );

        $data = new \stdClass();
        $data->ServerId='37340';
        $data->ApiKey= 'Fj35Wai2QYt64HxPz8q9';

        $data->Messages =
            array(
                array(
                    'MergeData'=>$merge_data,
                    'Subject' => $subject,
                    'To'=>
                        array(
                            array(
                                'EmailAddress'=>'%%DeliveryAddress%%'
                            )
                        ),
                    'From'=>
                        array(
                            'EmailAddress' => $from,
                            'FriendlyName' => $sender // Remitente
                        ),
                    'HtmlBody'=> $html
                )
            );

        $bodyJson = json_encode($data);
        $http = new Client();
        $result = $http->post(
            'https://inject.socketlabs.com/api/v1/email',
            $bodyJson,
            ['type' => 'json']
        );

        return $result;
    }
}