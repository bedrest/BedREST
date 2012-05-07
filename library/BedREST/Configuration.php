<?php

namespace BedREST;

/**
 * BedREST\Configuration
 * 
 * Configuration container for BedREST.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Configuration
{
    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    
    /**
     * Returns the entity manager.
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
    
    /**
     * Sets the entity manager.
     * @param \Doctrine\ORM\EntityManager $entityManager 
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
