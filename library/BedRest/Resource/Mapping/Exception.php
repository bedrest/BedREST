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
    public static function serviceClassNotProvided($className)
    {
        return new self("Class '{$className}' does not have a specified service class.");
    }

    public static function classIsNotMappedResource($className)
    {
        return new self("Class '{$className}' is not a mapped resource.");
    }

    public static function resourceNotFound($resourceName)
    {
        return new self("Resource '{$resourceName}' not found.");
    }

    public static function classIsNotMappedService($className)
    {
        return new self("Class '{$className}' is not a mapped service.");
    }

    public static function pathsRequired()
    {
        return new self("A set of paths must be provided in order to discover classes.");
    }

    public static function invalidPath($path)
    {
        return new self("The path '{$path}' is invalid.");
    }
}
