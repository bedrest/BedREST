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
 * MediaTypeList
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class MediaTypeList extends AbstractList
{
    /**
     * The list of media types.
     * @var array
     */
    protected $mediaTypes = array();

    /**
     * Constructor.
     * Takes a list of media types, either as a full string or an array of strings.
     * @param mixed $list
     */
    public function __construct($list)
    {
        if (!is_array($list)) {
            $list = explode(',', $list);
        }

        $mediaTypes = $this->parse($list);
        $this->mediaTypes = $this->sort($mediaTypes);
    }

    /**
     * Parses a list of media types out into a normalised structure.
     * @param  array $list
     * @return array
     */
    protected function parse(array $list)
    {
        $parsed = array();

        foreach ($list as $item) {
            $item = explode(';', trim($item));

            $entry = array(
                'media_range' => array_shift($item),
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
     * @return array
     */
    public function getMediaTypes()
    {
        return $this->mediaTypes;
    }

    /**
     * Returns the best format out of a list of supplied formats.
     * @param  array          $formats
     * @return string|boolean
     */
    public function getBestMatch(array $formats)
    {
        foreach ($this->mediaTypes as $item) {
            if (in_array($item['media_range'], $formats)) {
                return $item['media_range'];
            }
        }

        return false;
    }
}
