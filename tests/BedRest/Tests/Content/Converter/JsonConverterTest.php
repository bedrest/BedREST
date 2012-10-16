<?php

namespace BedRest\Tests\Content\Converter;

use BedRest\Content\Converter\JsonConverter;
use BedRest\Tests\BaseTestCase;

/**
 * JsonConverter
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class JsonConverterTest extends BaseTestCase
{
    public function testInvalidJsonThrowsException()
    {
        $this->setExpectedException('BedRest\Content\Converter\Exception');

        $mapper = new JsonConverter();

        $mapper->decode('{not valid JSON');
    }
}
