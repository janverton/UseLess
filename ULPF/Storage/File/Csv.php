<?php

// Define namespace
namespace ULPF\Storage\File;

/**
 * CSV file parser
 */
class Csv
{

    /**
     * CSV file handle
     *
     * @var resource
     */
    protected $fileHandle = false;

    /**
     * File handler
     *
     * @var Handler File Handler
     */
    protected $fileHandler = false;
    
    /**
     * CSV delimiter
     *
     * @var string
     */
    protected $delimiter = ',';

    /**
     * CSV data container
     *
     * @var array
     */
    protected $csvData = array();

    /**
     * Set FileHandler for storing and retreiving files 
     * 
     * @param Handler $fileHandler FileHandler
     * @return Csv Implements fluent interface
     */
    public function setFileHandler(Handler $fileHandler)
    {
        
        // Set FileHandler
        $this->fileHandler = $fileHandler;
        
        // Implement fluent interface
        return $this;
        
    }
    
    /**
     * Set CSV delimiter
     *
     * @return Csv Implements fluent interface
     */
    public function setDelimiter($delimiter)
    {

        // Set delimiter
        $this->delimiter = $delimiter;

        // Implement fluent interface
        return $this;

    }

    /**
     * Read CSV file and return its contents as an array
     *
     * @return array Parsed CSV data
     */
    public function readCsv($file)
    {

        // Set file
        $this->openCsvFile($file)
        
            // Parse csv file
            ->parseCsvLines()
            
            // Close csv file
            ->closeCsvFile();

        // Return CSV data
        return $this->csvData;

    }

    /**
     * Get file handler
     *
     * @return Handler FileHandler
     */
    protected function getFileHandler()
    {

        // Check whether file handler is set
        if (!$this->fileHandler) {
            // File handler is not set

            // Parser cannot continue without a file handler
            throw new Exception('File Handler not set');

        }

        // Return file handler
        return $this->fileHandler;

    }

     /**
     * Open CSV file to parse
     *
     * @param string $file File path to the CSV
     * @returns Csv Implements fluent interface
     */
    protected function openCsvFile($file)
    {

        // Auto detect line endings
        \ini_set('auto_detect_line_endings', true);

        // Open file handle
        $fileHandle = $this->getFileHandler()->getFileHandle($file);

        // Set file handle
        $this->fileHandle = $fileHandle;

        // Implement fluent interface
        return $this;

    }
    
    /**
     * Close csv file
     * 
     * @return Csv Implements fluent interface
     */
    protected function closeCsvFile()
    {
        
        // Close open file handle
        \fclose($this->fileHandle);

        // Auto detect line endings
        \ini_set('auto_detect_line_endings', false);
        
        // Implement fluent interface
        return $this;
        
    }

    /**
     * Parse all Csv lines
     * 
     * @return Csv Implements fluent interface
     */
    protected function parseCsvLines()
    {
        
        // Define empty data set
        $this->csvData = array();

        // Parse CSV lines while available
        do {

            // Get next CSV line
            $data = $this->parseCsvLine();

            // Check whether any data is found
            if ($data) {
                // Data is available

                // Add parsed CSV line
                $this->csvData[] = $data;

            }

        } while (false !== $data);
        
        // Implement fluent interface
        return $this;
        
    }

    /**
     * Parse the next CSV line and return its contents as 
     * an array
     *
     * @note max 1000 characters per line are read
     *
     * @return mixed Line data array on success or false on
     *  failure
     */
    protected function parseCsvLine()
    {

        // Get line data
        $data = \fgetcsv($this->fileHandle, 1000, $this->delimiter);

        // Return data
        return $data;

    }

}
