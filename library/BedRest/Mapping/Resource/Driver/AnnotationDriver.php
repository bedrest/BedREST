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

namespace BedRest\Mapping\Resource\Driver;

use BedRest\Mapping\MappingException;
use BedRest\Mapping\Resource\ResourceMetadata;
use BedRest\Mapping\Resource\Driver\Driver;
use Doctrine\Common\Annotations\Reader;

/**
 * AnnotationDriver
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class AnnotationDriver implements Driver
{
    /**
     * Annotation reader instance.
     * @var Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * Constructor.
     * @param Doctrine\Common\Annotations\Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForClass($className, ResourceMetadata $resourceMetadata)
    {
        // get all class annotations
        $reflClass = $resourceMetadata->getClassMetadata()->getReflectionClass();

        $classAnnotations = $this->reader->getClassAnnotations($reflClass);

        // if we are receiving annotations indexed by number, transform it to by class name
        if ($classAnnotations && is_numeric(key($classAnnotations))) {
            foreach ($classAnnotations as $annotation) {
                $classAnnotations[get_class($annotation)] = $annotation;
            }
        }

        // load headline resource information
        if (isset($classAnnotations['BedRest\Mapping\Resource\Annotations\Resource'])) {
            $resourceAnnotation = $classAnnotations['BedRest\Mapping\Resource\Annotations\Resource'];

            // resource name
            if (!empty($resourceAnnotation->name)) {
                $resourceMetadata->setName($resourceAnnotation->name);
            } else {
                $resourceMetadata->setName(substr($className, strrpos($className, '\\') + 1));
            }

            // service class
            if (!empty($resourceAnnotation->serviceClass)) {
                $resourceMetadata->setServiceClass($resourceAnnotation->serviceClass);
            } else {
                throw MappingException::serviceClassNotProvided($className);
            }
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function isResource($className)
    {
        $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($className), 'BedRest\Mapping\Resource\Annotations\Resource');
        
        if ($annotation) {
            return true;
        }
        
        return false;
    }
}

