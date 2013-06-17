<?php
/*
 * Copyright (C) 2011-2013 Geoff Adams <geoff@dianode.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace BedRest\Content\Converter;

/**
 * JsonConverter
 *
 * Simple converter for JSON output.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Json implements ConverterInterface
{
    public function decode($value)
    {
        $decoded = json_decode($value, true);

        // check if an error occurred during decoding
        if ($error = json_last_error()) {
            switch ($error) {
                case JSON_ERROR_DEPTH:
                    $errorMessage = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                case JSON_ERROR_SYNTAX:
                    $errorMessage = 'Syntax error';
                    break;
                case JSON_ERROR_UTF8:
                case JSON_ERROR_CTRL_CHAR:
                    $errorMessage = 'Encoding error';
                    break;
                case JSON_ERROR_NONE:
                default:
                    $errorMessage = '';
                    break;
            }

            throw new Exception("Error during JSON decoding: $errorMessage");
        }

        return $decoded;
    }

    public function encode($value)
    {
        return json_encode($value);
    }
}
