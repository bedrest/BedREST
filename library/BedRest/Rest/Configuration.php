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

namespace BedRest\Rest;

/**
 * Configuration
 *
 * Configuration container for BedRest.
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @todo Can we remove the need for a 'Configuration' object altogether?
 * @todo This seems to hold config for different areas of the system, not a good separation of concerns.
 */
class Configuration
{
    /**
     * Default service class name.
     * @var string
     */
    protected $defaultService = 'BedRest\Model\Doctrine\Service';

    /**
     * Allowable content types with associated converters.
     * @var array
     */
    protected $contentTypes = array(
        'application/json'
    );

    /**
     * Array of paths where resources should be auto-discovered.
     * @var array
     */
    protected $resourcePaths = array();

    /**
     * Sets the default service class name.
     * @param string $className
     */
    public function setDefaultService($className)
    {
        $this->defaultService = $className;
    }

    /**
     * Returns the default service class name.
     * @return string
     */
    public function getDefaultService()
    {
        return $this->defaultService;
    }

    /**
     * Sets the allowed content types for responses.
     * @param array $contentTypes
     */
    public function setContentTypes(array $contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }

    /**
     * Returns the allowable content types for responses.
     * @return array
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    /**
     * Sets a group of paths in which resources can be auto-discovered.
     * @param array $paths
     */
    public function setResourcePaths(array $paths)
    {
        $this->resourcePaths = $paths;
    }

    /**
     * Returns the paths in which resources can be auto-discovered.
     * @return array
     */
    public function getResourcePaths()
    {
        return $this->resourcePaths;
    }
}
