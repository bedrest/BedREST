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
