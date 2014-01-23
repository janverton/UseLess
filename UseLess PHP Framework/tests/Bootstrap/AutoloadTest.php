<?php

// Define namespace
namespace ULPFTest\Bootstrap;

// Includes
require_once __DIR__ . '/../../Bootstrap/Autoload.php';
require_once __DIR__ . '/../../Bootstrap/Exception.php';

/**
 * Autoload test class
 * 
 * @coversDefaultClass ULPF\Bootstrap\Autoload
 * @covers ::<protected>
 */
class AutoloadTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * Test class loading from the root directory
     * 
     * @covers ::__construct
     * @covers ::__destruct
     */
    public function testAutoloadClassFromRoot()
    {
        
        // Create autoload instance
        $autoload = new \ULPF\Bootstrap\Autoload(__DIR__);
        
        // Get the testclass
        $class = new \AutoloadTestClass();
        
        // Make sure the correct instance is returned
        $this->assertInstanceOf(
            'AutoloadTestClass', $class, 'Autoload did not load the test class'
        );
        
        // Make sure autoloader gets unset
        $autoload->__destruct();
    }
    
    /**
     * Try to load a not existing class
     * 
     * @covers ::__construct
     * @expectedException \ULPF\Bootstrap\Exception
     * @expectedExceptionMessage Autoload File not found: 
     */
    public function testAutoloadNotExistingClass()
    {
        
        // Create autoload instance
        new \ULPF\Bootstrap\Autoload(__DIR__);
        
        // Get not existing class
        new \ClassDoesNotExist();
        
    }
    
    /**
     * Instantiate the autoloader with a not existing root path
     * 
     * @covers ::__construct
     * @expectedException \ULPF\Bootstrap\Exception
     * @expectedExceptionMessage Autoload directory does noet exist: 
     */
    public function testAutoloadFromNotExistingRoot()
    {
        
        // Create autoload with a wrong root path
        new \ULPF\Bootstrap\Autoload('\Not\Existing\Dir');
        
    }
    
}