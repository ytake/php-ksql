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

namespace Ytake\KsqlClient\Query;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Ytake\KsqlClient\Mapper\ResultInterface;
use Ytake\KsqlClient\Mapper\StreamMapper;

/**
 * Class StreamQuery
 */
final class Stream extends AbstractStreamQuery
{
    /**
     * {@inheritdoc}
     */
    public function httpMethod(): string
    {
        return RequestMethodInterface::METHOD_POST;
    }

    /**
     * {@inheritdoc}
     */
    public function uri(): string
    {
        return 'query';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'ksql' => $this->query,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function queryResult(
        ResponseInterface $response
    ): ResultInterface {
        $stream = new StreamMapper($response);
        $stream->setCallback($this->callback);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperties(): bool
    {
        return true;
    }
}
