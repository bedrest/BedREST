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

/**
 * ResourceMetadata
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadata
{
    /**
     * Name of the resource.
     * @var string
     */
    protected $name;

    /**
     * Class name of this resource.
     * @var string
     */
    protected $className;

    /**
     * Class name of the service for this resource.
     * @var string
     */
    protected $service;

    /**
     * Identifier fields for the resource.
     * @var array
     */
    protected $identifierFields = array();

    /**
     * Constructor.
     * @param $className
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * Sets the name of the resource.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name of the resource.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the service for this resource.
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * Returns the service for this resource.
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Sets the resource class name.
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Returns the resource class name.
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
