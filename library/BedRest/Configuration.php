<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace BedRest;

use BedRest\Mapping\Resource\Driver\Driver as ResourceDriver;
use BedRest\Mapping\Service\Driver\Driver as ServiceDriver;
use Doctrine\ORM\EntityManager;

/**
 * Configuration
 *
 * Configuration container for BedRest.
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
     * Resource metadata driver.
     * @var \BedRest\Mapping\Resource\Driver\Driver
     */
    protected $resourceMetadataDriverImpl;
    
    /**
     * Service metadata driver.
     * @var \BedRest\Mapping\Service\Driver\Driver
     */
    protected $serviceMetadataDriverImpl;

    /**
     * Array of service namespaces, analogous to Doctrine\ORM\Configuration's entity namespace storage.
     * @var array
     */
    protected $serviceNamespaces = array();
    
    /**
     * Default service class name.
     * @var string
     */
    protected $defaultServiceClassName = 'BedRest\Service\SimpleEntityService';
    
    /**
     * Default data mapper.
     * @var string
     */
    protected $defaultDataMapper = 'BedRest\DataMapper\SimpleEntityMapper';
    
    /**
     * Allowable content types.
     * @var array
     */
    protected $contentTypes = array(
        'application/json'
    );
    
    /**
     * Available data converters.
     * @var array
     */
    protected $dataConverters = array(
        'application/json' => 'BedRest\DataConverter\JsonConverter'
    );

    /**
     * Array of data mapper class names against their aliases.
     * @var array
     */
    protected $dataMappers = array(
        'array' => 'BedRest\DataMapper\ArrayMapper'
    );

    /**
     * Sets the entity manager.
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns the entity manager.
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Sets all service namespace mappings.
     * @param array $serviceNamespaces
     */
    public function setServiceNamespaces(array $serviceNamespaces)
    {
        $this->serviceNamespaces = $serviceNamespaces;
    }

    /**
     * Returns all registered service namespace mappings.
     * @return array
     */
    public function getServiceNamespaces()
    {
        return $this->serviceNamespaces;
    }

    /**
     * Adds a single service namespace mapping.
     * @param string $alias
     * @param string $namespace
     */
    public function addServiceNamespace($alias, $namespace)
    {
        $this->serviceNamespaces[$alias] = $namespace;
    }

    /**
     * Sets the resource metadata driver implementation.
     * @param \BedRest\Mapping\Resource\Driver\Driver $driver
     */
    public function setResourceMetadataDriverImpl(ResourceDriver $driver)
    {
        $this->resourceMetadataDriverImpl = $driver;
    }

    /**
     * Returns the resource metadata driver implementation.
     * @return \BedRest\Mapping\Resource\Driver\Driver
     */
    public function getResourceMetadataDriverImpl()
    {
        return $this->resourceMetadataDriverImpl;
    }
    
    /**
     * Sets the service metadata driver implementation.
     * @param \BedRest\Mapping\Service\Driver\Driver $driver
     */
    public function setServiceMetadataDriverImpl(ServiceDriver $driver)
    {
        $this->serviceMetadataDriverImpl = $driver;
    }
    
    /**
     * Returns the service metadata driver implementation.
     * @return \BedRest\Mapping\Service\Driver\Driver
     */
    public function getServiceMetadataDriverImpl()
    {
        return $this->serviceMetadataDriverImpl;
    }
    
    /**
     * Sets the default service class name.
     * @param string $className
     */
    public function setDefaultServiceClassName($className)
    {
        $this->defaultServiceClassName = $className;
    }
    
    /**
     * Returns the default service class name.
     * @return string
     */
    public function getDefaultServiceClassName()
    {
        return $this->defaultServiceClassName;
    }
    
    /**
     * Sets the default data mapper.
     * @param string $dataMapper
     */
    public function setDefaultDataMapper($dataMapper)
    {
        $this->defaultDataMapper = $dataMapper;
    }
    
    /**
     * Returns the default data mapper.
     * @return string
     */
    public function getDefaultDataMapper()
    {
        return $this->defaultDataMapper;
    }
    
    /**
     * Sets the allowable content types for responses.
     * @param array $contentTypes
     */
    public function setContentTypes(array $contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }
    
    /**
     * Returns the allowable content types for responses.
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }
    
    /**
     * Sets the list of available data converters.
     * @param array $dataConverters
     */
    public function setDataConverters(array $dataConverters)
    {
        $this->dataConverters = $dataConverters;
}

    /**
     * Returns the list of available data converters.
     * @return array
     */
    public function getDataConverters()
    {
        return $this->dataConverters;
    }
    
    /**
     * Adds a data mapper with the given content type association, overwriting any existing entry.
     * @param string $contentType
     * @param string $dataConverter
     */
    public function addDataConverter($contentType, $dataConverter)
    {
        $this->dataConverters[$contentType] = $dataConverter;
    }
    
    /**
     * Returns the class name of the data converter for the given content type.
     * @param string $contentType
     * @return string|null
     */
    public function getDataConverter($contentType)
    {
        if (!isset($this->dataConverters[$contentType])) {
            return null;
        }
        
        return $this->dataConverters[$contentType];
    }
    
    /**
     * Sets the list of available data mappers.
     * @param array $dataMappers
     */
    public function setDataMappers(array $dataMappers)
    {
        $this->dataMappers = $dataMappers;
}

    /**
     * Returns the list of available data mappers.
     * @return array
     */
    public function getDataMappers()
    {
        return $this->dataMappers;
    }
    
    /**
     * Adds a data mapper with the given alias, overwriting any existing entry.
     * @param string $alias
     * @param string $dataMapper
     */
    public function addDataMapper($alias, $dataMapper)
    {
        $this->dataMappers[$alias] = $dataMapper;
    }
    
    /**
     * Returns the class name of the data mapper with the given alias.
     * @param string $alias
     * @return string|null
     */
    public function getDataMapper($alias)
    {
        if (!isset($this->dataMappers[$alias])) {
            return null;
        }
        
        return $this->dataMappers[$alias];
    }
}

