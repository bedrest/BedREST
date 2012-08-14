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

namespace BedRest\DataMapper;

use BedRest\Configuration;
use BedRest\ServiceManager;
use BedRest\DataMapper\DataMapper;
use BedRest\DataMapper\DataMappingException;

/**
 * DataMapperFactory
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class DataMapperFactory
{
    /**
     * Stores instances of loaded data mappers.
     * @var array
     */
    protected $loadedDataMappers;

    /**
     * Configuration object to pass to instances.
     * @var \BedRest\Configuration
     */
    protected $configuration;

    /**
     * Service manager object to pass to instances.
     * @var \BedRest\ServiceManager
     */
    protected $serviceManager;

    /**
     * Constructor.
     * @param \BedRest\Configuration $configuration
     * @param \BedRest\ServiceManager $serviceManager
     */
    public function __construct(Configuration $configuration, ServiceManager $serviceManager)
    {
        $this->configuration = $configuration;
        $this->serviceManager = $serviceManager;
    }

    /**
     * Returns an instance of the specified data mapper.
     * @param string $className
     */
    public function getDataMapper($className)
    {
        if (!isset($this->loadedDataMappers[$className])) {
            if (!class_exists($className)) {
                throw DataMappingException::dataMapperNotFound($className);
            }

            $this->loadDataMapper($className);
        }

        return $this->loadedDataMappers[$className];
    }

    /**
     * Loads an instance of a data mapper.
     * @param string $className
     */
    protected function loadDataMapper($className)
    {
        $dataMapper = new $className($this->configuration, $this->serviceManager);

        $this->loadedDataMappers[$className] = $dataMapper;
    }
}
