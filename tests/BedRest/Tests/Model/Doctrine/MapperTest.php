<?php

namespace BedRest\Tests\Model\Doctrine;

use BedRest\Model\Doctrine\Mapper;
use BedRest\Service\Data\Mapper as MapperInterface;
use BedRest\Service\ServiceManager;
use BedRest\TestFixtures\Models\Company\Asset as AssetEntity;
use BedRest\TestFixtures\Models\Company\Department as DepartmentEntity;
use BedRest\TestFixtures\Models\Company\Employee as EmployeeEntity;
use BedRest\Tests\RequiresModelTestCase;

/**
 * MapperTest
 *
 * Tests BedRest\DataMapper\Mapper.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class MapperTest extends RequiresModelTestCase
{
    /**
     * DataMapper under test.
     *
     * @var \BedRest\Model\Doctrine\Mapper
     */
    protected $mapper;

    /**
     * Mock entity store.
     *
     * @var array
     */
    protected $mockEntities = array();

    /**
     * Bootstrapping set up phase for each test.
     */
    protected function setUp()
    {
        $this->mapper = new Mapper(
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
        $employee1 = new EmployeeEntity();
        $employee1->id = 1;
        $employee1->name = 'Jane Doe';

        $department1 = new DepartmentEntity();
        $department1->id = 1;
        $department1->name = 'Department #1';

        $asset1 = new AssetEntity();
        $asset1->id = 1;

        $asset2 = new AssetEntity();
        $asset2->id = 2;

        $asset3 = new AssetEntity();
        $asset3->id = 3;

        $employee1->Department = $department1;
        $department1->Employees = array($employee1);

        $employee1->Assets = array($asset1, $asset2, $asset3);
        $asset1->LoanedTo = $employee1;
        $asset2->LoanedTo = $employee1;
        $asset3->LoanedTo = $employee1;

        $this->registerMockEntity('Employee', 1, $employee1);
        $this->registerMockEntity('Department', 1, $department1);
        $this->registerMockEntity('Asset', 1, $asset1);
        $this->registerMockEntity('Asset', 2, $asset2);
        $this->registerMockEntity('Asset', 3, $asset3);
    }

    /**
     * Returns a mock entity instance.
     *
     * @param  string  $entity
     * @param  integer $id
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
     *
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
     * Data provider for testing casting of field data during mapping.
     *
     * @return array
     */
    public function sampleFieldData()
    {
        $dob = new \DateTime('1st May 2012 00:00:00 +00:00');

        return array(
            array('id', 2, 2),
            array('id', 2, '2'),
            array('name', 'John Doe', 'John Doe'),
            array('name', '789', 789),
            array('dob', $dob, $dob),
            array('dob', $dob, $dob->format(\DateTime::ISO8601)),
            array('dob', $dob, $dob->getTimestamp()),
            array('dob', $dob, (array) $dob),
            array('dob', null, null),
            array('active', true, true),
            array('active', true, '1'),
            array('active', false, 0),
            array('salary', 50000.00, 50000.00),
            array('salary', 50000.00, '50000.00'),
            array('salary', 50000.00, 50000)
        );
    }

    /**
     * Data provider for testing association mapping.
     *
     * @return array
     */
    public function sampleAssociationData()
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

    public function sampleAssociationReverseData()
    {
        $this->createMockEntities();

        return array(
            array('Employee', 1, $this->getMockEntity('Employee', 1)),
            array('Department', 1, $this->getMockEntity('Department', 1)),
            array('Asset', 1, $this->getMockEntity('Asset', 1))
        );
    }

    /**
     * Test the class fulfills all contracts demanded of it.
     */
    public function testClassContract()
    {
        $this->assertTrue($this->mapper instanceof MapperInterface);
    }

    /**
     * Tests field mapping.
     *
     * @dataProvider sampleFieldData
     *
     * @param string $field
     * @param mixed  $expected
     * @param mixed  $provided
     */
    public function testFieldMapping($field, $expected, $provided)
    {
        $resource = new EmployeeEntity();

        $data = array(
            $field => $provided
        );
        $this->mapper->map($resource, $data);

        // check the cast was correct
        $cast = $resource->{$field};

        $this->assertEquals(gettype($expected), gettype($cast));
        $this->assertEquals($expected, $cast);
    }

    /**
     * Tests field reverse mapping.
     *
     * @dataProvider sampleFieldData
     *
     * @param string $field
     * @param mixed  $value
     */
    public function testFieldReverse($field, $value)
    {
        $resource = new EmployeeEntity();

        $data = array(
            $field => $value
        );
        $this->mapper->map($resource, $data);

        // data in the reverse-mapped resource should be equal to the original data
        $reversed = $this->mapper->reverse($resource, 1);

        $this->assertEquals(gettype($value), gettype($reversed[$field]));
        $this->assertEquals($value, $reversed[$field]);
    }

    /**
     * Tests association mapping, using various relationship types.
     *
     * @dataProvider sampleAssociationData
     *
     * @param string $field
     * @param mixed  $expected
     * @param mixed  $provided
     */
    public function testAssociationMapping($field, $expected, $provided)
    {
        $resource = new EmployeeEntity();

        $data = array(
            $field => $provided
        );
        $this->mapper->map($resource, $data);

        // check the cast was correct
        $cast = $resource->{$field};

        $this->assertEquals(gettype($expected), gettype($cast));
        $this->assertEquals($expected, $cast);
    }

    /**
     * Tests reverse association mapping.
     *
     * @dataProvider sampleAssociationReverseData
     *
     * @param string $entity
     * @param mixed  $id
     */
    public function testAssociationReverse($entity, $id, $resource)
    {
        $resourceClass = get_class($resource);
        $newResource = new $resourceClass;

        $data = $this->mapper->reverse($resource, 1);
        $this->mapper->map($newResource, $data);

        $this->assertEquals($resource, $newResource);
    }
}
