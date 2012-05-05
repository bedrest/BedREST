<?php

namespace BedREST\Tests\DataMapper;

use BedREST\DataMapper\AbstractMapper,
    BedREST\DataMapper\ArrayMapper,
    BedREST\Tests\BaseTestCase;

/**
 * BedREST\Tests\DataMapper\ArrayMapperTest
 * 
 * Tests BedREST\DataMapper\ArrayMapper
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ArrayMapperTest extends BaseTestCase
{
    protected function getTestData()
    {
        return array(
            'id' => 1,
            'name' => 'John Doe',
            'department' => 'Administration',
            'ssn' => '123-456',
            'dob' => new \DateTime()
        );
    }
    
    public function testClassContract()
    {
        $mapper = new ArrayMapper();
        
        $this->assertTrue($mapper instanceof AbstractMapper);
    }
    
    public function testInsantiationWithoutEntityManagerThrowsException()
    {
        $this->setExpectedException('BedREST\DataMapper\DataMappingException');
        
        $mapper = new ArrayMapper();
        
        $resource = new \BedREST\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();
        
        $mapper->map($resource, $data);
    }
    
    public function testBasicMapping()
    {
        $mapper = new ArrayMapper(array('entityManager' => self::getEntityManager()));
        
        $resource = new \BedREST\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();
        
        $mapper->map($resource, $data);
        
        foreach ($data as $property => $value) {
            $this->assertEquals($value, $resource->{$property});
        }
    }
    
    public function testBasicReverse()
    {
        $mapper = new ArrayMapper(array('entityManager' => self::getEntityManager()));
        
        $resource = new \BedREST\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();
        
        $mapper->map($resource, $data);
        
        foreach ($mapper->reverse($resource) as $property => $value) {
            $this->assertEquals($value, $data[$property]);
        }
    }
}
