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
 * EncodingList
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class EncodingList extends QualityFactorList
{
    /**
     * Parses a list of media types out into a normalised structure.
     *
     * @param  array $list
     * @return array
     */
    protected function parse(array $list)
    {
        $parsed = array();

        foreach ($list as $item) {
            $item = explode(';', trim($item));

            $entry = array(
                'encoding' => array_shift($item),
                'q' => 1
            );

            foreach ($item as $param) {
                $param = explode('=', $param);

                // ignore malformed params
                if (count($param) != 2) {
                    continue;
                }

                $entry[$param[0]] = $param[1];
            }

            $parsed[] = $entry;
        }

        return $parsed;
    }

    /**
     * Returns the full, ordered list of media types.
     *
     * @return array
     */
    public function getEncodings()
    {
        return $this->items;
    }

    /**
     * Returns the best format out of a list of supplied formats.
     *
     * @param  array          $formats
     * @return string|boolean
     */
    public function getBestMatch(array $formats)
    {
        foreach ($this->items as $item) {
            if (in_array($item['encoding'], $formats)) {
                return $item['encoding'];
            }
        }

        return false;
    }
}
