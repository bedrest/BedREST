<?php

namespace BedRest\TestFixtures\Models\Company;

use BedRest\Resource\Mapping\Annotation as BedRest;

/**
 * Department
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Resource
 * @BedRest\Handler(
 *      service="BedRest\TestFixtures\Services\Company\Generic"
 * )
 */
class Department
{
    /**
     * ID reference.
     * @var integer
     */
    protected $id;

    /**
     * Name of the department.
     * @var string
     */
    protected $name;

    /**
     * Employees belonging to this department.
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $Employees;

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
