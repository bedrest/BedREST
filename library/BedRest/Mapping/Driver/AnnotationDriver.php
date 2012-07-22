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

namespace BedRest\Mapping\Driver;

use BedRest\Mapping\ResourceMetadata;
use BedRest\Mapping\Driver\Driver;

/**
 * AnnotationDriver
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class AnnotationDriver implements Driver
{
    protected $annotationReader;
    
    public function loadMetadataForClass($className, ResourceMetadata $resourceMetadata)
    {
        $reflClass = $resourceMetadata->getClassMetadata()->getReflectionClass();
        
        $classAnnotations = $this->annotationReader->getClassAnnotation($reflClass);
        
        if ($classAnnotations && is_numeric(key($classAnnotations))) {
            foreach ($classAnnotations as $annotation) {
                $classAnnotations[get_class($annotation)] = $annotation;
            }
        }
        
        if (isset($classAnnotations['BedRest\Mapping\Resource'])) {
            $resourceAnnotation = $classAnnotations['BedRest\Mapping\Resource'];
            
            if (!empty($resourceAnnotation->name)) {
                $resourceMetadata->setName($resourceAnnotation->name);
            } else {
                $resourceMetadata->setName(substr($className, strrpos($className, '\\') + 1));
            }
            
            if (!empty($resourceAnnotation->serviceClass)) {
                $resourceMetadata->setName($resourceAnnotation->serviceClass);
            } else {
                throw MappingException::serviceClassNotProvided($className);
            }
        }
        
        
    }
}
