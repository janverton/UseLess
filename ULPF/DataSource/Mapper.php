<?php

namespace ULPF\DataSource;

/**
 * Mapper class is responsible for loading and saving entity data from and
 * to the given database
 */
class Mapper
{
    
    /**
     * Database client to map to
     * 
     * @var Database\Client 
     */
    protected $mysqlClient;
    
    /**
     * Table to map to
     * 
     * @var string
     */
    protected $table;
    
    /**
     * Construct mapper 
     * 
     * @param Database\Client $mysqlClient
     * @param string $table
     */
    public function __construct(Database\Client $mysqlClient, $table) {
        $this->table = $table;
        $this->mysqlClient = $mysqlClient;
    }
    
    /**
     * Load an entity by its primary key
     * 
     * @param array $primaryKey
     * @return Entity
     */
    public function load(array $primaryKey)
    {
        
        // Get primary key fields
        $fields = array_keys($primaryKey);
        
        // Get where clause fields
        $whereClause = array();
        foreach ($fields as $field) {
            $whereClause[] = '`' . $field . '` = :' . $field;
        }
        
        // Create query to load data by its primary key
        $query = 'SELECT * FROM `' . $this->table . '` WHERE '
            . \implode(' AND ', $whereClause);
        
        // Load data, which must be exactly one or an exception will be thrown
        $result = $this->mysqlClient->fetchOne($query, $primaryKey);
        
        // Load the entity
        $entity = new Entity();
        $entity->setProperties($result);
        $entity->setPrimaryKeyFields($fields);
        
        // Return the created entity
        return $entity;
        
    }
    
    /**
     * Save the given entity to the database
     * 
     * @param Entity $entity Entity to save
     * @return Mapper Implements fluent interface
     */
    public function save(Entity $entity)
    {
        
        // Get entity changes
        $changes = $entity->getModifiedProperties();
        
        // Assert changes are made
        if (!\count($changes)) {
            // Entity did not change
            
            // Do nothing
            return $this;
            
        }
        
        // Get primary key fields when defined
        $primaryKeyFields = $entity->getPrimaryKeyFields();
        
        // The parameters used in the query
        $parameters = array();
        
        // Check whether this is a new or existing entity based on the
        // availability of primary key fields
        if (!$primaryKeyFields) {
            // New Entity

            // Generate the set clause of the query
            $setClause = $this->getClause($changes, $parameters, $entity);
            
            // Create the update query
            $query = 'INSERT INTO `' . $this->table . '`'
                . ' SET ' . \implode(',', $setClause);
            
            // Execute insert query
            $this->mysqlClient->query($query, $parameters);
            
        } else {
            // Existing entity
            
            // Generate the set clause of the query
            $setClause = $this->getClause($changes, $parameters, $entity);
            
            // Generate the where clause of the query
            $whereClause = $this->getClause($primaryKeyFields, $parameters, $entity);
            
            // Create the update query
            $query = 'UPDATE `' . $this->table . '`'
                . ' SET ' . \implode(',', $setClause)
                . ' WHERE ' . \implode(' AND ', $whereClause);
            
            // Execute update query
            $this->mysqlClient->query($query, $parameters);
            
        }
        
        // Implement fluent interface
        return $this;
        
    }
    
    /**
     * Delete the given entity from the database
     * 
     * @param Entity $entity Entity to be deleted
     * @return Mapper Implements fluent interface
     */
    public function delete(Entity $entity)
    {
        
        // Define parameters
        $parameters = [];
        
        // Generate the where clause of the query
        $whereClause = $this->getClause(
            $entity->getPrimaryKeyFields(),
            $parameters,
            $entity
        );

        // Create the update query
        $query = 'DELETE FROM `' . $this->table . '`'
            . ' WHERE ' . \implode(' AND ', $whereClause);
        
        // Execute delete query
        $this->mysqlClient->query($query, $parameters);
        
        // Implement fluent interface
        return $this;
        
    }
    
    /**
     * Generate query clause
     * 
     * @param array  $fields     Fields to generate clause from
     * @param array  $parameters Resulting parameters set (by reference)
     * @param Entity $entity     Entity to populate parameters with
     * @return string
     */
    protected function getClause($fields, &$parameters, Entity $entity)
    {
        
        // The resulting clause of the query
        $clause = array();
        
        // Iterate the given fields
        foreach ($fields as $field) {
            
            // Add field to the clause
            $clause[] = '`' . $field . '` = :' . $field;
            
            // Add parameter based on the entity field value
            $parameters[$field] = $entity->$field;
            
        }
        
        // Return the generated clause
        return $clause;
        
    }
    
}