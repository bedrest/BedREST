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

namespace BedRest\Content\Negotiation;

/**
 * QualityFactorList
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
abstract class QualityFactorList
{
    protected $items;

    /**
     * Constructor.
     * Takes a list of items, either as an array or a comma-delimited string.
     *
     * @param mixed $items
     */
    public function __construct($items)
    {
        if (!is_array($items)) {
            $items = explode(',', $items);
        }

        $items = $this->parse($items);
        $this->items = $this->sort($items);
    }

    /**
     * Parses an array of items out into a normalised structure.
     *
     * @param  array $data
     * @return array
     */
    abstract protected function parse(array $data);

    /**
     * Sorts a list by the quality comparator.
     *
     * @param  array $list
     * @return array
     */
    protected function sort(array $list)
    {
        // @todo Take account of specificity with wildcard media ranges
        // (see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html)
        \BedRest\Util\Sort::mergeSort($list, array($this, 'qualityFactorComparator'));

        return $list;
    }

    /**
     * Comparator function for sorting a list of arrays by their 'q' factor.
     *
     * @param  array   $mediaType1
     * @param  array   $mediaType2
     * @return integer
     */
    public function qualityFactorComparator($mediaType1, $mediaType2)
    {
        $diff = $mediaType2['q'] - $mediaType1['q'];

        // doesn't cope too well with values 1 > v > -1, so make sure we return simple integers
        if ($diff > 0) {
            $diff = 1;
        } elseif ($diff < 0) {
            $diff = -1;
        }

        return $diff;
    }
}
