<?php

namespace BedRest\Mapping;

use Doctrine\ORM\Mapping\Annotation;

/**
 * BedRest\Mapping\Annotation\RestResource
 *
 * @author Geoff Adams <geoff@dianode.net>
 * 
 * @Annotation
 * @Target("CLASS")
 */
class RestResource implements Annotation
{
    /**
     * Fully-qualified class name (without preceding slash) of the service used
     * for interacting with entities of this type.
     * @var string
     */
    public $serviceClass = '';
}
