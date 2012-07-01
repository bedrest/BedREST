<?php

namespace BedRest\Mapping;

use Doctrine\ORM\Mapping\Annotation;

/**
 * BedRest\Mapping\RestProperty
 *
 * @author Geoff Adams <geoff@dianode.net>
 * 
 * @Annotation
 * @Target("PROPERTY")
 */
class RestProperty implements Annotation
{
    /**
     * Whether the property is read-only from a REST perspective.
     * @var boolean
     */
    public $readOnly = false;
}
