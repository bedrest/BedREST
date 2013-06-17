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

namespace BedRest\Resource\Mapping\Driver;

use BedRest\Resource\Mapping\ResourceMetadata;

/**
 * Driver
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
interface Driver
{
    /**
     * Populates the supplied ResourceMetadata object with data from the specified resource class.
     * @param string                                     $className
     * @param \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     */
    public function loadMetadataForClass($className, ResourceMetadata $resourceMetadata);

    /**
     * Returns the names of all classes known to this driver.
     * @return array
     */
    public function getAllClassNames();

    /**
     * Whether the specified class is a mapped resource.
     * @param  string  $className
     * @return boolean
     */
    public function isResource($className);
}
