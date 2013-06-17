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

        $result = new NegotiatedResult();
        $result->contentType = $contentType;
        $result->content = $this->encode($content, $contentType);

        return $result;
    }

    /**
     * @todo This should use a service locator.
     *
     * @param string $contentType
     *
     * @return mixed
     */
    protected function getConverter($contentType)
    {
        if (!isset($this->supportedMediaTypes[$contentType])) {
            throw new Exception("No converter found for content type '$contentType'");
        }

        $converterClass = $this->supportedMediaTypes[$contentType];

        return new $converterClass;
    }

    /**
     * @param mixed  $content
     * @param string $contentType
     *
     * @return mixed
     */
    public function encode($content, $contentType)
    {
        $converter = $this->getConverter($contentType);

        return $converter->encode($content);
    }

    /**
     * @param mixed  $content
     * @param string $contentType
     *
     * @return mixed
     */
    public function decode($content, $contentType)
    {
        $converter = $this->getConverter($contentType);

        return $converter->decode($content);
    }
}
