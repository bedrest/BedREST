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

namespace BedRest\Service\Mapping\Driver;

use BedRest\Service\Mapping\Exception;
use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Service\Mapping\Driver\Driver;
use Doctrine\Common\Annotations\Reader;

/**
 * AnnotationDriver
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class AnnotationDriver implements Driver
{
    const ANNOTATION_SERVICE = 'BedRest\Service\Mapping\Annotation\Service';
    const ANNOTATION_LISTENER = 'BedRest\Service\Mapping\Annotation\Listener';

    /**
     * Annotation reader instance.
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * Array of paths to search for services.
     * @var array
     */
    protected $paths = array();

    /**
     * Constructor.
     * @param \Doctrine\Common\Annotations\Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Adds a path to search for services.
     * @param string $path
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
    }

    /**
     * Adds a set of paths to search for services.
     * @param array $paths
     */
    public function addPaths($paths)
    {
        $this->paths = array_merge($this->paths, $paths);
    }

    /**
     * Retrieves the set of paths to search for services.
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForClass($className, ServiceMetadata $serviceMetadata)
    {
        // get all class annotations
        $reflClass = new \ReflectionClass($className);

        $classAnnotations = $this->reader->getClassAnnotations($reflClass);
        $classAnnotations = $this->indexAnnotationsByType($classAnnotations);

        // load headline service information
        if (isset($classAnnotations[self::ANNOTATION_SERVICE])) {
            $serviceAnnotation = $classAnnotations[self::ANNOTATION_SERVICE];
            $serviceMetadata->setType($serviceAnnotation->type);
        }

        // process events
        foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            $methodAnnotations = $this->reader->getMethodAnnotations($reflMethod);
            $methodAnnotations = $this->indexAnnotationsByType($methodAnnotations);

            // process listeners
            if (isset($methodAnnotations[self::ANNOTATION_LISTENER])) {
                if (!is_array($methodAnnotations[self::ANNOTATION_LISTENER])) {
                    $methodAnnotations[self::ANNOTATION_LISTENER]
                        = array($methodAnnotations[self::ANNOTATION_LISTENER]);
                }

                foreach ($methodAnnotations[self::ANNOTATION_LISTENER] as $listenerAnnotation) {
                    $serviceMetadata->addListener($listenerAnnotation->event, $reflMethod->getName());
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * This has been lifted pretty much wholesale from Doctrine ORM, so credit where credit is due.
     * @see Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver
     */
    public function getAllClassNames()
    {
        if (!$this->paths) {
            throw Exception::pathsRequired();
        }

        $classes = array();
        $includedFiles = array();

        foreach ($this->paths as $path) {
            if (!is_dir($path)) {
                throw Exception::invalidPath($path);
            }

            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+\.php$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = realpath($file[0]);

                require_once $sourceFile;

                $includedFiles[] = $sourceFile;
            }
        }

        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            if (in_array($sourceFile, $includedFiles) && $this->isService($className)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }

    /**
     * {@inheritDoc}
     */
    public function isService($className)
    {
        $annotation = $this->reader->getClassAnnotation(
            new \ReflectionClass($className),
            'BedRest\Service\Mapping\Annotation\Service'
        );

        if ($annotation) {
            return true;
        }

        return false;
    }

    protected function indexAnnotationsByType($annotations)
    {
        $indexed = array();

        // if we are receiving annotations indexed by number, transform it to by class name
        if ($annotations && is_numeric(key($annotations))) {
            foreach ($annotations as $annotation) {
                $annotationType = get_class($annotation);

                if (isset($indexed[$annotationType]) && !is_array($indexed[$annotationType])) {
                    $indexed[$annotationType] = array($indexed[$annotationType], $annotation);
                } elseif (isset($indexed[$annotationType])) {
                    $indexed[$annotationType][] = $annotation;
                } else {
                    $indexed[$annotationType] = $annotation;
                }
            }
        }

        return $indexed;
    }
}
