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

namespace BedRest\Content\Converter;

/**
 * Registry
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Registry
{

    /**
     * Mapping of content type to converter class name.
     * @var array
     */
    protected static $converters = array(
        'application/json' => 'BedRest\Content\Converter\JsonConverter'
    );

    /**
     * Sets the mapping between content types and content converters.
     * @param array $contentConverters
     */
    public static function setConverters(array $contentConverters)
    {
        self::$converters = $contentConverters;
    }

    /**
     * Sets the content converter class for the specified type.
     * @param string $contentType
     * @param string $className
     */
    public static function setConverter($contentType, $className)
    {
        self::$converters[$contentType] = $className;
    }

    /**
     * Returns the content type mappings.
     * @return array
     */
    public static function getConverters()
    {
        return self::$converters;
    }

    /**
     * Returns the content converter for the supplied content type, if available.
     * @param  string         $contentType
     * @return string|boolean
     */
    public static function getConverterClass($contentType)
    {
        if (!isset(self::$converters[$contentType])) {
            return null;
        }

        return self::$converters[$contentType];
    }

    /**
     * Returns an instance of the content converter for the requested content type, if available.
     * @param  string                               $contentType
     * @throws \BedRest\Content\Converter\Exception
     * @return \BedRest\Content\Converter\Converter
     */
    public static function getConverterInstance($contentType)
    {
        $className = self::getConverterClass($contentType);

        if (empty($className)) {
            throw new Exception("No converter is specified for content type '$contentType'.");
        } elseif (!class_exists($className)) {
            throw new Exception("Content converter '$className' could not be found.");
        }

        return new $className;
    }
}
