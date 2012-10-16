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
}
