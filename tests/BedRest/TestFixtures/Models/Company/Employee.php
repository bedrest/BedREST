<?php

namespace BedRest\TestFixtures\Models\Company;

use BedRest\Resource\Mapping\Annotation as BedRest;

/**
 * Employee
 *
 * This model uses protected properties and magic getters and setters. It also
 * does not have an explicit resource name set, so it should be auto-generated
 * by BedREST.
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Resource
 * @BedRest\Handler(
 *      service="BedRest\TestFixtures\Services\Company\Employee"
 * )
 */
class Employee
{
    /**
     * ID reference.
     * @var integer
     */
    protected $id;

    /**
     * Name of the employee.
     * @var string
     */
    protected $name;

    /**
     * Date of birth of the employee.
     * @var \DateTime
     */
    protected $dob;

    /**
     * Whether the employee is active or not.
     * @var boolean
     */
    protected $active;

    /**
     * Employee salary.
     * @var float
     */
    protected $salary;

    /**
     * Assets associated with this employee.
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $Assets;

    /**
     * Department this employee belongs to.
     * @var \BedRest\TestFixtures\Models\Company\Department
     */
    protected $Department;

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
