<?php

namespace ULPFTests\Presentation;

require_once __DIR__ . '/../../ULPF/Presentation/Controller/ControllerAbstract.php';
require_once __DIR__ . '/ControllerMock.php';
require_once __DIR__ . '/../../ULPF/Presentation/FrontController.php';

use ULPF\Presentation\FrontController;

/**
 * @uses \ULPF\Presentation\Controller\ControllerAbstract
 * @uses \ULPF\Presentation\Http\Request
 * @uses \ULPF\Presentation\Http\Response
 * 
 * @coversDefaultClass \ULPF\Presentation\FrontController
 * @covers ::<protected>
 */
class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * GET  api.example.com/heartbeat/ -> Heartbeat::getIndex()
     * 
     * @covers ::__construct
     * @covers ::run
     * 
     * @test
     */
    public function parseGetWithDefaultAction()
    {
        
        // Mock request object
        $requestMock = $this->getMockBuilder('\ULPF\Presentation\Http\Request')
            ->setMethods(array('getMethod', 'getSegmentKeys'))
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('get'));
        $requestMock->expects($this->any())
            ->method('getSegmentKeys')
            ->will($this->returnValue(array('controllerMock')));
        
        // Mock response object
        $responseMock = $this->getMockBuilder('\ULPF\Presentation\Http\Response')
            ->setMethods(array('setBody'))
            ->getMock();
        $responseMock->expects($this->once())
            ->method('setBody')
            ->with('I Beat!');
        
        // Get front controller
        $frontController = new FrontController($requestMock, $responseMock);
        
        $this->assertInstanceOf(
            '\ULPF\Presentation\Http\Response',
            $frontController->run()
        );
        
    }
        
}