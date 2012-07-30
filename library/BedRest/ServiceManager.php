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

use BedRest\Configuration;
use BedRest\RestManager;

/**
 * ServiceManager
 *
 * Service manager for controlling instances of various services in use by
 * the system.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceManager
{
    /**
     * BedRest configuration.
     * @var \BedRest\Configuration
     */
    protected $configuration;

    /**
     * Stores all loaded service instances.
     * @var array
     */
    protected $loadedServices;

    /**
     * Constructor.
     * @param BedRest\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns an instance of the specified service.
     * @param string $className
     * @param \BedRest\RestManager $restManager
     * @param string $resourceClassName
     * @return object
     */
    public function getService($className, RestManager $restManager, $resourceClassName)
    {
        $hash = $this->getServiceHash($restManager, $resourceClassName);
        
        if (!isset($this->loadedServices[$className][$hash])) {
            $this->loadService($className, $restManager, $resourceClassName);
        }

        return $this->loadedServices[$className][$hash];
    }

    /**
     * Whether a service has been loaded or not yet.
     * @param string $className
     * @param string $resourceClassName
     * @return boolean
     */
    public function hasService($className, RestManager $restManager, $resourceClassName)
    {
        $hash = $this->getServiceHash($restManager, $resourceClassName);
        
        if (isset($this->loadedServices[$className][$hash])) {
            return true;
        }

        return false;
    }

    /**
     * Loads the specified service class.
     * @param string $className
     * @param \BedRest\RestManager $restManager
     * @param string $resourceClassName
     * @throws \Exception
     */
    protected function loadService($className, RestManager $restManager, $resourceClassName)
    {
        if (!class_exists($className)) {
            throw new Exception("Service '$className' not found.");
        }

        $service = new $className(
            $restManager,
            $restManager->getResourceMetadata($resourceClassName)
        );

        $hash = $this->getServiceHash($restManager, $resourceClassName);
        $this->loadedServices[$className][$hash] = $service;
    }
    
    /**
     * Gets the hash used for indexing loaded services.
     * @param \BedRest\RestManager $restManager
     * @param string $resourceClassName
     * @return string
     */
    protected function getServiceHash(RestManager $restManager, $resourceClassName)
    {
        return $hash = $resourceClassName . '#' . spl_object_hash($restManager);
    }
}

