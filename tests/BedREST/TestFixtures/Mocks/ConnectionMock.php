<?php

namespace BedRest\TestFixtures\Mocks;

/**
 * BedRest\TestFixtures\Mocks\ConnectionMock
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ConnectionMock extends \Doctrine\DBAL\Connection
{
    protected $_platformMock;

    public function __construct(array $params, $driver, $config = null, $eventManager = null)
    {
        $this->_platformMock = new DatabasePlatformMock();

        parent::__construct($params, $driver, $config, $eventManager);

        // Override possible assignment of platform to database platform mock
        $this->_platform = $this->_platformMock;
    }

    /**
     * @override
     */
    public function getDatabasePlatform()
    {
        return $this->_platformMock;
    }
}