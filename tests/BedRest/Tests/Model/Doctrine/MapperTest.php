<?php

namespace BedRest\Tests\Model\Doctrine;

use BedRest\Model\Doctrine\Mapper;
use BedRest\Service\Data\Mapper as MapperInterface;
use BedRest\Service\ServiceManager;
use BedRest\TestFixtures\Models\Company\Asset as AssetEntity;
use BedRest\TestFixtures\Models\Company\Department as DepartmentEntity;
use BedRest\TestFixtures\Models\Company\Employee as EmployeeEntity;
use BedRest\Tests\FunctionalModelTestCase;
use Doctrine\ORM\EntityManager;

/**
 * MapperTest
 *
 * Tests BedRest\DataMapper\Mapper.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class MapperTest extends FunctionalModelTestCase
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
    protected static $mockEntities = array();

    /**
     * Bootstrapping set up phase for each test.
     */
    protected function setUp()
    {
        parent::setUp();

        // create mapper
        $this->mapper = new Mapper(
            new ServiceManager($this->getServiceConfiguration())
        );
        $this->mapper->setEntityManager(self::getEntityManager());
    }

    /**
     * Creates the mock entities needed for this test.
     */
    protected static function prepareTestData(EntityManager $em)
    {
        $employee1 = new EmployeeEntity();
        $employee1->id = 1;
        $employee1->name = 'Jane Doe';

        $department1 = new DepartmentEntity();
        $department1->id = 1;
        $department1->name = 'Department #1';

        $asset1 = new AssetEntity();
        $asset1->id = 1;
        $asset1->name = 'Asset #1';

        $asset2 = new AssetEntity();
        $asset2->id = 2;
        $asset2->name = 'Asset #2';

        $asset3 = new AssetEntity();
        $asset3->id = 3;
        $asset3->name = 'Asset #3';

        $employee1->Department = $department1;
        $department1->Employees = array($employee1);

        $employee1->Assets = array($asset1, $asset2, $asset3);
        $asset1->LoanedTo = $employee1;
        $asset2->LoanedTo = $employee1;
        $asset3->LoanedTo = $employee1;

        $em->persist($employee1);
        $em->persist($department1);
        $em->persist($asset1);
        $em->persist($asset2);
        $em->persist($asset3);

        $em->flush();
    }

    /**
     * Returns a mock entity instance.
     *
     * @param  string  $entity
     * @param  integer $id
     * @return object
     */
    protected static function getMockEntity($entity, $id)
    {
        $class = 'BedRest\TestFixtures\Models\Company\\' . $entity;

        return self::getEntityManager()->find($class, $id);
    }

    /**
     * Test the class fulfills all contracts demanded of it.
     */
    public function testClassContract()
    {
        $this->assertTrue($this->mapper instanceof MapperInterface);
    }

    /**
     * Data provider for testing casting of field data during mapping.
     *
     * Format is array(field, expected, provided).
     *
     * @return array
     */
    public function sampleFieldData()
    {
        $dob = new \DateTime('1st May 2012 00:00:00 +00:00');

        return array(
            'integer'
                => array('id', 2, 2),
            'integerFromString'
                => array('id', 2, '2'),
            'string'
                => array('name', 'John Doe', 'John Doe'),
            'stringFromInteger'
                => array('name', '789', 789),
            'datetime'
                => array('dob', $dob, $dob),
            'datetimeFromISO'
                => array('dob', $dob, $dob->format(\DateTime::ISO8601)),
            'datetimeFromTimestamp'
                => array('dob', $dob, $dob->getTimestamp()),
            'datetimeFromArray'
                => array('dob', $dob, (array) $dob),
            'datetimeNull'
                => array('dob', null, null),
            'boolean'
                => array('active', true, true),
            'booleanFromString'
                => array('active', true, '1'),
            'booleanFromInteger'
                => array('active', false, 0),
            'float'
                => array('salary', 50000.00, 50000.00),
            'floatFromString'
                => array('salary', 50000.00, '50000.00'),
            'floatFromInteger'
                => array('salary', 50000.00, 50000)
        );
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
        $mapped = $resource->{$field};

        $this->assertEquals(gettype($expected), gettype($mapped));
        $this->assertEquals($expected, $mapped);
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

        // data in the reverse-mapped resource should be equal to the original data in type and value
        $reversed = $this->mapper->reverse($resource, 1);

        $this->assertInternalType('array', $reversed);
        $this->assertEquals(gettype($value), gettype($reversed[$field]));
        $this->assertEquals($value, $reversed[$field]);
    }

    /**
     * Data provider for testing association mapping.
     *
     * Format is array(associationName, entityType, entityIDs). All associations are on the Employee entity.
     *
     * @return array
     */
    public function sampleAssociationData()
    {
        return array(
            'manyToOne' => array(
                'Department',
                'Department',
                1
            ),
            'oneToMany' => array(
                'Assets',
                'Asset',
                array(
                    1,
                    2,
                    3
                )
            )
        );
    }

    /**
     * Tests association mapping, using various relationship types.
     *
     * @dataProvider sampleAssociationData
     *
     * @param string $association Association name.
     * @param string $entityType  Entity type of the association.
     * @param mixed  $entityIds   ID(s) to populate the association with
     */
    public function testAssociationMapping($association, $entityType, $entityIds)
    {
        $collectionAssociation = is_array($entityIds);

        // establish the expected result
        // NOTE: We do this here, rather than in the data provider, as we rely on the mock entities having
        //       been populated in the createMockEntities() call, which is executed after the data provider.
        if ($collectionAssociation) {
            $expected = array();

            foreach ($entityIds as $individualId) {
                $expected[] = self::getMockEntity($entityType, $individualId);
            }
        } else {
            $expected = self::getMockEntity($entityType, $entityIds);
        }

        // get the actual result
        $data = array(
            $association => $entityIds
        );

        $resource = new EmployeeEntity();
        $this->mapper->map($resource, $data);

        $mapped = $resource->{$association};

        // check the mapping was correct
        $this->assertEquals(gettype($expected), gettype($mapped));

        if ($collectionAssociation) {
            foreach ($mapped as $index => $item) {
                $this->assertEntityEquals($expected[$index], $item);
            }
        } else {
            $this->assertEntityEquals($expected, $mapped);
        }
    }

    /**
     * Notes on testing reverse mapping associations:

       - provided with an entity
       - reverse map
       - entity associations should either be shallow reversed if depth is exceeded
       -   or reversed as if they were the top level entity if depth is not exceeded
       - special cases:
         - collections with no items should be reversed as an empty array
         - single-valued associations should be reversed as NULL
         - recursive mapping

       - we need to get the original item
       - then for each association:
         - find out the association type
         - check if the depth dictates a shallow map or not
         - for collections, shallow mapped:
           - we expect an array of arrays with a single field in each: the ID
         - for collections, deep mapped:
           - we expect an array of arrays with the full mapping as if it was the base entity
         - for collections, empty
           - we expect an empty array
         - for singles, shallow mapped:
           - we expect an array with a single field: the ID
         - for singles, deep mapped
           - we expect an array with the full mapping as if it was the base entity
         - for singles, empty
           - we expect NULL

       - ensure proxies are populated if necessary? how to test this behaviour?
       - could force clear Doctrine's cache, request an entity with extreme lazy loading then reverse
    */

    /**
     * Tests reverse mapping collection-valued associations with a depth of > 1.
     */
    public function testAssociationReverseCollectionDeep()
    {
        // get the test resource and reverse it
        $resource = self::getMockEntity('Employee', 1);
        $reversed = $this->mapper->reverse($resource, 2);

        // check we get an array back, firstly
        $this->assertInternalType('array', $reversed);

        // check the collection association itself
        $reversedCollection = $reversed['Assets'];
        $this->assertInternalType('array', $reversedCollection);
        $this->assertNotEmpty($reversedCollection);

        // check each item is equivalent to when it is reversed individually
        foreach ($reversedCollection as $reversedCollectionItem) {
            $this->assertInternalType('array', $reversedCollectionItem);

            $collectionItemResource = $this->getMockEntity('Asset', $reversedCollectionItem['id']);
            $collectionItemResourceReversed = $this->mapper->reverse($collectionItemResource, 1);

            $this->assertEquals($collectionItemResourceReversed, $reversedCollectionItem);
        }
    }

    /**
     * Tests reverse mapping collection-valued associations with a depth of 1.
     */
    public function testAssociationReverseCollectionShallow()
    {
        // get the test resource and reverse it
        $resource = self::getMockEntity('Employee', 1);
        $reversed = $this->mapper->reverse($resource, 1);

        // check we get an array back, firstly
        $this->assertInternalType('array', $reversed);

        // check the collection association itself
        $reversedCollection = $reversed['Assets'];
        $this->assertInternalType('array', $reversedCollection);
        $this->assertNotEmpty($reversedCollection);

        // check each item is of the form array('id' => n) with no more data
        foreach ($reversedCollection as $reversedCollectionItem) {
            $this->assertInternalType('array', $reversedCollectionItem);
            $this->assertCount(1, $reversedCollectionItem);
            $this->assertArrayHasKey('id', $reversedCollectionItem);
        }
    }

    /**
     * Tests reverse mapping single-valued associations with a depth of > 1.
     */
    public function testAssociationReverseSingleDeep()
    {
        // get the test resource and reverse it
        $resource = self::getMockEntity('Employee', 1);
        $reversed = $this->mapper->reverse($resource, 2);

        // check we get an array back, firstly
        $this->assertInternalType('array', $reversed);

        // check the item is equivalent to when it is reversed individually
        $reversedItem = $reversed['Department'];
        $this->assertInternalType('array', $reversedItem);

        $itemResource = $this->getMockEntity('Department', $reversedItem['id']);
        $itemResourceReversed = $this->mapper->reverse($itemResource, 1);

        $this->assertEquals($itemResourceReversed, $reversedItem);
    }

    /**
     * Tests reverse mapping single-valued associations with a depth of 1.
     */
    public function testAssociationReverseSingleShallow()
    {
        // get the test resource and reverse it
        $resource = self::getMockEntity('Employee', 1);
        $reversed = $this->mapper->reverse($resource, 1);

        // check we get an array back, firstly
        $this->assertInternalType('array', $reversed);

        // check the item is of the form array('id' => n) with no more data
        $reversedItem = $reversed['Department'];
        $this->assertInternalType('array', $reversedItem);
        $this->assertCount(1, $reversedItem);
        $this->assertArrayHasKey('id', $reversedItem);
    }
}
