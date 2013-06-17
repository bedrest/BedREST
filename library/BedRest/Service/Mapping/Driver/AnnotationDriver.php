<?php
/*
 * Copyright (C) 2011-2013 Geoff Adams <geoff@dianode.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace BedRest\Service\Mapping\Driver;

use BedRest\Mapping\Driver\AbstractAnnotationDriver;
use BedRest\Service\Mapping\Exception;
use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Service\Mapping\Driver\Driver;
use Doctrine\Common\Annotations\Reader;

/**
 * AnnotationDriver
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class AnnotationDriver extends AbstractAnnotationDriver implements Driver
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
        $serviceAnnotation = $this->getAnnotation($classAnnotations, self::ANNOTATION_SERVICE);
        if ($serviceAnnotation === false) {
            throw Exception::classIsNotMappedService($className);
        }

        // process events
        foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            $methodAnnotations = $this->reader->getMethodAnnotations($reflMethod);
            $methodAnnotations = $this->indexAnnotationsByType($methodAnnotations);

            // process listeners
            $listenerAnnotations = $this->getAnnotation($methodAnnotations, self::ANNOTATION_LISTENER);
            if ($listenerAnnotations !== false) {
                if (!is_array($listenerAnnotations)) {
                    $listenerAnnotations = array($listenerAnnotations);
                }

                foreach ($listenerAnnotations as $listenerAnnotation) {
                    // ensure we don't double-up listener entries
                    $existingListeners = $serviceMetadata->getListeners($listenerAnnotation->event);
                    if (!in_array($reflMethod->getName(), $existingListeners)) {
                        $serviceMetadata->addListener($listenerAnnotation->event, $reflMethod->getName());
                    }
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
        $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($className), self::ANNOTATION_SERVICE);
        if (!empty($annotation)) {
            return true;
        }

        return false;
    }
}
