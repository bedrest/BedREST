<?php

namespace BedREST\TestFixtures\Mocks;

/**
 * BedREST\TestFixtures\Mocks\EntityManagerMock
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class EntityManagerMock extends \Doctrine\ORM\EntityManager
{
    /**
     * @override
     */
    public static function create($conn, \Doctrine\ORM\Configuration $config = null, \Doctrine\Common\EventManager $eventManager = null)
    {
        if (is_null($config)) {
            $config = new \Doctrine\ORM\Configuration();
            $config->setProxyDir(BEDREST_TESTS_PATH . 'BedREST/TextFixtures/Proxies');
            $config->setProxyNamespace('BedREST\TextFixtures\Proxies');
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
            $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver());
        }

        if (is_null($eventManager)) {
            $eventManager = new \Doctrine\Common\EventManager();
        }

        return new EntityManagerMock($conn, $config, $eventManager);
    }
}
