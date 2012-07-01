<?php

namespace BedRest\TestFixtures\Mocks;

/**
 * BedRest\TestFixtures\Mocks\DatabasePlatformMock
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class DatabasePlatformMock extends \Doctrine\DBAL\Platforms\AbstractPlatform
{
    /**
     * @override
     */
    protected function _getCommonIntegerTypeDeclarationSQL(array $columnDef)
    {
        
    }

    /**
     * @override
     */
    protected function initializeDoctrineTypeMappings()
    {
        
    }

    /**
     * @override
     */
    public function getBigIntTypeDeclarationSQL(array $columnDef)
    {
        
    }

    /**
     * @override
     */
    public function getBlobTypeDeclarationSQL(array $field)
    {
        
    }

    /**
     * @override
     */
    public function getBooleanTypeDeclarationSQL(array $columnDef)
    {
        
    }

    /**
     * @override
     */
    public function getClobTypeDeclarationSQL(array $field)
    {
        
    }

    /**
     * @override
     */
    public function getIntegerTypeDeclarationSQL(array $columnDef)
    {
        
    }

    /**
     * @override
     */
    public function getName()
    {
        return 'mock';
    }

    /**
     * @override
     */
    public function getSmallIntTypeDeclarationSQL(array $columnDef)
    {
        
    }
}