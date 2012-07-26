<?php

namespace BedRest\Tests;

/**
 * BedRest\Tests\BaseTestCase
 *
 * @author Geoff Adams <geoff@dianode.net
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Entity manager used for tests.
     * @var BedRest\TestFixtures\Mocks\EntityManagerMock
     */
    protected static $em;

    /**
     * Configuration used for tests.
     * @var BedRest\Configuration
     */
    protected static $config;

    /**
     * Retrieves an entity manager to be used by tests requiring one.
     * @param mixed $conn
     * @return \BedRest\TestFixtures\Mocks\EntityManagerMock
     */
    public static function getEntityManager($conn = null)
    {
        if (!self::$em) {
            $config = new \Doctrine\ORM\Configuration();
            $config->setProxyDir(BEDREST_TESTS_PATH . 'BedRest/TextFixtures/Proxies');
            $config->setProxyNamespace('BedRest\TextFixtures\Proxies');
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
            $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver());

            $eventManager = new \Doctrine\Common\EventManager();

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

            self::$em = \BedRest\TestFixtures\Mocks\EntityManagerMock::create($conn);
        }

        return self::$em;
    }

    /**
     * Returns a configuration object for use in tests.
     * @return BedRest\Configuration
     */
    public static function getConfiguration()
    {
        if (!self::$config) {
            $config = new \BedRest\Configuration();
            $config->setEntityManager(self::getEntityManager());

            self::$config = $config;
        }

        return self::$config;
    }
}

