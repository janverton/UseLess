<?php

// Define namespace
namespace ULPFTest\Storage\File;

// Includes
require_once __DIR__ . '/../../../ULPF/Storage/File/Csv.php';
require_once __DIR__ . '/../../../ULPF/Storage/File/Handler.php';
require_once __DIR__ . '/../../../ULPF/Storage/File/Exception.php';

/**
 * Csv file test class
 * 
 * @uses \ULPF\Storage\File\Handler
 * 
 * @coversDefaultClass \ULPF\Storage\File\Csv
 * @covers ::<protected>
 */
class CsvTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * Csv instance
     * 
     * @var \ULPF\Storage\File\Csv
     */
    protected $instance = null;
    
    /**
     * Test setup
     */
    public function setUp() {
        
        // Parent setup
        parent::setUp();
        
        // Create instance of Csv object
        $this->instance = new \ULPF\Storage\File\Csv();
        
    }
    
    /**
     * Read test.csv
     * 
     * @covers ::setFileHandler
     * @covers ::setDelimiter
     * @covers ::readCsv
     */
    public function testReadCsv()
    {
        
        // Prepare file handler
        $fileHandler = new \ULPF\Storage\File\Handler();
        $fileHandler->setRootDirectory(__DIR__);
        $this->instance->setFileHandler($fileHandler);
        
        // Read test.csv contents
        $contents = $this->instance
            ->setDelimiter(',')
            ->readCsv('test.csv');
        
        // Assert contents match
        $this->assertEquals(
            array(0 => array('Use','Less')),
            $contents
        );
        
    }
    
}