<?php

namespace BedRest\Tests\DataConverter;

use BedRest\DataConverter\JsonConverter;
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
        $this->setExpectedException('BedRest\DataConverter\Exception');

        $mapper = new JsonConverter();

        $mapper->decode('{not valid JSON');
    }
}
