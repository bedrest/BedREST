<?php

namespace BedRest\TestFixtures\Models;

/**
 * InvalidResource
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class InvalidResource
{
    protected $id;

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function __get($property)
    {
        return $this->$property;
    }
}
