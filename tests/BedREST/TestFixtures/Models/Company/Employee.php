<?php

namespace BedREST\TestFixtures\Models\Company;

use Doctrine\ORM\Mapping as ORM,
    BedREST\Mapping as BedREST;

/**
 * BedREST\Test\Models\Company\Employee
 *
 * @author Geoff Adams <geoff@dianode.net>
 * 
 * @ORM\Entity
 * @ORM\Table(name="employee")
 * @BedREST\RestResource
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
    
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function __get($property)
    {
        return $this->$property;
    }
    
    public function getRestProperties()
    {
        
    }
}
