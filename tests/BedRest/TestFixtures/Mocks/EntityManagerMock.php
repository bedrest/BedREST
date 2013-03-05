<?php

namespace BedRest\TestFixtures\Mocks;

/**
 * BedRest\TestFixtures\Mocks\EntityManagerMock
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class EntityManagerMock extends \Doctrine\ORM\EntityManager
{
    /**
     * Mock item data store.
     * @var array
     */
    protected $mockData = array();

    /**
     * @override
     */
    public static function create($conn, \Doctrine\ORM\Configuration $config = null, \Doctrine\Common\EventManager $eventManager = null)
    {
        if (is_null($config)) {
            $config = new \Doctrine\ORM\Configuration();
            $config->setProxyDir(TESTS_BASEDIR . 'BedRest/TextFixtures/Proxies');
            $config->setProxyNamespace('BedRest\TextFixtures\Proxies');
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());

            // set the annotation driver manually to enable custom annotations
            $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(new \Doctrine\Common\Annotations\AnnotationReader());
            $config->setMetadataDriverImpl($annotationDriver);
        }

        if (is_null($eventManager)) {
            $eventManager = new \Doctrine\Common\EventManager();
        }

        return new EntityManagerMock($conn, $config, $eventManager);
    }

    /**
     * Adds a mock item to the internal data store.
     * @param string $className
     * @param mixed  $id
     * @param mixed  $item
     */
    public function addMockItem($className, $id, $item)
    {
        $this->mockData[$className][$id] = $item;
    }

    /**
     * Overridden for allowing mock 'find' requests.
     * @param  string      $className
     * @param  mixed       $id
     * @return object|void
     */
    public function find($className, $id)
    {
        if (!isset($this->mockData[$className][$id])) {
            return null;
        }

        return $this->mockData[$className][$id];
    }
}
