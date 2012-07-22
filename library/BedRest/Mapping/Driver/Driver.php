<?php

namespace BedRest\Mapping\Driver;

use BedRest\Mapping\ResourceMetadata;

/**
 * Driver
 * 
 * @author Geoff Adams <geoff@dianode.net>
 */
interface Driver
{
    public function loadMetadataForClass($className, ResourceMetadata $resourceMetadata);
}
