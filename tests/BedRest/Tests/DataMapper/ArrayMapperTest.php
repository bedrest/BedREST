<?php

namespace BedRest\Tests\DataMapper;

use BedRest\DataMapper\AbstractMapper,
    BedRest\DataMapper\ArrayMapper,
    BedRest\Tests\BaseTestCase;

/**
 * BedRest\Tests\DataMapper\ArrayMapperTest
 *
 * Tests BedRest\DataMapper\ArrayMapper.
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
            'ssn' => '123456',
            'dob' => new \DateTime('1st May 2012 00:00:00 +00:00'),
            'active' => true
        );
    }

    protected function getUncastTestData()
    {
        $dob = new \DateTime('1st May 2012 00:00:00 +00:00');

        return array(
            'id' => '1',
            'name' => 'John Doe',
            'department' => 'Administration',
            'ssn' => 123456,
            'dob' => $dob->format(\DateTime::ISO8601),
            'active' => '1'
        );
    }

    /**
     * Test the class fulfills all contracts demanded of it.
     */
    public function testClassContract()
    {
        $mapper = new ArrayMapper();

        $this->assertTrue($mapper instanceof AbstractMapper);
    }

    /**
     * Test that instantiation without an entity manager will fail.
     */
    public function testInsantiationWithoutEntityManagerThrowsException()
    {
        $this->setExpectedException('BedRest\DataMapper\DataMappingException');

        $mapper = new ArrayMapper();

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();

        $mapper->map($resource, $data);
    }

    /**
     * Test basic field mapping to ensure data is correctly mapped over.
     */
    public function testBasicFieldMapping()
    {
        $mapper = new ArrayMapper(self::getConfiguration());

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();

        $mapper->map($resource, $data);

        foreach ($data as $property => $value) {
            $this->assertEquals($value, $resource->{$property});
        }
    }

    /**
     * Test basic field reverse mapping to ensure data is correctly reversed.
     */
    public function testBasicFieldReverse()
    {
        $mapper = new ArrayMapper(self::getConfiguration());

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();

        $mapper->map($resource, $data);

        foreach ($mapper->reverse($resource) as $property => $value) {
            $this->assertEquals($value, $data[$property]);
        }
    }

    /**
     * Test basic field mapping with a dataset including fields which are not
     * present in the target resource. Data should be mapped as expected, with
     * non-existant fields not throwing any errors.
     */
    public function testBasicFieldMappingWithNonExistentFields()
    {
        $mapper = new ArrayMapper(self::getConfiguration());

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();

        $data = $this->getTestData();
        $nonExistentFields = array(
            'dummyField' => 'dummyValue'
        );

        $mapper->map($resource, array_merge($data, $nonExistentFields));

        foreach ($data as $property => $value) {
            $this->assertEquals($value, $resource->{$property});
        }
    }

    /**
     * Tests basic field mapping with a dataset which requires casting and then
     * checks the resultant casting was successful.
     */
    public function testBasicFieldMappingWithCasting()
    {
        $mapper = new ArrayMapper(self::getConfiguration());

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();

        $data = $this->getTestData();
        $uncastData = $this->getUncastTestData();

        $mapper->map($resource, $uncastData);

        foreach ($data as $property => $value) {
            $this->assertEquals(gettype($value), gettype($resource->{$property}));
            $this->assertEquals($value, $resource->{$property});
        }
    }
}

