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
class SimpleDoctrineMapper extends AbstractDoctrineMapper
{
    /**
     * Maps an input array into a resource or set of resources.
     * @param  mixed                           $resource Resource to map data into.
     * @param  array                           $data     Array of data.
     * @throws \BedRest\Service\Data\Exception
     */
    public function map($resource, $data)
    {
        if (!is_array($data)) {
            throw new Exception('Supplied data is not an array.');
        }

        // cast data
        $fields = $this->castFields($resource, $data);
        $associations = $this->castAssociations($resource, $data);

        $data = array_merge($fields, $associations);

        foreach ($data as $property => $value) {
            $resource->$property = $value;
        }
    }

    protected function castAssociations($resource, array $data)
    {
        // get the class meta data for the entity
        $em = $this->getEntityManager();

        $classMetaInfo = $em->getClassMetadata(get_class($resource));

        // process all associations
        $output = array();

        foreach ($classMetaInfo->associationMappings as $name => $mapping) {
            // skip associations not included in the data
            if (!isset($data[$name])) {
                continue;
            }

            // detect the value of the association
            if ($classMetaInfo->isSingleValuedAssociation($name)) {
                $value = $this->castSingleValuedAssociation($data[$name], $mapping);
            } elseif ($classMetaInfo->isCollectionValuedAssociation($name)) {
                $value = $this->castCollectionValuedAssociation($data[$name], $mapping);
            } else {
                throw Exception::invalidAssociationType($classMetaInfo->getName(), $name);
            }

            $output[$name] = $value;
        }

        return $output;
    }

    protected function castSingleValuedAssociation($data, $mapping)
    {
        $output = $this->getEntityManager()->find($mapping['targetEntity'], $data);

        return $output;
    }

    protected function castCollectionValuedAssociation($data, $mapping)
    {
        $output = array();

        foreach ($data as $item) {
            $output[] = $this->getEntityManager()->find($mapping['targetEntity'], $item);
        }

        return $output;
    }

    /**
     * Reverse maps a data set into an array.
     * @param  mixed   $data         Data to reverse map.
     * @param  integer $maxDepth
     * @param  integer $currentDepth
     * @return array
     */
    public function reverse($data, $maxDepth = 1, $currentDepth = 0)
    {
        $return = null;

        if (is_array($data) || $data instanceof Collection) {
            $return = array();

            foreach ($data as $key => $value) {
                $return[$key] = $this->reverse($value, $maxDepth, $currentDepth);
            }
        } elseif (is_object($data) &&
            !$this->getEntityManager()->getMetadataFactory()->isTransient(get_class($data))
        ) {
            $currentDepth++;
            $return = $this->reverseEntity($data, $maxDepth, $currentDepth);
        } else {
            $return = $data;
        }

        return $return;
    }

    /**
     * Reverse maps a single resource entity.
     * @param  object  $resource
     * @param  integer $maxDepth
     * @param  integer $currentDepth
     * @return array
     */
    protected function reverseEntity($resource, $maxDepth, $currentDepth)
    {
        if ($currentDepth > $maxDepth) {
            $output = $resource->id;
        } else {
            $classMetadata = $this->getEntityManager()->getClassMetadata(get_class($resource));

            $fieldData = $this->reverseEntityFields($resource, $classMetadata);
            $associationData = $this->reverseEntityAssociations($resource, $classMetadata, $maxDepth, $currentDepth);

            $output = array_merge($fieldData, $associationData);
        }

        return $output;
    }

    /**
     * Reverse maps entity fields using the class metadata to perform any casting.
     * @param  mixed                               $resource
     * @param  \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return array
     */
    protected function reverseEntityFields($resource, ClassMetadata $classMetadata)
    {
        $data = array();

        foreach ($classMetadata->fieldMappings as $property => $mapping) {
            $value = $resource->$property;

            $data[$property] = $value;
        }

        return $data;
    }

    /**
     * Reverse maps entity associations, using the class metadata to determine those associations.
     * @param  mixed                               $resource
     * @param  \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return array
     */
    protected function reverseEntityAssociations($resource, ClassMetadata $classMetadata, $maxDepth, $currentDepth)
    {
        $data = array();

        foreach ($classMetadata->associationMappings as $association => $mapping) {
            $value = $resource->$association;

            // force proxies to load data
            if ($value instanceof \Doctrine\ORM\Proxy\Proxy) {
                $value->__load();
            }

            if ($mapping['type'] & ClassMetadata::TO_ONE) {
                // single entity
                $data[$association] = $this->reverse($resource->$association, $maxDepth, $currentDepth);
            } else {
                // collections must be looped through, assume each item within is an entity
                $data[$association] = array();

                if ($value === null) {
                    $value = array();
                }

                foreach ($value as $item) {
                    $data[$association][] = $this->reverse($item, $maxDepth, $currentDepth);
                }
            }
        }

        return $data;
    }
}
