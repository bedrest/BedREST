<?php

namespace BedRest\TestFixtures\Models\Company;

use Doctrine\ORM\Mapping as ORM;
use BedRest\Resource\Mapping\Annotation as BedRest;

/**
 * Asset
 *
 * Author: Geoff Adams <geoff@dianode.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="asset")
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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Name of the asset.
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * Who the asset is currently loaned to.
     * @var \BedRest\TestFixtures\Models\Company\Employee
     * @ORM\ManyToOne(targetEntity="BedRest\TestFixtures\Models\Company\Employee")
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
