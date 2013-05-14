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

namespace BedRest\Resource\Mapping;

/**
 * Exception
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Exception extends \Exception
{
    /**
     * Thrown when a class is not a mapped resource.
     *
     * @param string $className
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function classIsNotMappedResource($className)
    {
        return new self("Class '{$className}' is not a mapped resource.");
    }

    /**
     * Thrown when a resource cannot be found.
     *
     * @param string $resourceName
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function resourceNotFound($resourceName)
    {
        return new self("Resource '{$resourceName}' not found.");
    }

    /**
     * Thrown when no paths have been supplied.
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function pathsRequired()
    {
        return new self("A set of paths must be provided in order to discover classes.");
    }

    /**
     * Thrown when an invalid path has been supplied.
     *
     * @param $path
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function invalidPath($path)
    {
        return new self("The path '{$path}' is invalid.");
    }

    /**
     * Thrown when a set of invalid sub-resources have been supplied.
     *
     * @param string $className
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function invalidSubResources($className)
    {
        return new self("Invalid set of sub-resources supplied for class '$className''.");
    }
}
