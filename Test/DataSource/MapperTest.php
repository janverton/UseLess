<?php

namespace ULPFTest\DataSource;

require_once __DIR__ . '/../../ULPF/DataSource/Database/Client.php';
require_once __DIR__ . '/../../ULPF/DataSource/Database/Exception.php';
require_once __DIR__ . '/../../ULPF/DataSource/Entity.php';
require_once __DIR__ . '/../../ULPF/DataSource/Mapper.php';

/**
 * @uses \ULPF\DataSource\Database\Client
 * @uses \ULPF\DataSource\Database\Exception
 * @uses \ULPF\DataSource\Entity
 * 
 * @coversDefaultClass \ULPF\DataSource\Mapper
 * @covers ::<protected>
 * @covers ::__construct
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Get an entity by its primary key which exists
     * 
     * @covers ::load
     * @test
     */
    public function loadExistingEntity()
    {
        
        // Mock database client
        $userDatabase = $this->getMockBuilder(
                '\ULPF\DataSource\Database\Client'
            )
            ->disableOriginalConstructor()
            ->setMethods(array('fetchOne'))
            ->getMock();
        
        // Mock that user 1 exists and its data is returned
        $userDatabase->expects($this->once())
            ->method('fetchOne')
            ->with(
                'SELECT * FROM `users` WHERE `userId` = :userId',
                array('userId' => 1)
            )->will(
                $this->returnValue(
                    array(
                        'userId' => 1,
                        'userName' => 'John'
                    )
                )
            );
        
        // Get Mapper with database mock for user
        $mapper = new \ULPF\DataSource\Mapper($userDatabase, 'users');
        
        // Load entity
        $userEntity = $mapper->load(array('userId' => 1));
        
        // Assort an entity is returned
        $this->assertInstanceOf('\ULPF\DataSource\Entity', $userEntity);
        
        // Assert the user is loaded properly
        $this->assertSame('John', $userEntity->userName);
        
    }
    
    /**
     * Get an entity by its primary key which does not exist
     * 
     * @covers ::load
     * @expectedException \ULPF\DataSource\Database\Exception
     * @test
     */
    public function loadNotExistingEntity()
    {
        
        // Mock database client
        $userDatabase = $this->getMockBuilder(
                '\ULPF\DataSource\Database\Client'
            )
            ->disableOriginalConstructor()
            ->setMethods(array('fetchOne'))
            ->getMock();
        
        // Mock that user 1 is not found
        $userDatabase->expects($this->once())
            ->method('fetchOne')
            ->with(
                'SELECT * FROM `users` WHERE `userId` = :userId',
                array('userId' => 1)
            )->will(
                $this->throwException(new \ULPF\DataSource\Database\Exception())
            );
        
        // Get Mapper with database mock for user
        $mapper = new \ULPF\DataSource\Mapper($userDatabase, 'users');
        
        // Try to Load entity
        $mapper->load(array('userId' => 1));
        
    }
    
    /**
     * Save a changed entity by its primary key
     * 
     * @covers ::save
     * @test
     */
    public function saveExistingEntityWithChanges()
    {
        
        // Mock "existing" user entity
        $userEntity = new \ULPF\DataSource\Entity();
        $userEntity->setPrimaryKeyFields(array('userId'));
        $userEntity->setProperties(array('userId' => 2));
        
        // Modify user entity name
        $userEntity->name = 'Fritz';
        
        // Mock database client
        $userDatabase = $this->getMockBuilder(
                '\ULPF\DataSource\Database\Client'
            )
            ->disableOriginalConstructor()
            ->setMethods(array('query'))
            ->getMock();
        
        // Assume that user 2 is saved
        $userDatabase->expects($this->once())
            ->method('query')
            ->with(
                'UPDATE `users` SET `name` = :name WHERE `userId` = :userId',
                array(
                    'name' => 'Fritz',
                    'userId' => 2
                )
            );
        
        // Get Mapper with database mock for user
        $mapper = new \ULPF\DataSource\Mapper($userDatabase, 'users');
        
        // Try to save entity
        $mapper->save($userEntity);
        
    }
    
    /**
     * Save an entity which has no changes, this should prevent any queries
     * to be executed on behalf of the entity
     * 
     * @covers ::save
     * @test
     */
    public function saveExistingEntityWithoutChanges()
    {
        
        // Mock "existing" user entity which contains no changes
        $userEntity = new \ULPF\DataSource\Entity();
        
        // Mock database client
        $userDatabase = $this->getMockBuilder(
                '\ULPF\DataSource\Database\Client'
            )
            ->disableOriginalConstructor()
            ->setMethods(array('query'))
            ->getMock();
        
        // Assume the query should never be executed
        $userDatabase->expects($this->never())
            ->method('query');
        
        // Get Mapper with database mock for user
        $mapper = new \ULPF\DataSource\Mapper($userDatabase, 'users');
        
        // Try to save entity
        $mapper->save($userEntity);
        
    }
    
    /**
     * Save an entity which has no changes
     * 
     * @covers ::save
     * @test
     */
    public function saveNewEntity()
    {
        
        // Mock "existing" user entity
        $userEntity = new \ULPF\DataSource\Entity();
        
        // Modify user entity name
        $userEntity->name = 'Kola';
        
        // Mock database client
        $userDatabase = $this->getMockBuilder(
                '\ULPF\DataSource\Database\Client'
            )
            ->disableOriginalConstructor()
            ->setMethods(array('query'))
            ->getMock();
        
        // Assume that a new user is created
        $userDatabase->expects($this->once())
            ->method('query')
            ->with(
                'INSERT INTO `users` SET `name` = :name',
                array(
                    'name' => 'Kola'
                )
            );
        
        // Get Mapper with database mock for user
        $mapper = new \ULPF\DataSource\Mapper($userDatabase, 'users');
        
        // Try to save entity
        $mapper->save($userEntity);
        
    }
    
    /**
     * Delete an existing user entity
     * 
     * @covers ::delete
     * @test
     */
    public function deleteExistingEntity()
    {
        
        // Mock existing user entity
        $userEntity = new \ULPF\DataSource\Entity();
        $userEntity->setPrimaryKeyFields(array('userId'));
        $userEntity->setProperties(array('userId' => 3, 'name' => 'Obi'));
        
        // Mock database client
        $userDatabase = $this->getMockBuilder(
                '\ULPF\DataSource\Database\Client'
            )
            ->disableOriginalConstructor()
            ->setMethods(array('query'))
            ->getMock();
        
        // Assume that an existing user will be deleted
        $userDatabase->expects($this->once())
            ->method('query')
            ->with(
                'DELETE FROM `users` WHERE `userId` = :userId',
                array(
                    'userId' => 3
                )
            );
        
        // Get Mapper with database mock for user
        $mapper = new \ULPF\DataSource\Mapper($userDatabase, 'users');
        
        // Try to save entity
        $mapper->delete($userEntity);
        
    }
    
}