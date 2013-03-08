<?php

namespace BedRest\Tests;

use BedRest\Resource\Mapping\Driver\AnnotationDriver as ResourceAnnotationDriver;
use BedRest\Rest\Configuration as RestConfiguration;
use BedRest\Service\Configuration as ServiceConfiguration;
use BedRest\Service\Mapping\Driver\AnnotationDriver as ServiceAnnotationDriver;
use BedRest\TestFixtures\Mocks\EntityManagerMock;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * RequiresModelTestCase
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RequiresModelTestCase extends BaseTestCase
{
    /**
     * Doctrine EntityManager used for tests.
     * @var \BedRest\TestFixtures\Mocks\EntityManagerMock
     */
    protected static $doctrineEntityManager;

    /**
     * Retrieves an entity manager to be used by tests requiring one.
     * @param  mixed                                         $conn
     * @return \BedRest\TestFixtures\Mocks\EntityManagerMock
     */
    public static function getEntityManager($conn = null)
    {
        if (!self::$doctrineEntityManager) {
            $config = new Configuration();

            // entity namespaces for the test environment
            $namespaces = array(
                'BedRest\TestFixtures\Models\Company' => TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company/'
            );

            $config->setEntityNamespaces(array_keys($namespaces));

            // basic Proxy config
            $config->setProxyDir(TESTS_BASEDIR . '/BedRest/TextFixtures/Proxies');
            $config->setProxyNamespace('BedRest\TextFixtures\Proxies');

            // ArrayCache, to avoid persistent caching in test environment
            $config->setMetadataCacheImpl(new ArrayCache());

            // basic AnnotationDriver configuration for parsing Doctrine annotations
            $metaDriver = new AnnotationDriver(new AnnotationReader());
            $metaDriver->addPaths(array_values($namespaces));

            $config->setMetadataDriverImpl($metaDriver);

            // basic EventManager
            $eventManager = new EventManager();

            // mock the DB connection
            if ($conn == null) {
                $conn = array(
                    'driverClass'  => '\BedRest\TestFixtures\Mocks\DriverMock',
                    'wrapperClass' => '\BedRest\TestFixtures\Mocks\ConnectionMock',
                    'user'         => 'test',
                    'password'     => 'connection'
                );
            }

            if (is_array($conn)) {
                $conn = DriverManager::getConnection($conn, $config, $eventManager);
            }

            // mock the EntityManager
            self::$doctrineEntityManager = EntityManagerMock::create($conn, $config, $eventManager);
        }

        return self::$doctrineEntityManager;
    }

    /**
     * Returns a configuration object, pre-configured for tests which require a model to work with.
     * @return \BedRest\Rest\Configuration
     */
    public static function getConfiguration()
    {
        if (!self::$config) {
            $config = new RestConfiguration();

            // create metadata driver
            $reader = new AnnotationReader();
            $driver = new ResourceAnnotationDriver($reader);
            $driver->addPaths(
                array(
                    TESTS_BASEDIR . '/BedRest/TestFixtures/Models',
                    TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company'
                )
            );

            $config->setResourceMetadataDriverImpl($driver);

            self::$config = $config;
        }

        return self::$config;
    }

    /**
     * Returns a service configuration object, pre-configured for tests which require a model to work with.
     * @return \BedRest\Service\Configuration
     */
    public static function getServiceConfiguration()
    {
        if (!self::$serviceConfig) {
            $config = new ServiceConfiguration();

            // create metadata driver
            $driver = new ServiceAnnotationDriver(new AnnotationReader());
            $driver->addPaths(
                array(
                    'BedRest\TestFixtures\Services' => TESTS_BASEDIR . '/BedRest/TestFixtures/Services'
                )
            );
            $config->setServiceMetadataDriverImpl($driver);

            // create DI container
            $container = new ContainerBuilder();
            $container->setParameter('doctrine.entitymanager', static::getEntityManager());
            $config->setServiceContainer($container);

            self::$serviceConfig = $config;
        }

        return self::$serviceConfig;
    }
}
