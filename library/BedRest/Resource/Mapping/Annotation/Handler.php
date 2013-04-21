<?php

namespace BedRest\Resource\Mapping\Annotation;

/**
 * Handler
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @Annotation
 * @Target("CLASS")
 */
class Handler
{
    /**
     * Name of the service for this resource.
     * @var string
     */
    public $service;
}
