<?php

namespace ULPF\DataSource;

class Entity
{
    
    protected $properties = array();
    protected $modified = array();
    protected $primaryKeyFields = array();
    
    public function getModifiedProperties()
    {
        return \array_keys($this->modified);
    }
    
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
        $this->modified = array();
    }
    
    public function getProperties()
    {
        return $this->properties;
    }
    
    public function setPrimaryKeyFields(array $primaryKeyFields)
    {
        $this->primaryKeyFields = $primaryKeyFields;
    }
    
    public function getPrimaryKeyFields()
    {
        return $this->primaryKeyFields;
    }
    
    public function __get($name)
    {
        return $this->properties[$name];
    }
    
    public function __set($name, $value)
    {
        $this->modified[$name] = true;
        $this->properties[$name] = $value;
    }
}