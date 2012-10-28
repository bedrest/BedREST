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

namespace BedRest\Resource\Mapping\Driver;

use BedRest\Resource\Mapping\Exception;
use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Resource\Mapping\Driver\Driver;
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
    public function loadMetadataForClass($className, ResourceMetadata $resourceMetadata)
    {
        // get all class annotations
        $reflClass = new \ReflectionClass($className);

        $classAnnotations = $this->reader->getClassAnnotations($reflClass);

        // if we are receiving annotations indexed by number, transform it to by class name
        if ($classAnnotations && is_numeric(key($classAnnotations))) {
            foreach ($classAnnotations as $annotation) {
                $classAnnotations[get_class($annotation)] = $annotation;
            }
        }

        // load headline resource information
        if (isset($classAnnotations['BedRest\Resource\Mapping\Annotation\Resource'])) {
            $resourceAnnotation = $classAnnotations['BedRest\Resource\Mapping\Annotation\Resource'];

            // resource name
            if (!empty($resourceAnnotation->name)) {
                $resourceMetadata->setName($resourceAnnotation->name);
            } else {
                $resourceMetadata->setName(substr($className, strrpos($className, '\\') + 1));
            }
        }

        // load handler information
        if (isset($classAnnotations['BedRest\Resource\Mapping\Annotation\Handler'])) {
            $handlerAnnotation = $classAnnotations['BedRest\Resource\Mapping\Annotation\Handler'];

            // handler
            if (!empty($handlerAnnotation->handler)) {
                $resourceMetadata->setHandler($handlerAnnotation->handler);
            }

            // service
            if (!empty($handlerAnnotation->service)) {
                $resourceMetadata->setService($handlerAnnotation->service);
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
            if (in_array($sourceFile, $includedFiles) && $this->isResource($className)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }

    /**
     * {@inheritDoc}
     */
    public function isResource($className)
    {
        $annotation = $this->reader->getClassAnnotation(
            new \ReflectionClass($className),
            'BedRest\Resource\Mapping\Annotation\Resource'
        );

        if ($annotation) {
            return true;
        }

        return false;
    }
}
