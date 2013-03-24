<?php

namespace BedRest\Tests;

use BedRest\Rest\Configuration as RestConfiguration;
use BedRest\Service\Configuration as ServiceConfiguration;
use BedRest\Service\Mapping\Driver\AnnotationDriver as ServiceAnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * BedRest\Tests\BaseTestCase
 *
 * @author Geoff Adams <geoff@dianode.net
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Configuration used for tests.
     * 
     * @var \BedRest\Rest\Configuration
     */
    protected $config;

    /**
     * Service configuration used for tests.
     * 
     * @var \BedRest\Service\Configuration
     */
    protected $serviceConfig;

    /**
     * Returns a Configuration object for use in tests.
     * 
     * @return \BedRest\Rest\Configuration
     * @todo Move this method to FunctionalModelTestCase.
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
     *
     * @todo Move this method to FunctionalModelTestCase.
     */
    protected function createConfiguration()
    {
        $config = new RestConfiguration();

        $this->config = $config;
    }

    /**
     * Returns a Service Configuration object for use in tests.
     * 
     * @return \BedRest\Service\Configuration
     * @todo Move this method to FunctionalModelTestCase.
     */
    protected function getServiceConfiguration()
    {
        if (!$this->serviceConfig) {
            $this->createServiceConfiguration();
        }

        return $this->serviceConfig;
    }

    /**
     * Creates a service configuration object, pre-configured for tests which require a model to work with.
     * 
     * @todo Move this method to FunctionalModelTestCase.
     */
    protected function createServiceConfiguration()
    {
        $config = new ServiceConfiguration();

        $this->serviceConfig = $config;
    }
}
