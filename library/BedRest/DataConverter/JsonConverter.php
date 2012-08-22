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

namespace BedRest\DataConverter;

/**
 * JsonConverter
 *
 * Simple converter for JSON output.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class JsonConverter implements DataConverter
{
    public function decode($value)
    {
        $decoded = json_decode($value, true);

        // check if an error occurred during decoding
        if ($error = json_last_error()) {
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $errorMessage = 'Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $errorMessage = 'Invalid or malformed JSON';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $errorMessage = 'Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $errorMessage = 'Syntax error, malformed JSON';
                break;
                default:
                    $errorMessage = '';
                break;
            }

            throw new Exception("Error during JSON deocding: $errorMessage");
        }

        return $decoded;
    }

    public function encode($value)
    {
        return json_encode($value);
    }
}

