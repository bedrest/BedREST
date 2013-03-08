<?php

namespace BedRest\TestFixtures\Models\Company;

use Doctrine\ORM\Mapping as ORM;
use BedRest\Resource\Mapping\Annotation as BedRest;

/**
 * Department
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="department")
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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Name of the department.
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * Employees belonging to this department.
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="BedRest\TestFixtures\Models\Company\Employee", mappedBy="Department")
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
