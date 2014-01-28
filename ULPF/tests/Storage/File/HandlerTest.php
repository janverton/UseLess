<?php

// Define namespace
namespace ULPFTest\Storage\File;

// Includes
require_once __DIR__ . '/../../../Storage/File/Handler.php';
require_once __DIR__ . '/../../../Storage/File/Exception.php';

/**
 * File storage test class
 * 
 * @coversDefaultClass ULPF\Storage\File\Handler
 * @covers ::<protected>
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * File storage component instance
     * 
     * @var \ULPF\Storage\File\Handler
     */
    protected $instance = null;
    
    /**
     * Test setup
     */
    public function setUp() {
        
        // Parent setup
        parent::setUp();
        
        // Create instance of framework
        $this->instance = new \ULPF\Storage\File\Handler();
        
    }
    
    /**
     * Set a root directory
     * 
     * @covers ::setRootDirectory
     */
    public function testSetRootDirectory()
    {
        
        // When directory is set an instance of the file handler is returned
        $this->assertInstanceOf(
            '\ULPF\Storage\File\Handler',
            $this->instance->setRootDirectory('/tmp')
        );
        
    }
    
    /**
     * Set a root directory which does not exist
     * 
     * @covers ::setRootDirectory
     * @expectedException \ULPF\Storage\File\Exception
     * @expectedExceptionMessage Root directory /does/not/exist does not exist
     */
    public function testSetNotExistingBaseDirectory()
    {
        
        // Set root directory which does not exist
        $this->instance->setRootDirectory('/does/not/exist');
        
    }
    
    /**
     * Set a root directory which is not writable by the current user
     * 
     * @covers ::setRootDirectory
     * @expectedException \ULPF\Storage\File\Exception
     * @expectedExceptionMessage Root directory /home is not writeable
     */
    public function testSetUnwritableBaseDirectory()
    {
        
        // Set root directory which does not exist
        $this->instance->setRootDirectory('/home');
        
    }
    
    /**
     * Save a file
     * 
     * @covers ::setRootDirectory
     * @covers ::saveContents
     */
    public function testSaveFile()
    {
        
        // Clear test file
        if (\file_exists('/tmp/test.txt')) {
            \unlink('/tmp/test.txt');
        }
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // When a file is saved an instance of the file handler is returned
        $this->assertInstanceOf(
            '\ULPF\Storage\File\Handler',
            $this->instance->saveContents('Test data', 'test.txt')
        );
        
        // File should exist
        $this->assertFileExists('/tmp/test.txt');
        
        // Get file contents
        $contents = \file_get_contents('/tmp/test.txt', 'r');
        
        // Saved file data should match
        $this->assertEquals('Test data', $contents);
        
    }
    
    /**
     * Save file in a directory
     * 
     * @covers ::setRootDirectory
     * @covers ::saveContents
     */
    public function testSaveFileInDirectory()
    {
        
        // Clear test file
        if (\file_exists('/tmp/dir/test.txt')) {
            \unlink('/tmp/dir/test.txt');
        }
        
        // Create test dir when it does not exist
        if (!\is_dir('/tmp/dir')) {
            \mkdir('/tmp/dir');
        }
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // When a file is saved an instance of the file handler is returned
        $this->assertInstanceOf(
            '\ULPF\Storage\File\Handler',
            $this->instance->saveContents('Test data', 'dir/test.txt')
        );
        
    }
    
    /**
     * Save file in a not existing directory. The directory will be created
     * 
     * @covers ::setRootDirectory
     * @covers ::saveContents
     */
    public function testSaveFileInNotExistingDirectory()
    {
        
        // Clear test file
        if (\file_exists('/tmp/dir/test.txt')) {
            \unlink('/tmp/dir/test.txt');
        }
        
        // Remove test dir when it exists
        if (\is_dir('/tmp/dir')) {
            \rmdir('/tmp/dir');
        }
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // When a file is saved an instance of the file handler is returned
        $this->assertInstanceOf(
            '\ULPF\Storage\File\Handler',
            $this->instance->saveContents('Test data', 'dir/test.txt')
        );
        
    }
    
    /**
     * Saving a file above the root directory is not allowed
     * 
     * @covers ::setRootDirectory
     * @covers ::saveContents
     */
    public function testSaveFileInParentOfRoot()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Save contents into parent of root, this ought to be canonicalized
        $this->instance->saveContents(
            'Test data',
            '/../is/../the/./test/.///test.txt'
        );
        
        // File should exist with the following path
        $this->assertFileExists('/tmp/the/test/test.txt');
        
        
        
    }
    
    /**
     * Save a file without the root directory being set
     * 
     * @covers ::saveContents
     * @covers \ULPF\Storage\File\Exception
     * @expectedException Exception
     * @expectedExceptionMessage Root directory is not set
     */
    public function testSaveFileWithoutRoot()
    {
        
        // Save some contents
        $this->instance->saveContents('Test data', 'test.txt');
        
    }
    
    /**
     * Overwrite file data
     * 
     * @covers ::saveContents
     */
    public function testOverwriteFile()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Save some contents
        $this->instance->saveContents('Test data', 'test.txt');
        
        // Overwrite withs some other contents
        $this->instance->saveContents('Test', 'test.txt');
        
        // File should exist
        $this->assertFileExists('/tmp/test.txt');
        
        // Get file contents
        $contents = \file_get_contents('/tmp/test.txt', 'r');
        
        // Saved file data should match the contents of the second save
        $this->assertEquals('Test', $contents);
        
    }
    
    /**
     * Remove a file
     * 
     * @covers ::remove
     */
    public function testRemoveFile()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Save some contents
        $this->instance->saveContents('Test data', 'test.txt');
        
        // Removing a file should return an instance of the file handler
        $this->assertInstanceOf(
            '\ULPF\Storage\File\Handler',
            $this->instance->remove('test.txt')
        );
        
        // File should be removed
        $this->assertFileNotExists('/tmp/test.txt');
        
    }
    
    /**
     * Try to remove a not existing file
     * 
     * @covers ::remove
     * @covers \ULPF\Storage\File\Exception
     * @expectedException \ULPF\Storage\File\Exception
     * @expectedExceptionMessage File does not exist 
     */
    public function testRemoveNotExistingFile()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Remove not existing file
        $this->instance->remove('idonotexist.txt');
        
    }
    
    /**
     * Try to remove a not existing file
     * 
     * @covers ::remove
     * @covers \ULPF\Storage\File\Exception
     * @expectedException \ULPF\Storage\File\Exception
     * @expectedExceptionMessage File does not exist 
     */
    public function testRemoveFileInParentOfRoot()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Remove not existing file
        $this->instance->remove('../tmp/remove.txt');
        
    }
    
    /**
     * Remove a directory
     * 
     * @covers ::remove
     */
    public function testRemoveDirectory()
    {
        
        // Create test dir
        if (!is_dir('/tmp/test')) {
            \mkdir('/tmp/test');
        }
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Remove directory
        $this->instance->remove('test');
        
        // Directory should be removed
        $this->assertFileNotExists('/tmp/test');
        
    }
    
    /**
     * Remove a directory with contents
     * 
     * @covers ::remove
     */
    public function testRemoveDirectoryWithContents()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Save a file
        $this->instance->saveContents('test', 'test/sub/test.txt');
        
        // Remove directory with contents
        $this->instance->remove('test');
        
        // Directory should be removed
        $this->assertFileNotExists('/tmp/test');
        
    }
    
    /**
     * Remove a directory which does not exist
     * 
     * @covers ::remove
     * @covers \ULPF\Storage\File\Exception
     * @expectedException \ULPF\Storage\File\Exception
     * @expectedExceptionMessage File does not exist
     */
    public function testRemoveNotExistingDirectory()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Remove directory with contents
        $this->instance->remove('test');
        
    }
    
}