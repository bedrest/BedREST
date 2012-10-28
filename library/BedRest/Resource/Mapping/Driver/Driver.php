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
