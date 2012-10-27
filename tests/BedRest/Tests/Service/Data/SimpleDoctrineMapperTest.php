<?php

namespace BedRest\Tests\Service\Data;

use BedRest\Service\Data\DataMapper;
use BedRest\Service\Data\SimpleDoctrineMapper;
use BedRest\Service\ServiceManager;
use BedRest\Tests\BaseTestCase;

/**
 * SimpleDoctrineMapperTest
 *
 * Tests BedRest\DataMapper\SimpleDoctrineMapper.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class SimpleDoctrineMapperTest extends BaseTestCase
{
    /**
     * DataMapper under test.
     * @var \BedRest\Service\Data\SimpleDoctrineMapper
     */
    protected $mapper;

    /**
     * A set of test data cast in the intended data types.
     * @return array
     */
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

    /**
     * A set of test data, corresponding to that of getTestData(), but with each field
     * cast to a different type (if possible).
     * @return array
     */
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
     * Bootstrapping set up phase for each test.
     */
    protected function setUp()
    {
        $this->mapper = new SimpleDoctrineMapper(
            self::getServiceConfiguration(),
            new ServiceManager(self::getServiceConfiguration())
        );
    }

    /**
     * Test the class fulfills all contracts demanded of it.
     */
    public function testClassContract()
    {
        $this->assertTrue($this->mapper instanceof DataMapper);
    }

    /**
     * Test basic field mapping to ensure data is correctly mapped over.
     */
    public function testBasicFieldMapping()
    {
        $resource = new \BedRest\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();

        $this->mapper->map($resource, $data);

        foreach ($data as $property => $value) {
            $this->assertEquals($value, $resource->{$property});
        }
    }

    /**
     * Test basic field reverse mapping to ensure data is correctly reversed.
     */
    public function testBasicFieldReverse()
    {
        $resource = new \BedRest\TestFixtures\Models\Company\Employee();
        $data = $this->getTestData();

        $this->mapper->map($resource, $data);

        $reversed = $this->mapper->reverse($resource);

        foreach ($reversed as $property => $value) {
            $this->assertEquals($data[$property], $value);
        }
    }

    /**
     * Test basic field mapping with a dataset including fields which are not
     * present in the target resource. Data should be mapped as expected, with
     * non-existant fields not throwing any errors.
     */
    public function testBasicFieldMappingWithNonExistentFields()
    {
        $resource = new \BedRest\TestFixtures\Models\Company\Employee();

        $data = $this->getTestData();
        $nonExistentFields = array(
            'dummyField' => 'dummyValue'
        );

        $this->mapper->map($resource, array_merge($data, $nonExistentFields));

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
        $resource = new \BedRest\TestFixtures\Models\Company\Employee();

        $data = $this->getTestData();
        $uncastData = $this->getUncastTestData();

        $this->mapper->map($resource, $uncastData);

        foreach ($data as $property => $value) {
            $this->assertEquals(gettype($value), gettype($resource->{$property}));
            $this->assertEquals($value, $resource->{$property});
        }
    }
}
