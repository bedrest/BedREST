<?php

namespace BedRest\TestFixtures\Mocks\Content\Converter;

use BedRest\Content\Converter\ConverterInterface;

/**
 * Dummy
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Dummy implements ConverterInterface
{
    public function encode($value)
    {
        return 'encoded_data';
    }

    public function decode($value)
    {
        return 'decoded_data';
    }
}
