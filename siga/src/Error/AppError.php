<?php
namespace App\Error;

use Cake\Error\BaseErrorHandler;

class AppError extends BaseErrorHandler
{
    public function handleFatalError($code, $description, $file, $line)
    {
        return '';
    }

    public function _displayError($error, $debug)
    {
        if($debug) {
            echo 'Existe un error en el archivo: <strong>' . $error['file'] . '</strong> en la linea <strong>' . $error['line'] . '</strong>.<br>';
            echo 'Mensaje de error: <strong>' . $error['description'] . '</strong>. <br><br>';
        }
    }

    public function _displayException($exception)
    {
        echo $exception;
        //echo 'There has been an exception!';
    }
}