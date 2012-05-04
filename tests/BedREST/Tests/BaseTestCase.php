<?php

namespace BedREST\Tests;

/**
 * BedREST\Tests\BaseTestCase
 *
 * @author Geoff Adams <geoff@dianode.net
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Entity manager used for tests.
     * @var BedREST\TestFixtures\Mocks\EntityManagerMock
     */
    protected static $em;
    
    /**
     * Retrieves an entity manager to be used by tests requiring one.
     * @param mixed $conn
     * @return \BedREST\TestFixtures\Mocks\EntityManagerMock
     */
    public static function getEntityManager($conn = null)
    {
        if (!self::$em) {
            $config = new \Doctrine\ORM\Configuration();
            $config->setProxyDir(BEDREST_TESTS_PATH . 'BedREST/TextFixtures/Proxies');
            $config->setProxyNamespace('BedREST\TextFixtures\Proxies');
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
            $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver());

            $eventManager = new \Doctrine\Common\EventManager();
        
            if ($conn == null) {
                $conn = array(
                    'driverClass'  => '\BedREST\TestFixtures\Mocks\DriverMock',
                    'wrapperClass' => '\BedREST\TestFixtures\Mocks\ConnectionMock',
                    'user'         => 'test',
                    'password'     => 'connection'
                );
            }

            if (is_array($conn)) {
                $conn = \Doctrine\DBAL\DriverManager::getConnection($conn, $config, $eventManager);
            }
            
            self::$em = \BedREST\TestFixtures\Mocks\EntityManagerMock::create($conn);
        }
        
        return self::$em;
    }
}
