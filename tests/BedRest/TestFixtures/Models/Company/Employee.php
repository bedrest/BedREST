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
 *      handler="BedRest\TestFixtures\ResourceHandlers\DefaultHandler",
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
     * Department name.
     * @var string
     * @ORM\Column(type="string")
     */
    protected $department;

    /**
     * SSN of the employee.
     * @var string
     * @ORM\Column(type="string")
     */
    protected $ssn;

    /**
     * Date of birth of the employee.
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $dob;

    /**
     * Whether the employee is active or not.
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $active;

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function __get($property)
    {
        return $this->$property;
    }
}
