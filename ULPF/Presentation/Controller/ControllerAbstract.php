<?php

namespace ULPF\Presentation\Controller;

use ULPF\Presentation\Http\Request;
use ULPF\Presentation\Http\Response;

abstract class ControllerAbstract
{
    
    /**
     * Http request instance
     * @var Request
     */
    protected $request;
    
    /**
     * Http response instance
     * @var Response
     */
    protected $response;
    
    /**
     * Construct controller
     * 
     * @param Request $request
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    
}
