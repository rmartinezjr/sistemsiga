<?php

namespace App\Error;

use Cake\Error\ExceptionRenderer;
use App\Controller\ErrorController;
use Cake\Event\Event;

class AppExceptionRenderer extends ExceptionRenderer
{
    /* protected function _getController()
    {
        $controller = parent::_getController();
        //$controller->getEventManager()->on();
        /*$controller->getEventManager()->on('Controller.beforeRender', function (Event $event) {
            $event->getSubject()->viewBuilder()->theme('Error');
        });*/
    /*   return $controller;
   }*/
}