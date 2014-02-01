<?php

// Define namespace
namespace ULPF\Bootstrap;

/**
 * Autoload class
 * 
 * This class enables the autoloading of classes. Loading is done by directory 
 * structure. Make sure custom autoloads are defined after this autoloader
 * is initialized because it will clear your current autoloads.
 * 
 * @example $class = new \Folder\Example(); // Loads the class 'Example' from
 *  the 'Folder' directory.
 * 
 * @see      http://www.php.net/manual/en/function.spl-autoload.php
 */
class Autoload
{
    
    /**
     * Root of autoload directory
     * 
     * @var string
     */
    protected $rootDirectory = '';
    
    /**
     * Instantiate autoloader
     * 
     * @param string $rootDirectory Root directory where the classes will be 
     *  loaded from
     * 
     * @return void
     * @throws Exception
     */
    public function __construct($rootDirectory)
    {
        
        // Make sure the given autoload directory exists
        if (!\is_dir($rootDirectory)) {
            // Directory does not exist
            
            // Throw an Exception
            throw new Exception(
                'Autoload directory does noet exist: ' . $rootDirectory
            );
            
        }
        
        // Set the root directory for autoloading
        $this->rootDirectory = $rootDirectory;
        
        // Use these extensions
        \spl_autoload_extensions('.php');

        // Register include file function for autoloading
        \spl_autoload_register(array($this, 'includeFile'), true, false);
        
    }
    
    /**
     * Removes autoload function from registry
     * 
     * @return void
     */
    public function __destruct()
    {
        
        // Unregister autoload function
        \spl_autoload_unregister(array($this, 'includeFile'));
        
    }
    
    /**
     * Include class file. This is done by backslash notation
     * 
     * @param string $classname Class to load
     * 
     * @example \Controller\DummyController(); will load the class 
     *  DummyController from the Controller directory
     * 
     * @return void
     */
    protected function includeFile($classname)
    {
        
        // Create class path
        $classPath = $this->rootDirectory . \DIRECTORY_SEPARATOR .
            \str_replace(
                '\\', \DIRECTORY_SEPARATOR, $classname
            ) . '.php';
        
        // Check whether the class file exists
        if (!\file_exists($classPath)) {
            // Class file does not exist
            
            // Return to skip to next registered autoloader
            return false;
            
        }
        
        // Include class file
        include_once $classPath;
        
    }
    
}