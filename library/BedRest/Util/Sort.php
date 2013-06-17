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

namespace BedRest\Util;

/**
 * Sort
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Sort
{
    /**
     * Sorts the parsed accept header using a merge sort algorithm, in order to maintain order as presented in the
     * header.
     *
     * All of this code has been taken from http://www.php.net/manual/en/function.usort.php#38827, some changes made
     * are purely for readability and code style.
     *
     * @param array  $array
     * @param string $comparator
     *
     * @return null
     */
    public static function mergeSort(array &$array, $comparator = 'strcmp')
    {
        // optimisation, no sorting required on arrays with 0 or 1 items
        if (count($array) < 2) {
            return;
        }

        // split the array in half
        $halfway = count($array) / 2;
        $array1 = array_slice($array, 0, $halfway);
        $array2 = array_slice($array, $halfway);

        // use recursion to sort both halves
        self::mergeSort($array1, $comparator);
        self::mergeSort($array2, $comparator);

        // optimisation, if the end of $array1 is less than the start of $array2, append and return
        if (call_user_func($comparator, end($array1), $array2[0]) < 1) {
            $array = array_merge($array1, $array2);

            return;
        }

        // merge the arrays into a single array
        $array = array();
        $ptr1 = $ptr2 = 0;

        while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
            if (call_user_func($comparator, $array1[$ptr1], $array2[$ptr2]) < 1) {
                $array[] = $array1[$ptr1++];
            } else {
                $array[] = $array2[$ptr2++];
            }
        }

        // merge the remainder
        while ($ptr1 < count($array1)) {
            $array[] = $array1[$ptr1++];
        }

        while ($ptr2 < count($array2)) {
            $array[] = $array2[$ptr2++];
        }
    }
}
