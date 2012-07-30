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

namespace BedRest\DataMapper;

/**
 * DataMapper
 * 
 * @author Geoff Adams <geoff@dianode.net>
 */
interface DataMapper
{
    /**
     * Maps data into a resource.
     * @param object $resource Entity to map the data into.
     * @param mixed $data Data to be mapped.
     */
    public function map($resource, $data);
    
    /**
     * Reverse maps data from a resource into the desired format.
     * @param mixed $resource Entity to map data from.
     * @return mixed
     */
    public function reverse($resource);
    
    /**
     * Reverse maps generic data structures into the desired format.
     * @param mixed $data
     * @return mixed
     */
    public function reverseGeneric($data);
}