<?php

namespace BedRest\Tests\DataMapper;

use BedRest\DataMapper\AbstractMapper,
    BedRest\DataMapper\JsonMapper,
    BedRest\Tests\BaseTestCase;

/**
 * BedRest\Tests\DataMapper\JsonMapperTest
 *
 * Tests BedRest\DataMapper\JsonMapper.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class JsonMapperTest extends BaseTestCase
{
    protected function getTestData()
    {
        $dob = new \DateTime('1st May 2012 00:00:00 +00:00');

        return array(
            'id' => 1,
            'name' => 'John Doe',
            'department' => 'Administration',
            'ssn' => '123456',
            'dob' => $dob->format(\DateTime::ISO8601),
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

    protected function getExpectedTestData()
    {
        $dob = new \DateTime('1st May 2012 00:00:00 +00:00');

        return array(
            'id' => 1,
            'name' => 'John Doe',
            'department' => 'Administration',
            'ssn' => '123456',
            'dob' => $dob,
            'active' => true
        );
    }

    /**
     * Test the class fulfills all contracts demanded of it.
     */
    public function testClassContract()
    {
        $mapper = new JsonMapper();

        $this->assertTrue($mapper instanceof AbstractMapper);
    }

    /**
     * Test that instantiation without an entity manager will fail.
     */
    public function testInsantiationWithoutEntityManagerThrowsException()
    {
        $this->setExpectedException('BedRest\DataMapper\DataMappingException');

        $mapper = new JsonMapper();

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();

        $mapper->map($resource, json_encode($data));
    }

    public function testInvalidJsonThrowsException()
    {
        $this->setExpectedException('BedRest\DataMapper\DataMappingException');

        $mapper = new JsonMapper();

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();

        $mapper->map($resource, '{not valid JSON');
    }

    /**
     * Test basic field mapping to ensure data is correctly mapped over.
     */
    public function testBasicFieldMapping()
    {
        $mapper = new JsonMapper(self::getConfiguration());

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();

        $mapper->map($resource, json_encode($data));

        foreach ($this->getExpectedTestData() as $property => $value) {
            $this->assertEquals($value, $resource->{$property});
        }
    }

    /**
     * Test basic field reverse mapping to ensure data is correctly reversed.
     */
    public function testBasicFieldReverse()
    {
        $mapper = new JsonMapper(self::getConfiguration());

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();

        $mapper->map($resource, json_encode($data));

        $reverse = json_decode($mapper->reverse($resource));

        foreach ($reverse as $property => $value) {
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
        $mapper = new JsonMapper(self::getConfiguration());

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();

        $data = $this->getTestData();
        $nonExistentFields = array(
            'dummyField' => 'dummyValue'
        );

        $mapper->map($resource, json_encode(array_merge($data, $nonExistentFields)));

        foreach ($this->getExpectedTestData() as $property => $value) {
            $this->assertEquals($value, $resource->{$property});
        }
    }

    /**
     * Tests basic field mapping with a dataset which requires casting and then
     * checks the resultant casting was successful.
     */
    public function testBasicFieldMappingWithCasting()
    {
        $mapper = new JsonMapper(self::getConfiguration());

        $resource = new \BedRest\TestFixtures\Models\Company\Employee();

        $uncastData = $this->getUncastTestData();

        $mapper->map($resource, json_encode($uncastData));

        foreach ($this->getExpectedTestData() as $property => $value) {
            $this->assertEquals(gettype($value), gettype($resource->{$property}));
            $this->assertEquals($value, $resource->{$property});
        }
    }
}

