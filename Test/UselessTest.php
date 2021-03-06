<?php

// Include framework
include __DIR__ . '/../ULPF/UseLess.php';

/**
 * UseLess Framework test class
 */
class UselessTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * UseLess instance
     * 
     * @var \ULPF\UseLess
     */
    protected $instance = null;
    
    /**
     * Test setup
     */
    public function setUp() {
        
        // Parent setup
        parent::setUp();
        
        // Create instance of framework
        $this->instance = new \ULPF\UseLess();
        
    }
    
    /**
     * Run the framework
     */
    public function testRun()
    {
        
        // Run instance and get result
        $result = $this->instance->run();
        
        // Result should be "Hello World!"
        $this->assertEquals('Hello World!', $result, 'She does not say Hello!');
        
    }
    
}