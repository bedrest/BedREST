<?php

namespace BedREST\TestFixtures\Mocks;

/**
 * BedREST\TestFixtures\Mocks\DriverMock
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class DriverMock implements \Doctrine\DBAL\Driver
{
    private $_platformMock;

    private $_schemaManagerMock;

    /**
     * @override
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        return new DriverConnectionMock();
    }

    /**
     * @override
     */
    protected function _constructPdoDsn(array $params)
    {
        return "";
    }

    /**
     * @override
     */
    public function getDatabasePlatform()
    {
        if ( ! $this->_platformMock) {
            $this->_platformMock = new DatabasePlatformMock;
        }
        return $this->_platformMock;
    }

    /**
     * @override
     */
    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        if($this->_schemaManagerMock == null) {
            return new SchemaManagerMock($conn);
        } else {
            return $this->_schemaManagerMock;
        }
    }

    /**
     * @override
     */
    public function getDatabase(\Doctrine\DBAL\Connection $conn)
    {
        
    }

    /**
     * @override
     */
    public function getName()
    {
        return 'mock';
    }
}