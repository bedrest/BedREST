<?php

namespace BedRest\Resource\Mapping\Annotation;

/**
 * Handler
 *
 * Author: Geoff Adams <geoff@dianode.net>
 *
 * @Annotation
 * @Target("CLASS")
 */
class Handler
{
    /**
     * Fully-qualified class name (without preceding slash) of the handler for this resource.
     * @var string
     */
    public $handler;

    /**
     * Fully-qualified class name (without preceding slash) of the service for this resource.
     * @var string
     */
    public $service;
}
