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

namespace BedRest\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Types\Type;

/**
 * ResourceMapping
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMeta
{
    /**
     * Name of the resource.
     * @var string
     */
    protected $name;
    
    /**
     * Name of the entity class for this resource.
     * @var string
     */
    protected $entityClass;
    
    /**
     * Name of the service class for this resource.
     * @var string
     */
    protected $serviceClass;
    
    /**
     * Service method names.
     * @var array
     */
    protected $serviceMethods = array(
        'index' => 'index',
        'get' => 'get',
        'post' => 'post',
        'put' => 'put',
        'delete' => 'delete'
    );
    
    /**
     * Identifier fields for the resource.
     * @var array
     */
    protected $identifierFields = array();
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setServiceClass($serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }
    
    public function getServiceClass()
    {
        return $this->serviceClass;
    }
    
    public function setServiceMethods($methods)
    {
        $this->serviceMethods = $methods;
    }
    
    public function getServiceMethods()
    {
        return $this->serviceMethods;
    }
    
    public function setServiceMethod($type, $method)
    {
        $this->serviceMethods[$type] = $method;
    }
    
    public function getServiceMethod($type)
    {
        return $this->serviceMethods[$type];
    }
    
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }
    
    public function getEntityClass()
    {
        return $this->entityClass;
    }
}
