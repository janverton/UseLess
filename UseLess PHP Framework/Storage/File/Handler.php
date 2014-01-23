<?php

// Define namespace
namespace ULPF\Storage\File;

/**
 * File storage component
 */
class Handler
{
    
    /**
     * Root directory where files are stored
     * 
     * @var string
     */
    protected $root = null;
    
    /**
     * Set root directory where files or directories will be stored
     * 
     * @param string $rootDirectory Root directory path
     * @return Handler Implements fluent interface
     */
    public function setRootDirectory($rootDirectory)
    {
        
        // Check whether directory exists
        if (!\is_dir($rootDirectory)) {
            // Directory does not exist

            // Throw an exception
            throw new \ULPF\Storage\File\Exception(
                'Root directory ' . $rootDirectory . ' does not exist'
            );
            
        }
        
        // Check whether the directory is writeable
        if (!\is_writable($rootDirectory)) {
            // Directory is not writeable
            
            // Throw an exception
            throw new \ULPF\Storage\File\Exception(
                'Root directory ' . $rootDirectory . ' is not writeable'
            );
            
        }
        
        // Set directory
        $this->root = $rootDirectory;
        
        // Implement fluent interface
        return $this;
        
    }
    
    /**
     * Save contents to a file
     * 
     * @param string $content  File content to save
     * @param string $fileName File name
     * @return Handler Implements fluent interface
     */
    public function saveContents($content, $fileName)
    {
        
        // Get file path
        $file = $this->getRootDirectory()
            . $this->canonicalizePath($fileName);
        
        // Make sure the directory exists
        $this->assertDirectory($file);
        
        // Check whether file exists
        if (!\file_exists($file)) {
            // File does not exist yet
            
            // Create file
            \touch($file);
            
        }
        
        // Get file handle
        $fileHandle = \fopen($file, 'w');
        
        // Write content to file
        \fwrite($fileHandle, $content);
        
        // Close file handle
        \fclose($fileHandle);
        
        // Implement fluent interface
        return $this;
        
    }
    
    /**
     * Remove a file
     * 
     * @see http://stackoverflow.com/questions/1407338/a-recursive-remove-directory-function-for-php
     * 
     * @param string $fileName File to remove
     * @return Handler
     */
    public function remove($fileName)
    {
        
        // Get file path
        $file = $this->getRootDirectory() . $this->canonicalizePath($fileName);
        
        // Check whether file exists
        if (!\file_exists($file)) {
            // File does not exist
            
            // Throw an exception
            throw new Exception('File does not exist');
            
        }
        
        // Check whether this is a file or directory
        if (\is_file($file)) {
            // Resource is a file
            
            // Remove file
            \unlink($file);
            
        } else if (\is_dir($file)) {
            // Resource is a directory
            
            // Create recursive iterator
            $recursiveIterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $file, \FilesystemIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            
            // Iterate file/dir paths
            foreach($recursiveIterator as $path) {
                
                // Check whether path is a file or directory
                if ($path->isFile()) {
                    // Path is a file
                    
                    // Remove file
                    \unlink($path->getPathname());
                    
                } else {
                    // Path is a directory
                    
                    // Remove directory
                    \rmdir($path->getPathname());
                }
            }
            
            // Remove directory
            \rmdir($file);
            
        }
        
        // Implement fluent interface
        return $this;
        
    }
    
    /**
     * Get root directory
     * 
     * @return string Root directory path
     * @throws Exception
     */
    protected function getRootDirectory()
    {
        
        // Check whether root directory is set
        if (!isset($this->root)) {
            // Root not set
            
            // Throw an exception
            throw new Exception('Root directory is not set');
            
        }
        
        // Return root directory
        return $this->root . \DIRECTORY_SEPARATOR;
        
    }
    
    /**
     * Assert the path to the given directory exists
     * 
     * @param string $path Directory path
     * @return boolean
     */
    protected function assertDirectory($path)
    {
        
        // Get directory path
        $directoryPath = \substr(
            $path,
            0,
            \strrpos($path, \DIRECTORY_SEPARATOR, -2)
        );
        
        // Check whether the directory exists
        if (\is_dir($directoryPath)) {
            // Directory exists
            
            // Return
            return true;
        }
        
        // Assert parent directory exists
        $this->assertDirectory($directoryPath);

        // Create directory
        return \mkdir($directoryPath);
         
    }
    
    /**
     * Canonicalize a file path
     * 
     * @param string $path File path to be canonicalized
     * @return string Filepath
     */
    protected function canonicalizePath($path)
    {
        
        // Replace forward and backward slashes with the appropriate
        // directory separator
        $path = \str_replace(array('/', '\\'), \DIRECTORY_SEPARATOR, $path);
        
        // Break file path in parts, remove parts without length
        $parts = \array_filter(
            \explode(\DIRECTORY_SEPARATOR, $path), 'strlen'
        );
        
        // Define absolute path parts
        $absolutes = array();
        
        // Iterate parts
        foreach ($parts as $part) {
            
            // Check whether part is a single dot
            if ('.' === $part){
                // Part is a single dot
                
                // Skip single dot
                continue;
                
            }
            
            // Check whether part equals "directory up" 
            if ('..' === $part) {
                // Directory up
                
                // Pop last part of the absolutes array
                \array_pop($absolutes);
                
            } else {
                // Regular path part
                
                // Add part
                $absolutes[] = $part;
                
            }
            
        }
        
        // Prepend root directory and complete filtered relatives to absolute
        // path
        $filePath = \implode(\DIRECTORY_SEPARATOR, $absolutes);
    
        // Return path
        return $filePath;
        
    }
    
}