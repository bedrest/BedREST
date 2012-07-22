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
     * Identifier fields for the resource.
     * @var array
     */
    protected $identifierFields = array();
    
    /**
     * Class metadata for the entity.
     * @var Doctrine\ORM\Mapping\ClassMetadata
     */
    protected $classMetadata;
    
    /**
     * Constructor.
     * @param string $entityClass
     */
    public function __construct($entityClass)
    {
        $this->entityClass = $entityClass;
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
     * Sets the class name of the service for this resource.
     * @param string $serviceClass 
     */
    public function setServiceClass($serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }
    
    /**
     * Returns the class name of the service for this resource.
     * @return string
     */
    public function getServiceClass()
    {
        return $this->serviceClass;
    }
    
    /**
     * Sets the entity class name. Must be a Doctrine entity.
     * @param string $entityClass 
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }
    
    /**
     * Returns the Doctrine entity class name.
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }
    
    /**
     * Sets the class metadata object for the entity associated with this resource.
     * @param Doctrine\ORM\Mapping\ClassMetadata $classMetadata 
     */
    public function setClassMetadata(ClassMetadata $classMetadata)
    {
        $this->classMetadata = $classMetadata;
    }
    
    /**
     * Returns the class metadata for the entity associated with this resource.
     * @return Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->classMetadata;
    }
}
