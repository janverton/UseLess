<?php

class ControllerMock extends \ULPF\Presentation\Controller\ControllerAbstract
{
    
    public function getIndexAction()
    {
        $this->response->setBody('I Beat!');
    }
    
}