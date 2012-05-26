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

namespace BedREST\DataMapper;

/**
 * BedREST\DataMapper\Adapter\PhpArray
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ArrayMapper extends AbstractMapper
{
    /**
     * Maps data into an entity from an array.
     * @param mixed $resource Entity to map the data into.
     * @param array $data Data to be mapped.
     */
    public function map($resource, $data)
    {
        if (!is_array($data)) {
            throw new DataMappingException('Supplied data is not an array');
        }
        
        $data = $this->castFieldData($resource, $data);
        
        foreach ($data as $property => $value) {
            $resource->$property = $value;
        }
    }
    
    /**
     * Maps data from an entity into an array.
     * @param mixed $resource Entity to map data from.
     * @return array
     */
    public function reverse($resource)
    {
        $classMetadata = $this->getEntityManager()->getClassMetadata(get_class($resource));
        
        $return = array();
        foreach ($classMetadata->fieldMappings as $property => $mapping) {
            $return[$property] = $resource->$property;
        }
        
        return $return;
    }
}
