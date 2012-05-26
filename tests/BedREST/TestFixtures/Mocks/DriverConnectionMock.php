<?php

namespace BedRest\TestFixtures\Mocks;

/**
 * BedRest\TestFixtures\Mocks\DriverConnectionMock
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class DriverConnectionMock implements \Doctrine\DBAL\Driver\Connection
{
    /**
     * @override
     */
    public function beginTransaction()
    {
        
    }

    /**
     * @override
     */
    public function commit()
    {
        
    }

    /**
     * @override
     */
    public function errorCode()
    {
        
    }

    /**
     * @override
     */
    public function errorInfo()
    {
        
    }

    /**
     * @override
     */
    public function exec($statement)
    {
        
    }

    /**
     * @override
     */
    public function lastInsertId($name = null)
    {
        
    }

    /**
     * @override
     */
    public function prepare($prepareString)
    {
        
    }

    /**
     * @override
     */
    public function query()
    {
        
    }

    /**
     * @override
     */
    public function quote($input, $type = \PDO::PARAM_STR)
    {
        
    }

    /**
     * @override
     */
    public function rollBack()
    {
        
    }
}