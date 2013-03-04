<?php

namespace BedRest\Tests\Service\Data;

use BedRest\Service\Data\DataMapper;
use BedRest\Service\Data\SimpleDoctrineMapper;
use BedRest\Service\ServiceManager;
use BedRest\TestFixtures\Models\Company\Asset as AssetEntity;
use BedRest\TestFixtures\Models\Company\Department as DepartmentEntity;
use BedRest\TestFixtures\Models\Company\Employee as EmployeeEntity;
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
     * Mock entity store.
     * @var array
     */
    protected $mockEntities = array();

    /**
     * Bootstrapping set up phase for each test.
     */
    protected function setUp()
    {
        $this->mapper = new SimpleDoctrineMapper(
            self::getServiceConfiguration(),
            new ServiceManager(self::getServiceConfiguration())
        );

        $this->mapper->setEntityManager(self::getEntityManager());
    }

    /**
     * Creates the mock entities needed for this test.
     */
    protected function createMockEntities()
    {
        $department1 = new DepartmentEntity();
        $department1->id = 1;
        $department1->name = 'Department #1';
        $this->registerMockEntity('Department', 1, $department1);

        $asset1 = new AssetEntity();
        $asset1->id = 1;
        $this->registerMockEntity('Asset', 1, $asset1);

        $asset2 = new AssetEntity();
        $asset2->id = 2;
        $this->registerMockEntity('Asset', 2, $asset2);

        $asset3 = new AssetEntity();
        $asset3->id = 3;
        $this->registerMockEntity('Asset', 3, $asset3);
    }

    /**
     * Returns a mock entity instance.
     * @param string  $entity
     * @param integer $id
     * @return object
     */
    protected function getMockEntity($entity, $id)
    {
        if (!isset($this->mockEntities[$entity][$id])) {
            $this->fail("Mock entity of type '$entity' with ID $id does not exist.");
        }

        return $this->mockEntities[$entity][$id];
    }

    /**
     * Registers a mock entity instance.
     * @param string  $entity
     * @param integer $id
     * @param object  $instance
     */
    protected function registerMockEntity($entity, $id, $instance)
    {
        $this->mockEntities[$entity][$id] = $instance;

        $em = self::getEntityManager();
        $em->addMockItem(get_class($instance), $id, $instance);
    }

    /**
     * A set of test data cast in the intended data types.
     * @return array
     */
    protected function getEmployeeTestData()
    {
        return array(
            'id' => 1,
            'name' => 'John Doe',
            'ssn' => '123456',
            'dob' => new \DateTime('1st May 2012 00:00:00 +00:00'),
            'active' => true,
            'salary' => 50000.00
        );
    }

    /**
     * Data provider for testing casting of field data during mapping.
     */
    public function uncastFieldData()
    {
        $dob = new \DateTime('1st May 2012 00:00:00 +00:00');

        return array(
            array('id', 1, '1'),
            array('name', '789', 789),
            array('dob', $dob, $dob->format(\DateTime::ISO8601)),
            array('dob', $dob, $dob->getTimestamp()),
            array('dob', $dob, (array) $dob),
            array('dob', null, null),
            array('active', true, '1'),
            array('salary', 50000.00, '50000.00'),
            array('salary', 50000.00, 50000)
        );
    }

    /**
     * Data provider for testing association mapping.
     */
    public function associationData()
    {
        $this->createMockEntities();

        return array(
            array(
                'Department',
                $this->getMockEntity('Department', 1),
                1
            ),
            array(
                'Assets',
                array(
                    $this->getMockEntity('Asset', 1),
                    $this->getMockEntity('Asset', 2),
                    $this->getMockEntity('Asset', 3),
                ),
                array(
                    1,
                    2,
                    3
                )
            )
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
    public function testFieldMapping()
    {
        $resource = new EmployeeEntity();
        $data = $this->getEmployeeTestData();

        $this->mapper->map($resource, $data);

        foreach ($data as $property => $value) {
            $this->assertEquals($value, $resource->{$property});
        }
    }

    /**
     * Test basic field reverse mapping to ensure data is correctly reversed.
     */
    public function testFieldReverse()
    {
        $resource = new EmployeeEntity();
        $data = $this->getEmployeeTestData();

        $this->mapper->map($resource, $data);

        // data in the reverse-mapped resource should be equal to the original data
        $reversed = $this->mapper->reverse($resource, 1);

        foreach (array_keys($data) as $property) {
            $this->assertEquals($data[$property], $reversed[$property]);
        }
    }

    /**
     * Test field mapping with a data set including fields which are not present in
     * the target resource. Data should be mapped as normal, without the presence of
     * the non-existent fields causing any errors.
     */
    public function testFieldMappingWithNonExistentFields()
    {
        $resource = new EmployeeEntity();

        // add a non-existent field to the input data set
        $data = $this->getEmployeeTestData();
        $nonExistentFields = array(
            'dummyField' => 'dummyValue'
        );

        $this->mapper->map($resource, array_merge($data, $nonExistentFields));

        foreach ($data as $property => $value) {
            $this->assertEquals($value, $resource->{$property});
        }
    }

    /**
     * Tests field mapping with a data which requires casting.
     *
     * @dataProvider uncastFieldData
     */
    public function testFieldMappingCasting($field, $expected, $uncast)
    {
        $resource = new EmployeeEntity();

        $data = array(
            $field => $uncast
        );
        $this->mapper->map($resource, $data);

        // check the cast was correct
        $cast = $resource->{$field};

        $this->assertEquals(gettype($expected), gettype($cast));
        $this->assertEquals($expected, $cast);
    }

    /**
     * Tests association mapping, using various relationship types.
     *
     * @dataProvider associationData
     */
    public function testAssociationMapping($field, $expected, $uncast)
    {
        $resource = new EmployeeEntity();

        $data = array(
            $field => $uncast
        );
        $this->mapper->map($resource, $data);

        // check the cast was correct
        $cast = $resource->{$field};

        $this->assertEquals(gettype($expected), gettype($cast));
        $this->assertEquals($expected, $cast);
    }
}
