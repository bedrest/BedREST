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

namespace BedRest\Service;

use BedRest\Service\Mapping\Driver\Driver as ServiceDriver;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Configuration
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Configuration
{
    /**
     * Array of service namespaces.
     * @var array
     */
    protected $serviceNamespaces = array();

    /**
     * Service metadata driver.
     * @var \BedRest\Service\Mapping\Driver\Driver
     */
    protected $serviceMetadataDriverImpl;

    /**
     * Resource cache implementation.
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $serviceMetadataCacheImpl;

    /**
     * Service container.
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $serviceContainer;

    /**
     * Sets all service namespace mappings.
     * @param array $serviceNamespaces
     */
    public function setServiceNamespaces(array $serviceNamespaces)
    {
        foreach ($serviceNamespaces as $alias => $namespace) {
            $this->addServiceNamespace($alias, $namespace);
        }
    }

    /**
     * Adds a single service namespace mapping.
     * @param string $alias
     * @param string $namespace
     */
    public function addServiceNamespace($alias, $namespace)
    {
        $namespace = $this->normaliseNamespace($namespace);

        $this->serviceNamespaces[$alias] = $namespace;
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
     * Returns the service namespace associated with an alias, if defined.
     * @param $alias
     * @return string|null
     */
    public function getServiceNamespace($alias)
    {
        if (!isset($this->serviceNamespaces[$alias])) {
            return null;
        }

        return $this->serviceNamespaces[$alias];
    }

    /**
     * Normalises a namespace by appending the trailing slash.
     * @param  string $namespace
     * @return string
     */
    protected function normaliseNamespace($namespace)
    {
        $namespace = rtrim($namespace, '\\');

        return $namespace . '\\';
    }

    /**
     * Sets the service metadata driver implementation.
     * @param \BedRest\Service\Mapping\Driver\Driver $driver
     */
    public function setServiceMetadataDriverImpl(ServiceDriver $driver)
    {
        $this->serviceMetadataDriverImpl = $driver;
    }

    /**
     * Returns the service metadata driver implementation.
     * @return \BedRest\Service\Mapping\Driver\Driver
     */
    public function getServiceMetadataDriverImpl()
    {
        return $this->serviceMetadataDriverImpl;
    }

    /**
     * Sets the service metadata cache implementation.
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function setServiceMetadataCacheImpl(Cache $cache)
    {
        $this->serviceMetadataCacheImpl = $cache;
    }

    /**
     * Returns the service metadata cache implementation.
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getServiceMetadataCacheImpl()
    {
        return $this->serviceMetadataCacheImpl;
    }

    /**
     * Sets the service container.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function setServiceContainer(ContainerBuilder $container)
    {
        $this->serviceContainer = $container;
    }

    /**
     * Returns the service container.
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }
}
