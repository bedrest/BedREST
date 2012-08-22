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

namespace BedRest\Service\Mapping\Driver;

use BedRest\Service\Mapping\ServiceMetadata;

/**
 * Driver
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
interface Driver
{
    /**
     * Populates the supplied ServiceMetadata object with data from the specified service class.
     * @param string $className
     * @param \BedRest\Service\Mapping\ServiceMetadata $serviceMetadata
     */
    public function loadMetadataForClass($className, ServiceMetadata $serviceMetadata);

    /**
     * Returns a list of all class names known to this driver.
     * @return array
     */
    public function getAllClassNames();

    /**
     * Whether the specified class is a mapped service.
     * @param string $className
     * @return boolean
     */
    public function isService($className);
}

