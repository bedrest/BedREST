<?php

namespace BedRest\Mapping;

/**
 * MappingException
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class MappingException extends \BedRest\Exception
{
    public static function serviceClassNotProvided($className)
    {
        return new self("Class '{$className}' does not have a specified service class.");
    }
}
