<?php

namespace BedRest\TestFixtures\Models\Company;

use BedRest\Resource\Mapping\Annotation as BedRest;

/**
 * Asset
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Resource(name="asset")
 * @BedRest\Handler(
 *      service="BedRest\TestFixtures\Services\Company\Generic"
 * )
 */
class Asset
{
    /**
     * ID reference.
     * @var integer
     */
    protected $id;

    /**
     * Name of the asset.
     * @var string
     */
    protected $name;

    /**
     * Who the asset is currently loaned to.
     * @var \BedRest\TestFixtures\Models\Company\Employee
     */
    protected $LoanedTo;

    /**
     * Magic setter.
     * @param string $property
     * @param mixed  $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Magic getter.
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }
}
