<?php

namespace BedRest\Tests;

use BedRest\Rest\Configuration as RestConfiguration;
use BedRest\Service\Configuration as ServiceConfiguration;

/**
 * BedRest\Tests\BaseTestCase
 *
 * @author Geoff Adams <geoff@dianode.net
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
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
