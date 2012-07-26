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
     * @var BedRest\Configuration
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
     * @param string $serviceClass
     */
    public function getService($serviceClass)
    {
        if (!isset($this->loadedServices[$serviceClass])) {
            $this->loadService($serviceClass);
        }

        return $this->loadedServices[$serviceClass];
    }

    /**
     * Loads the specified service class.
     * @param string $serviceClass
     * @throws \Exception
     */
    protected function loadService($serviceClass)
    {
        if (!class_exists($serviceClass)) {
            throw new Exception("Service '$serviceClass' not found.");
        }

        $service = new $serviceClass();

        if (method_exists($service, 'setEntityManager')) {
            $service->setEntityManager($this->configuration->getEntityManager());
        }

        $this->loadedServices[$serviceClass] = $service;
    }
}

