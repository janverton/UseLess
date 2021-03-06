<?php

// Define namespace
namespace ULPFTest\Storage\File;

// Includes
require_once __DIR__ . '/../../../ULPF/Storage/File/Handler.php';
require_once __DIR__ . '/../../../ULPF/Storage/File/Exception.php';

/**
 * File storage test class
 * 
 * @uses \ULPF\Storage\File\Handler::getRealFilePath
 * 
 * @coversDefaultClass \ULPF\Storage\File\Handler
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
     * Create a directory
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::fileExists
     * @uses \ULPF\Storage\File\Handler::remove
     * 
     * @covers ::createDirectory
     */
    public function testCreateDirectory()
    {
        
        // Define test directory name
        $dirName = 'testdir';
        
        // Set directory to /tmp
        $this->instance->setRootDirectory('/tmp');
        
        // Clear test directory
        if ($this->instance->fileExists($dirName)) {
            
            // Remove created directory
            $this->instance->remove($dirName);
        
            
        }
        
        // Create new directory
        $this->instance->createDirectory($dirName);
        
        // Assert directory is created
        $this->assertTrue($this->instance->fileExists($dirName));
        
    }
    
    /**
     * Save a file
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::fileExists
     * @uses \ULPF\Storage\File\Handler::remove
     * @uses \ULPF\Storage\File\Handler::getFileContents
     * 
     * @covers ::saveContents
     */
    public function testSaveFile()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Clear test file
        if ($this->instance->fileExists('test.txt')) {
            $this->instance->remove('test.txt');
        }
        
        // When a file is saved an instance of the file handler is returned
        $this->assertInstanceOf(
            '\ULPF\Storage\File\Handler',
            $this->instance->saveContents('Test data', 'test.txt')
        );
        
        // File should exist
        $this->assertTrue($this->instance->fileExists('test.txt'));
        
        // Get file contents
        $contents = $this->instance->getFileContents('test.txt');
        
        // Saved file data should match
        $this->assertEquals('Test data', $contents);
        
    }
    
    /**
     * Save file in a directory
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::fileExists
     * @uses \ULPF\Storage\File\Handler::remove
     * 
     * @covers ::saveContents
     */
    public function testSaveFileInDirectory()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Clear test file
        if ($this->instance->fileExists('dir/test.txt')) {
            $this->instance->remove('dir/test.txt');
        }
        
        // Create test dir when it does not exist
        if (!$this->instance->fileExists('dir')) {
            $this->instance->createDirectory('dir');
        }
        
        // When a file is saved an instance of the file handler is returned
        $this->assertInstanceOf(
            '\ULPF\Storage\File\Handler',
            $this->instance->saveContents('Test data', 'dir/test.txt')
        );
        
    }
    
    /**
     * Save file in a not existing directory. The directory will be created
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::fileExists
     * @uses \ULPF\Storage\File\Handler::remove
     * 
     * @covers ::saveContents
     */
    public function testSaveFileInNotExistingDirectory()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Clear test dir/file
        if ($this->instance->fileExists('dir/test.txt')) {
            $this->instance->remove('dir');
        }
        
        // When a file is saved an instance of the file handler is returned
        $this->assertInstanceOf(
            '\ULPF\Storage\File\Handler',
            $this->instance->saveContents('Test data', 'dir/test.txt')
        );
        
    }
    
    /**
     * Saving a file above the root directory is not allowed
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::fileExists
     * @uses \ULPF\Storage\File\Handler::remove
     * 
     * @covers ::saveContents
     */
    public function testSaveFileInParentOfRoot()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Clear test dir
        if ($this->instance->fileExists('the')) {
            $this->instance->remove('the');
        }
        
        // Save contents into parent of root, this ought to be canonicalized
        $this->instance->saveContents(
            'Test data',
            '/../is/../the/./test/.///test.txt'
        );
        
        // File should exist with the following path
        $this->assertTrue(
            $this->instance->fileExists('the/test/test.txt')
        );
        
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
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::fileExists
     * @uses \ULPF\Storage\File\Handler::getFileContents
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
        $this->assertTrue(
            $this->instance->fileExists('test.txt')
        );
        
        // Get file contents
        $contents = $this->instance->getFileContents('test.txt');
        
        // Saved file data should match the contents of the second save
        $this->assertEquals('Test', $contents);
        
    }
    
    /**
     * Remove a file
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::saveContents
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
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
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
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
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
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
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
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::saveContents
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
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
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
    
    /**
     * Test for file existance
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::saveContents
     * @uses \ULPF\Storage\File\Handler::remove
     * 
     * @covers ::fileExists
     * @covers ::getRealFilePath
     */
    public function testFileExists()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Create a file
        $this->instance->saveContents('I exist', 'exists.txt');
        
        // Assert file exists
        $this->assertTrue($this->instance->fileExists('exists.txt'));
        
        // Remove file
        $this->instance->remove('exists.txt');
        
        // File does not exist
        $this->assertFalse($this->instance->fileExists('exists.txt'));
        
    }
    
    /**
     * Get contents of a file located in this file storage component
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * @uses \ULPF\Storage\File\Handler::saveContents
     * 
     * @covers ::getFileContents
     */
    public function testGetFileContents()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Create a file
        $this->instance->saveContents('Get my contents', 'exists.txt');
        
        // Assert content is returned
        $this->assertEquals(
            'Get my contents',
            $this->instance->getFileContents('exists.txt')
        );
        
    }
    
    /**
     * Try to get contents from a not existing file
     * 
     * @uses \ULPF\Storage\File\Handler::setRootDirectory
     * 
     * @covers ::getFileContents
     * @expectedException \ULPF\Storage\File\Exception
     * @expectedExceptionMessage File does not exist
     */
    public function testGetFileContentsFromNotExistingFile()
    {
        
        // Set root directory
        $this->instance->setRootDirectory('/tmp');
        
        // Get contents of a not existing file
        $this->instance->getFileContents('idonotexists.txt');
        
    }
    
}