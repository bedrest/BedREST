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
 * Negotiator
 *
 * @author Geoff Adams <geoff@dianode.net>s
 */
class Negotiator
{
    /**
     * @var array
     */
    public $acceptedTypes = array();

    /**
     * Sets the list of supported Content-Types for negotiation.
     *
     * @return array
     */
    public function getAcceptedTypes()
    {
        return $this->acceptedTypes;
    }

    /**
     * Retrieves the list of supported Content-Types for negotiation.
     *
     * @param array $mediaTypes
     */
    public function setAcceptedTypes(array $mediaTypes)
    {
        $this->acceptedTypes = $mediaTypes;
    }

    /**
     * Negotiates content based on a set of input criteria.
     *
     * @param  \BedRest\Content\Negotiation\MediaTypeList    $mediaTypeList
     * @throws \BedRest\Content\Negotiation\Exception
     * @return \BedRest\Content\Negotiation\NegotiatedResult
     */
    public function negotiate(MediaTypeList $mediaTypeList)
    {
        $contentType = $mediaTypeList->getBestMatch($this->acceptedTypes);
        if (!$contentType) {
            throw new Exception('A suitable Content-Type could not be found.');
        }

        $result = new NegotiatedResult();
        $result->contentType = $contentType;

        return $result;
    }
}
