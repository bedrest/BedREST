<?php

namespace BedRest\Tests;

use BedRest\Resource\Mapping\ResourceMetadataFactory;
use BedRest\Resource\Mapping\Driver\AnnotationDriver as ResourceAnnotationDriver;
use BedRest\Rest\Configuration as RestConfiguration;
use BedRest\Service\Mapping\ServiceMetadataFactory;
use BedRest\Service\Mapping\Driver\AnnotationDriver as ServiceAnnotationDriver;
use BedRest\Service\SimpleLocator;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * FunctionalModelTestCase
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class FunctionalModelTestCase extends BaseTestCase
{
    /**
     * Configuration used for tests.
     *
     * @var \BedRest\Rest\Configuration
     */
    protected $config;

    protected function setUp()
    {
        $this->createConfiguration();
    }

    protected function tearDown()
    {
        $this->config = null;
    }

    /**
     * Returns a ResourceMetadataFactory instance, pre-configured to use the BedRest\TestFixtures\Models classes.
     *
     * @return \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    protected function getResourceMetadataFactory()
    {
        $driver = new ResourceAnnotationDriver(new AnnotationReader());
        $driver->addPaths(
            array(
                TESTS_BASEDIR . '/BedRest/TestFixtures/Models',
                TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company'
            )
        );

        $factory = new ResourceMetadataFactory($this->getConfiguration(), $driver);

        return $factory;
    }

    /**
     * Returns a ServiceMetadataFactory instance, pre-configured to use the BedRest\TestFixtures\Services classes.
     *
     * @return \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    protected function getServiceMetadataFactory()
    {
        $driver = new ServiceAnnotationDriver(new AnnotationReader());
        $driver->addPaths(
            array(
                'BedRest\TestFixtures\Services' => TESTS_BASEDIR . '/BedRest/TestFixtures/Services'
            )
        );

        return new ServiceMetadataFactory($driver);
    }

    /**
     * Returns a SimpleLocator service locator.
     *
     * @return \BedRest\Service\LocatorInterface
     */
    protected function getServiceLocator()
    {
        return new SimpleLocator(new ContainerBuilder());
    }

    /**
     * Returns a Configuration object for use in tests.
     *
     * @return \BedRest\Rest\Configuration
     */
    protected function getConfiguration()
    {
        if (!$this->config) {
            $this->createConfiguration();
        }

        return $this->config;
    }

    /**
     * Creates a configuration object, pre-configured for tests which require a model to work with.
     */
    protected function createConfiguration()
    {
        $config = new RestConfiguration();

        $this->config = $config;
    }
}
