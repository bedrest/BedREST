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

namespace BedRest\DataMapper;

use BedRest\Exception as BedRestException;

/**
 * DataMapperFactory
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class DataMapperFactory
{
    /**
     * Array of DataMapper instances.
     * @var array
     */
    protected static $instances;
    
    /**
     * Mapping of content types to data mapper class names.
     * @var array
     */
    protected static $associations;
    
    /**
     * Protected contructor to preserve singleton status.
     */
    protected function __construct()
    {
    }

    /**
     * Registers an association of content type to a data mapper class name.
     * @param string $contentType
     * @param string $dataMapper
     */
    public static function registerAssociation($contentType, $dataMapper)
    {
        self::$associations[$contentType] = $dataMapper;
    }
    
    /**
     * Returns the data mapper class name associated with a content type.
     * @param string $contentType
     * @return string|null
     */
    public static function getAssociation($contentType)
    {
        if (!isset(self::$associations[$contentType])) {
            return null;
        }
        
        self::$associations[$contentType];
    }
    
    /**
     * Registers a set of associations of content types to data mapper class names.
     * @param array $associations
     */
    public static function registerAssociations(array $associations)
    {
        foreach ($associations as $contentType => $dataMapper) {
            self::$associations[$contentType] = $dataMapper;
        }
    }
    
    /**
     * Returns all data mapper/content type associations.
     * @return array
     */
    public static function getAssociations()
    {
        return self::$associations;
    }
    
    /**
     * Returns an instance of the data mapper associated with the requested content type.
     * @param string $contentType
     * @return \BedRest\DataMapper\DataMapper
     * @throws \BedRest\Exception
     */
    public static function get($contentType)
    {
        if (!$className = self::getAssociation($contentType)) {
            throw new BedRestException("Content type '{$contentType}' is not registered with a data mapper.");
        }
        
        return self::getByClassName($className);
    }
    
    /**
     * Returns an instance of the data mapper referenced by its class name.
     * @param string $className
     * @return \BedRest\DataMapper\DataMapper
     */
    public static function getByClassName($className)
    {
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className;
        }
        
        return self::$instances[$className];
    }
}
