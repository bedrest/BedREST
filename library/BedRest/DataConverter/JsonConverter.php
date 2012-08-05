<?php

namespace BedRest\DataConverter;

/**
 * JsonConverter
 *
 * Simple converter for JSON output.
 * 
 * @author Geoff Adams <geoff@dianode.net>
 */
class JsonConverter implements DataConverter
{
    public function decode($value)
    {
        $decoded = json_decode($value, true);
        
        // check if an error occurred during decoding
        if ($error = json_last_error()) {
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $errorMessage = 'Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $errorMessage = 'Invalid or malformed JSON';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $errorMessage = 'Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $errorMessage = 'Syntax error, malformed JSON';
                break;
                default:
                    $errorMessage = '';
                break;
            }

            throw new DataConversionException("Error during JSON deocding: $errorMessage");
        }
        
        return $decoded;
    }

    public function encode($value)
    {
        return json_encode($value);
    }
}

