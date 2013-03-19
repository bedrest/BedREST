<?php

namespace BedRest\TestFixtures\Models\Company;

use Doctrine\ORM\Mapping as ORM;
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
 * @ORM\Entity
 * @ORM\Table(name="employee")
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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Name of the employee.
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * Date of birth of the employee.
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dob;

    /**
     * Whether the employee is active or not.
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $active;

    /**
     * Employee salary.
     * @var float
     * @ORM\Column(type="decimal", nullable=true)
     */
    protected $salary;

    /**
     * Assets associated with this employee.
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="BedRest\TestFixtures\Models\Company\Asset", mappedBy="LoanedTo")
     */
    protected $Assets;

    /**
     * Department this employee belongs to.
     * @var \BedRest\TestFixtures\Models\Company\Department
     * @ORM\ManyToOne(targetEntity="BedRest\TestFixtures\Models\Company\Department")
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
