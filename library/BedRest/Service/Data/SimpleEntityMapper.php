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

namespace BedRest\Service\Data;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * SimpleEntityMapper
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class SimpleEntityMapper extends AbstractMapper
{
    /**
     * Maps an input array into a resource or set of resources.
     * @param mixed $resource Resource to map data into.
     * @param array $data Array of data.
     */
    public function map($resource, $data)
    {
        if (!is_array($data)) {
            throw new Exception('Supplied data is not an array.');
        }

        // cast data
        $data = $this->castFieldData($resource, $data);

        foreach ($data as $property => $value) {
            $resource->$property = $value;
        }
    }

    /**
     * Reverse maps data into an array.
     * @param mixed $data Data to reverse map.
     * @return string
     */
    public function reverse($data)
    {
        $return = null;

        if (is_array($data)) {
            // arrays
            $return = array();

            foreach ($data as $key => $value) {
                $return[$key] = $this->reverse($value);
            }
        } elseif (is_object($data) && !$this->getEntityManager()->getMetadataFactory()->isTransient(get_class($data))) {
            // entities
            $return = $this->reverseEntity($data);
        } else {
            // non-arrays and non-entity objects, along with native types
            $return = $data;
        }
        return $return;
    }

    /**
     * Reverse maps an entity instance.
     * @param mixed $resource
     * @return array
     */
    protected function reverseEntity($resource)
    {
        $classMetadata = $this->getEntityManager()->getClassMetadata(get_class($resource));

        $fieldData = $this->reverseEntityFields($resource, $classMetadata);
        $associationData = $this->reverseEntityAssociations($resource, $classMetadata);

        return array_merge($fieldData, $associationData);
    }

    /**
     * Reverse maps entity fields using the class metadata to perform any casting.
     * @param mixed $resource
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return array
     */
    protected function reverseEntityFields($resource, ClassMetadata $classMetadata)
    {
        $data = array();

        foreach ($classMetadata->fieldMappings as $property => $mapping) {
            switch ($mapping['type']) {
                /**
                case Type::DATE:
                case Type::DATETIME:
                case Type::DATETIMETZ:
                case Type::TIME:
                    if ($resource->$property instanceof \DateTime) {
                        $value = $resource->$property->format(\DateTime::ISO8601);
                    }
                    break;
                    */
                default:
                    $value = $resource->$property;
                    break;
            }

            $data[$property] = $value;
        }

        return $data;
    }

    /**
     * Reverse maps entity associations, using the class metadata to determine those associations.
     * @param mixed $resource
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return array
     */
    protected function reverseEntityAssociations($resource, ClassMetadata $classMetadata)
    {
        $data = array();

        foreach ($classMetadata->associationMappings as $association => $mapping) {
            $value = $resource->$association;

            // force proxies to load data
            if ($value instanceof \Doctrine\ORM\Proxy\Proxy) {
                $value->__load();
            }

            if ($value instanceof Collection) {
                // collections must be looped through, assume each item within is an entity
                $data[$association] = array();

                foreach ($value as $item) {
                    $data[$association][] = $this->reverseEntity($item);
                }
            } else {
                $data[$association] = $this->reverseEntity($resource->$association);
            }
        }

        return $data;
    }
}

