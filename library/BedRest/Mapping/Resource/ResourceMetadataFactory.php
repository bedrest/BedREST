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

namespace BedRest\Mapping\Resource;

use BedRest\Configuration;
use BedRest\Mapping\Resource\Driver\Driver;
use BedRest\Mapping\MappingException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

/**
 * ResourceMetadataFactory
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadataFactory
{
    /**
     * Configuration object.
     * @var \BedRest\Configuration
     */
    protected $configuration;

    /**
     * Mapping metadata driver.
     * @var \BedRest\Mapping\Resource\Driver\Driver
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
     * Constructor.
     * Initialises the factory with the set configuration.
     * @param \BedRest\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->driver = $configuration->getResourceMetadataDriverImpl();
        $this->classMetadataFactory = $configuration->getEntityManager()->getMetadataFactory();
    }

    /**
     * Returns ResourceMetadata for the specified class.
     * @param string $className
     * @return \BedRest\Mapping\Resource\ResourceMetadata
     */
    public function getMetadataFor($className)
    {
        if (!$this->isResource($className)) {
            throw MappingException::classIsNotMappedResource($className);
        }

        if (!isset($this->loadedMetadata[$className])) {
            $this->loadMetadata($className);
        }

        return $this->loadedMetadata[$className];
    }

    protected function loadMetadata($className)
    {
        $resource = new ResourceMetadata($className);

        // load ClassMetadata
        $classMetadata = $this->classMetadataFactory->getMetadataFor($className);
        $resource->setClassMetadata($classMetadata);

        // use the driver to load metadata
        $this->driver->loadMetadataForClass($className, $resource);

        // store the metadata
        $this->loadedMetadata[$className] = $resource;
    }
}

