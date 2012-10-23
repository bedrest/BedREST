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

namespace BedRest\Resource\Mapping;

use BedRest\Rest\Configuration;
use BedRest\Resource\Mapping\Exception;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * ResourceMetadataFactory
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadataFactory
{
    /**
     * Configuration object.
     * @var \BedRest\Rest\Configuration
     */
    protected $configuration;

    /**
     * Mapping metadata driver.
     * @var \BedRest\Resource\Mapping\Driver\Driver
     */
    protected $driver;

    /**
     * ClassMetadataFactory instance
     * @var \Doctrine\ORM\Mapping\ClassMetadataFactory
     */
    protected $classMetadataFactory;

    /**
     * Stores all loaded ResourceMetadata instances.
     * @var array
     */
    protected $loadedMetadata = array();

    /**
     * Stores a map of resource names to class names.
     * @var array
     */
    protected $resourceClassMap = array();

    /**
     * Constructor.
     * Initialises the factory with the set configuration.
     * @param \BedRest\Rest\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->driver = $configuration->getResourceMetadataDriverImpl();
        $this->classMetadataFactory = $configuration->getEntityManager()->getMetadataFactory();
    }

    /**
     * Returns ResourceMetadata for the specified class.
     * @param  string                                     $className
     * @throws \BedRest\Resource\Mapping\Exception
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function getMetadataFor($className)
    {
        if (!$this->isResource($className)) {
            throw Exception::classIsNotMappedResource($className);
        }

        if (!isset($this->loadedMetadata[$className])) {
            $this->loadMetadata($className);
        }

        return $this->loadedMetadata[$className];
    }

    /**
     * Returns ResourceMetadata for the specified resource.
     * @param  string                                     $resourceName
     * @throws \BedRest\Resource\Mapping\Exception
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function getMetadataByResourceName($resourceName)
    {
        $this->getAllMetadata();

        if (!isset($this->resourceClassMap[$resourceName])) {
            throw Exception::resourceNotFound($resourceName);
        }

        return $this->loadedMetadata[$this->resourceClassMap[$resourceName]];
    }

    /**
     * Returns the entire collection of ResourceMetadata objects for all mapped resources. Entities not marked as
     * resources are not included.
     * @return array
     */
    public function getAllMetadata()
    {
        $classMetadataCollection = $this->classMetadataFactory->getAllMetadata();

        foreach ($classMetadataCollection as $classMetadata) {
            $className = $classMetadata->getName();

            if (!isset($this->loadedMetadata[$className]) &&
                $this->isResource($className)) {
                $this->loadMetadata($classMetadata);
            }
        }

        return $this->loadedMetadata;
    }

    /**
     * Loads the ResourceMetadata for the supplied class. Class can be provided either as a class name or as a
     * ClassMetadata object.
     * @param mixed $class
     */
    protected function loadMetadata($class)
    {
        // load ClassMetadata
        if ($class instanceof ClassMetadata) {
            $classMetadata = $class;
            $class = $classMetadata->getName();
        } else {
            $classMetadata = $this->classMetadataFactory->getMetadataFor($class);
        }

        $resource = new ResourceMetadata($class);
        $resource->setClassMetadata($classMetadata);
        $resource->setHandler($this->configuration->getDefaultResourceHandler());
        $resource->setService($this->configuration->getDefaultService());

        // use the driver to load metadata
        $this->driver->loadMetadataForClass($class, $resource);

        // store the metadata
        $this->loadedMetadata[$class] = $resource;
        $this->resourceClassMap[$resource->getName()] = $class;
    }

    /**
     * Whether the specified class is a mapped resource.
     * @param  string  $className
     * @return boolean
     */
    public function isResource($className)
    {
        return $this->driver->isResource($className);
    }
}
