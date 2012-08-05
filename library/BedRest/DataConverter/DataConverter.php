<?php

namespace BedRest\DataConverter;

/**
 * DataConverter
 * 
 * @author Geoff Adams <geoff@dianode.net>
 */
interface DataConverter
{
    public function encode($value);
    
    public function decode($value);
}

