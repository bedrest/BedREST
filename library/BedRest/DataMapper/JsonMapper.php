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

use Doctrine\DBAL\Types\Type;

/**
 * JsonMapper
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class JsonMapper extends AbstractMapper
{
    /**
     * Maps data into an entity from a raw JSON string.
     * @param mixed $resource
     * @param string $data
     */
    public function map($resource, $data)
    {
        if (!is_string($data)) {
            throw new DataMappingException('Supplied data is not a string');
        }

        // decode the data
        $data = json_decode($data, true);

        // check if an error occurred during decoding
        if ($error = json_last_error()) {
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $errorMessage = 'Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $errorMessage = 'Invalid or malformed JSON';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $errorMessage = 'Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $errorMessage = 'Syntax error, malformed JSON';
                break;
                default:
                    $errorMessage = '';
                break;
            }

            throw new DataMappingException("Error during JSON deocding: $errorMessage");
        }

        // cast data
        $data = $this->castFieldData($resource, $data);

        foreach ($data as $property => $value) {
            $resource->$property = $value;
        }
    }

    /**
     * Maps data from an entity to a JSON string.
     * @param mixed $resource Entity to map data from.
     * @return array
     */
    public function reverse($resource)
    {
        $data = $this->reverseToArray($resource);

        return json_encode($data);
    }
    
    /**
     * Converts a resource into an array.
     * @param mixed $resource
     * @return array
     */
    protected function reverseToArray($resource)
    {
        $classMetadata = $this->getEntityManager()->getClassMetadata(get_class($resource));

        $data = array();

        foreach ($classMetadata->fieldMappings as $property => $mapping) {
            switch ($mapping['type']) {
                case Type::DATE:
                case Type::DATETIME:
                case Type::DATETIMETZ:
                case Type::TIME:
                    if ($resource->$property instanceof \DateTime) {
                        $value = $resource->$property->format(\DateTime::ISO8601);
                    }
                break;
                default:
                    $value = $resource->$property;
                break;
            }

            $data[$property] = $value;
        }
        
        return $data;
    }
    
    /**
     * Reverse maps generic data structures into the desired format.
     * @param mixed $data
     * @return mixed
     */
    public function reverseGeneric($data)
    {
        $return = $this->reverseGenericWorker($data);
        
        return json_encode($return);
    }
    
    /**
     * Performs the actual work of reverseGeneric().
     * @param mixed $data
     * @return mixed
     */
    protected function reverseGenericWorker($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_object($value) && !$this->getEntityManager()->getMetadataFactory()->isTransient(get_class($value))) {
                    $return[$key] = $this->reverseToArray($value);
                } else {
                    $return[$key] = $this->reverseGenericWorker($value);
                }
            }
        } elseif (is_object($data) && !$this->getEntityManager()->getMetadataFactory()->isTransient(get_class($data))) {
            $return = $this->reverseToArray($data);
        } else {
            $return = $data;
        }
        
        return $return;
    }
}

