<?php

// Define namespace
namespace ULPF\Bootstrap;

/**
 * Configuration class
 * 
 * The Configuration class takes a file path to the .ini and extracts the 
 * settings. Values can be retrieved by the get method.
 * 
 */
class Configuration
{
    
    /**
     * Contains the parsed .ini file
     * 
     * @var array
     */
    protected $config = array();
    
    /**
     * Create a Configuration instance based on the given .ini file
     * 
     * @param string $filePath Config file path
     * @throws Exception
     */
    public function __construct($filePath)
    {
        
        // File should exist
        if (!\file_exists($filePath)) {
            
            // Throw an exception when it doesn't
            throw new Exception('Configuration file does not exist');
            
        }
            
        // Set config
        $this->config = \parse_ini_file($filePath);
        
    }
    
    /**
     * Get the value for the given property
     * 
     * @param string $name Property name to return
     * @return mixed
     */
    public function get($name)
    {
        
        // Make sure given property name exists
        if (\array_key_exists($name, $this->config)) {
            
            // Return tha value when found
            return $this->config[$name];
            
        }
        
    }
    
}