<?php

// Define namespace
namespace ULPFTest\Bootstrap;

// Includes
require_once __DIR__ . '/../../../ULPF/Bootstrap/Configuration.php';
require_once __DIR__ . '/../../../ULPF/Bootstrap/Exception.php';

/**
 * Configuration test class
 * 
 * @coversDefaultClass ULPF\Bootstrap\Configuration
 * @covers ::<protected>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * Create configuration instance
     * 
     * @covers ::__construct
     */
    public function testCreateConfigurationInstance()
    {
        
        // Create Configuration instance
        $config = new \ULPF\Bootstrap\Configuration(__DIR__ . '/test.ini');
        
        // A valid Configuration instance should be available
        $this->assertInstanceOf(
            'ULPF\Bootstrap\Configuration',
            $config,
            'Configuration not created'
        );
        
    }
    
    /**
     * Try to create a config based on a not existing configuration file
     * 
     * @covers ::__construct
     * @expectedException ULPF\Bootstrap\Exception
     * @expectedExceptionMessage Configuration file does not exist
     */
    public function testCreateConfigurationWithNotExistingConfigurationFile()
    {
        
        // Create Configuration instance
        new \ULPF\Bootstrap\Configuration('notexisitngfile.ini');
        
    }
    
    /**
     * Retrieve a value from the configuration
     * 
     * @covers ::__construct
     * @covers ::get
     */
    public function testGetConfigurationValue()
    {
        
        // Create Configuration instance
        $config = new \ULPF\Bootstrap\Configuration(__DIR__ . '/test.ini');
        
        // Property foo should be set
        $this->assertEquals(
            'bar', $config->get('foo'), 'Configuration property \'foo\' not set'
        );
        
    }
    
    /**
     * Retrieve a not existing value from the configuration
     * 
     * @covers ::__construct
     * @covers ::get
     */
    public function testGetUnavailableConfigurationValue()
    {
        
        // Create Configuration instance
        $config = new \ULPF\Bootstrap\Configuration(__DIR__ . '/test.ini');
        
        // Property bar should not be set
        $this->assertNull(
            $config->get('bar'), 'Configuration property \'foo\' not set'
        );
        
    }
    
}