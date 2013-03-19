<?php

namespace BedRest\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration as DoctrineConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * FunctionalModelTestCase
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class FunctionalModelTestCase extends BaseTestCase
{
    /**
     * Doctrine EntityManager used for tests.
     * @var \Doctrine\ORM\EntityManager
     */
    protected static $doctrineEntityManager;

    /**
     * Doctrine SchemaTool for managing the test schema.
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    protected static $schemaTool;

    protected function setUp()
    {
        $this->createConfiguration();
        $this->createServiceConfiguration();
    }

    protected function tearDown()
    {
        $this->config = null;
        $this->serviceConfig = null;
    }

    public static function setUpBeforeClass()
    {
        $em = self::getEntityManager();
        $metaData = $em->getMetadataFactory()->getAllMetadata();

        self::$schemaTool = new SchemaTool($em);
        self::$schemaTool->dropSchema($metaData);
        self::$schemaTool->createSchema($metaData);

        static::prepareTestData($em);
    }

    public static function tearDownAfterClass()
    {
        self::$schemaTool->dropDatabase();
        self::$schemaTool = null;

        self::$doctrineEntityManager = null;
    }

    /**
     * Should be overridden by inheriting test cases.
     * @param \Doctrine\ORM\EntityManager $em
     */
    protected static function prepareTestData(EntityManager $em)
    {

    }

    /**
     * Retrieves an EntityManager connected to an SQLite database.
     * @return \Doctrine\ORM\EntityManager
     */
    protected static function getEntityManager()
    {
        if (!self::$doctrineEntityManager) {
            self::createEntityManager();
        }

        return self::$doctrineEntityManager;
    }

    /**
     * Creates the EntityManager instance.
     * @return \Doctrine\ORM\EntityManager
     */
    protected static function createEntityManager()
    {
        $config = new DoctrineConfiguration();

        // entity namespaces for the test environment
        $namespaces = array(
            'BedRest\TestFixtures\Models\Company' => TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company/'
        );

        $config->setEntityNamespaces(array_keys($namespaces));

        // basic Proxy config
        $config->setProxyDir(TESTS_BASEDIR . '/BedRest/TestFixtures/Proxies');
        $config->setProxyNamespace('BedRest\TestFixtures\Proxies');

        // ArrayCache, to avoid persistent caching in test environment
        $config->setMetadataCacheImpl(new ArrayCache());

        // basic AnnotationDriver configuration for parsing Doctrine annotations
        $metaDriver = new AnnotationDriver(new AnnotationReader());
        $metaDriver->addPaths(array_values($namespaces));

        $config->setMetadataDriverImpl($metaDriver);

        // create the EntityManager
        $connectionOptions = array(
            'driver'    => 'pdo_sqlite',
            'path'      => TESTS_BASEDIR . '/test_db.sqlite'
        );
        $em = EntityManager::create($connectionOptions, $config);

        self::$doctrineEntityManager = $em;
    }

    protected function createServiceConfiguration()
    {
        parent::createServiceConfiguration();

        $config = $this->serviceConfig;
        $container = $config->getServiceContainer();
        $container->setParameter('doctrine.entitymanager', self::getEntityManager());
    }

    /**
     * Asserts that the two Doctrine entities are the same item.
     * @param object  $expected
     * @param object  $actual
     * @param integer $maxDepth
     * @param integer $currentDepth
     */
    public static function assertEntityEquals($expected, $actual, $maxDepth = 2, $currentDepth = 0)
    {
        // depth check to avoid stack overflows
        $currentDepth++;
        if ($currentDepth > $maxDepth) {
            return;
        }

        $classMetadata = self::getEntityManager()->getClassMetadata(get_class($expected));

        // verify field contents
        foreach ($classMetadata->getFieldNames() as $field) {
            self::assertEquals($expected->$field, $actual->$field);
        }

        // handle associations by simple recursion
        foreach ($classMetadata->getAssociationMappings() as $association => $mapping) {
            if ($classMetadata->isSingleValuedAssociation($association)) {
                self::assertEntityEquals($expected->$association, $actual->$association, $maxDepth, $currentDepth);
            } else {
                $actualAssociation = $actual->$association;
                foreach ($expected->$association as $index => $associationItem) {

                    self::assertEntityEquals($associationItem, $actualAssociation[$index], $maxDepth, $currentDepth);
                }
            }
        }
    }
}