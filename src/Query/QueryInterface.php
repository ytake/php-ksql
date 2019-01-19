<?php
declare(strict_types=1);

/**
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

namespace Istyle\KsqlClient\Query;

use Istyle\KsqlClient\Mapper\ResultInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface QueryInterface
 */
interface QueryInterface
{
    /**
     * @return string
     */
    public function httpMethod(): string;

    /**
     * request uri for ksql-server
     *
     * @return string
     */
    public function uri(): string;

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param ResponseInterface $response
     *
     * @return ResultInterface
     */
    public function queryResult(ResponseInterface $response): ResultInterface;

    /**
     * Property overrides to run the statements with.
     * for /ksql, /query
     * @return bool
     */
    public function hasProperties(): bool;
}
