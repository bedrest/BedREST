<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
