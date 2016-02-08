<?php

namespace ULPFTest\Presentation\Http;

require_once __DIR__ . '/../../../ULPF/Presentation/Http/Response.php';

use ULPF\Presentation\Http\Response;

/**
 * @coversDefaultClass \ULPF\Presentation\Http\Response
 * @covers ::<protected>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * Sending simple successful response returns 200 header and JSON response
     * 
     * @covers ::setBody
     * @covers ::getBody
     * @covers ::getStatusCode
     * @test
     */
    public function sendOk()
    {

        // Prepare a response with a string as a body
        $response = new Response();
        $response->setBody('Foo');
        
        // String should be JSON formatted
        $this->assertSame('"Foo"', $response->getBody());
        
        // Get status code
        $this->assertSame('200 OK', $response->getStatusCode());
        
    }
    
    /**
     * Sending an internal server error when debugging is enabled
     * 
     * @covers ::enableDebug
     * @covers ::internalServerError
     * @covers ::getBody
     * @covers ::getStatusCode
     * @test
     */
    public function sendInternalServerErrorWithDebug()
    {
        
        // Get response and enable debugging
        $response = new Response();
        $response->enableDebug();
        $response->internalServerError(new \Exception());
        
        // Get response when an exception is is set
        $responseBody = \json_decode(
            $response->getBody(),
            true
        );
        
        // Assert message and trace data are set
        $this->assertArrayHasKey('message', $responseBody);
        $this->assertArrayHasKey('trace', $responseBody);
        
        // Get status code
        $this->assertSame(
            '500 Internal Server Error',
            $response->getStatusCode()
        );
        
    }
    
}