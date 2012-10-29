<?php

namespace BedRest\Tests;

use \BedRest\Rest\Configuration as RestConfiguration;
use \BedRest\Service\Configuration as ServiceConfiguration;

/**
 * BedRest\Tests\BaseTestCase
 *
 * @author Geoff Adams <geoff@dianode.net
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Entity manager used for tests.
     * @var \BedRest\TestFixtures\Mocks\EntityManagerMock
     */
    protected static $em;

    /**
     * Configuration used for tests.
     * @var \BedRest\Rest\Configuration
     */
    protected static $config;

    /**
     * Service configuration used for tests.
     * @var \BedRest\Service\Configuration
     */
    protected static $serviceConfig;

    /**
     * Retrieves an entity manager to be used by tests requiring one.
     * @param  mixed                                         $conn
     * @return \BedRest\TestFixtures\Mocks\EntityManagerMock
     */
    public static function getEntityManager($conn = null)
    {
        if (!self::$em) {
            $config = new \Doctrine\ORM\Configuration();

            // entity namespaces for the test environment
            $namespaces = array(
                'BedRest\TestFixtures\Models\Company' => TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company/'
            );

            $config->setEntityNamespaces(array_keys($namespaces));

            // basic Proxy config
            $config->setProxyDir(TESTS_BASEDIR . '/BedRest/TextFixtures/Proxies');
            $config->setProxyNamespace('BedRest\TextFixtures\Proxies');

            // ArrayCache, to avoid persistent caching in test environment
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());

            // basic AnnotationDriver configuration for parsing Doctrine annotations
            $metaDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(new \Doctrine\Common\Annotations\AnnotationReader());
            $metaDriver->addPaths(array_values($namespaces));

            $config->setMetadataDriverImpl($metaDriver);

            // basic EventManager
            $eventManager = new \Doctrine\Common\EventManager();

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
                $conn = \Doctrine\DBAL\DriverManager::getConnection($conn, $config, $eventManager);
            }

            // mock the EntityManager
            self::$em = \BedRest\TestFixtures\Mocks\EntityManagerMock::create($conn, $config, $eventManager);
        }

        return self::$em;
    }

    /**
     * Returns a configuration object for use in tests.
     * @return \BedRest\Rest\Configuration
     */
    public static function getConfiguration()
    {
        if (!self::$config) {
            $config = new RestConfiguration();

            self::$config = $config;
        }

        return self::$config;
    }

    /**
     * Returns a service configuration object for use in tests.
     * @return \BedRest\Service\Configuration
     */
    public static function getServiceConfiguration()
    {
        if (!self::$serviceConfig) {
            $config = new ServiceConfiguration();

            self::$serviceConfig = $config;
        }

        return self::$serviceConfig;
    }
}
