<?php

namespace BedRest\Tests\Model\Doctrine;

use BedRest\Model\Doctrine\Service as DoctrineService;
use BedRest\Rest\ResourceNotFoundException;
use BedRest\Rest\RestManager;
use BedRest\Rest\Request\Request;
use BedRest\Service\ServiceManager;
use BedRest\TestFixtures\Models\Company\TestDataSet;
use BedRest\Tests\FunctionalModelTestCase;
use Doctrine\ORM\EntityManager;

/**
 * ServiceTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceTest extends FunctionalModelTestCase
{
    /**
     * Service under test.
     * @var \BedRest\Model\Doctrine\Service
     */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        // create a ServiceManager so we can get a DataMapper instance with all the functionality we need
        $serviceManager = new ServiceManager($this->getServiceConfiguration());
        $serviceManager->setServiceMetadataFactory($this->getServiceMetadataFactory());

        // create a RestManager
        $restManager = new RestManager($this->getConfiguration());
        $restManager->setServiceManager($serviceManager);
        $restManager->setResourceMetadataFactory($this->getResourceMetadataFactory());

        // get the metadata and mapper required by the service
        $resourceMetadata = $restManager->getResourceMetadataByName('asset');
        $mapper = $serviceManager->getDataMapper($resourceMetadata->getService());

        // create the service
        $this->service = new DoctrineService($resourceMetadata, $mapper);
        $this->service->setEntityManager(self::getEntityManager());
    }

    /**
     * Creates the mock entities needed for this test.
     */
    protected static function prepareTestData(EntityManager $em)
    {
        foreach (TestDataSet::getDataSet() as $item) {
            $em->persist($item);
        }

        $em->flush();
    }

    /**
     * Returns a mock entity instance.
     *
     * @param string  $entityType
     * @param integer $id
     *
     * @return object
     */
    protected static function getMockEntity($entityType, $id)
    {
        $class = 'BedRest\TestFixtures\Models\Company\\' . $entityType;

        return self::getEntityManager()->find($class, $id);
    }

    public function testCreateResource()
    {
        $rawBody = array(
            'name' => 'test-creation'
        );

        $request = new Request();
        $request->setContentType('application/json');
        $request->setRawBody(json_encode($rawBody));

        $data = $this->service->create($request);

        $this->assertInstanceOf('BedRest\TestFixtures\Models\Company\Asset', $data);
        $this->assertNotNull($data->id);
        $this->assertEquals($rawBody['name'], $data->name);
    }

    public function testUpdateResource()
    {
        $id = 4;
        $rawBody = array(
            'name' => 'test-update'
        );

        $request = new Request();
        $request->setContentType('application/json');
        $request->setRawBody(json_encode($rawBody));
        $request->setRouteComponents(
            array(
                'identifier' => $id
            )
        );

        $data = $this->service->update($request);

        $this->assertInstanceOf('BedRest\TestFixtures\Models\Company\Asset', $data);
        $this->assertEquals($id, $data->id);
        $this->assertEquals($rawBody['name'], $data->name);
    }

    public function testDeleteResource()
    {
        $id = 4;

        $request = new Request();
        $request->setRouteComponents(
            array(
                'identifier' => $id
            )
        );

        $data = $this->service->get($request);
        $this->assertInstanceOf('BedRest\TestFixtures\Models\Company\Asset', $data);

        $this->service->delete($request);

        $exceptionRaised = false;
        try {
            $this->service->get($request);
        } catch (ResourceNotFoundException $e) {
            $exceptionRaised = true;
        }

        if (!$exceptionRaised) {
            $this->fail("Expected exception 'ResourceNotFoundException' was not raised, resource was not deleted.");
        }
    }

    public function testDeleteNonExistentResource()
    {
        $request = new Request();
        $request->setRouteComponents(
            array(
                'identifier' => 100
            )
        );

        $this->setExpectedException('BedRest\Rest\ResourceNotFoundException');
        $this->service->delete($request);
    }

    public function testGetResource()
    {
        $request = new Request();
        $request->setRouteComponents(
            array(
                'identifier' => 1
            )
        );

        $data = $this->service->get($request);

        $this->assertInstanceOf('BedRest\TestFixtures\Models\Company\Asset', $data);
    }

    public function testGetNonExistentResource()
    {
        $request = new Request();
        $request->setRouteComponents(
            array(
                'identifier' => 100
            )
        );

        $this->setExpectedException('BedRest\Rest\ResourceNotFoundException');
        $this->service->get($request);
    }

    public function testGetCollection()
    {
        $request = new Request();

        $data = $this->service->getCollection($request);

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('perPage', $data);
        $this->assertEquals($data['count'], count($data['items']));

        $this->assertCount(3, $data['items']);
        $this->assertEquals(3, $data['total']);
        $this->assertInstanceOf('BedRest\TestFixtures\Models\Company\Asset', $data['items'][0]);
        $this->assertInstanceOf('BedRest\TestFixtures\Models\Company\Asset', $data['items'][1]);
        $this->assertInstanceOf('BedRest\TestFixtures\Models\Company\Asset', $data['items'][2]);
    }

    public function testGetCollectionSize()
    {
        $size = $this->service->getCollectionSize();

        $this->assertEquals(3, $size);
    }
}
