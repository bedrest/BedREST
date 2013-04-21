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
    public $supportedMediaTypes = array();

    /**
     * Retrieves the list of supported media types for negotiation.
     *
     * @return array
     */
    public function getSupportedMediaTypes()
    {
        return $this->supportedMediaTypes;
    }

    /**
     * Sets the list of supported media types for negotiation.
     *
     * @param array $mediaTypes
     *
     * @throws \BedRest\Content\Negotiation\Exception
     */
    public function setSupportedMediaTypes(array $mediaTypes)
    {
        foreach ($mediaTypes as $mediaType => $converterClass) {
            if (!is_string($mediaType)) {
                throw new Exception('Media type must be a string.');
            }

            if (!is_string($converterClass)) {
                throw new Exception('Converter class name must be a string.');
            }
        }

        $this->supportedMediaTypes = $mediaTypes;
    }

    /**
     * Negotiates content based on a set of input criteria.
     *
     * @param mixed                                      $content
     * @param \BedRest\Content\Negotiation\MediaTypeList $mediaTypeList
     *
     * @throws \BedRest\Content\Negotiation\Exception
     * @return \BedRest\Content\Negotiation\NegotiatedResult
     */
    public function negotiate($content, MediaTypeList $mediaTypeList)
    {
        $contentType = $mediaTypeList->getBestMatch(array_keys($this->supportedMediaTypes));
        if (!$contentType) {
            throw new Exception('A suitable Content-Type could not be found.');
        }

        // TODO: this should use a service locator of some description
        $converterClass = $this->supportedMediaTypes[$contentType];
        $converter = new $converterClass;

        $result = new NegotiatedResult();
        $result->contentType = $contentType;
        $result->content = $converter->encode($content);

        return $result;
    }
}
