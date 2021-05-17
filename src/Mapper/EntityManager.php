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

namespace Ytake\KsqlClient\Mapper;

use Ytake\KsqlClient\Entity\EntityInterface;
use Ytake\KsqlClient\Exception\UnknownJsonObjectsException;
use Ytake\KsqlClient\Exception\UnrecognizedPropertyException;

use function array_key_exists;

/**
 * Class EntityManager
 */
final class EntityManager
{
    /** @var array<string, ResultInterface> */
    private array $map = [
        'kafka_topics' => KafkaTopicMapper::class,
        'streams' => StreamsListMapper::class,
        'generic_error' => KsqlErrorMapper::class,
        'statement_error' => KsqlStatementErrorMapper::class,
        'tables' => TablesListMapper::class,
        'queries' => QueriesMapper::class,
        'properties' => PropertiesMapper::class,
        'sourceDescription' => SourceDescriptionMapper::class,
        'queryDescription' => QueryDescriptionMapper::class,
        'currentStatus' => CurrentStatusMapper::class,
    ];

    /**
     * @param array $row
     */
    public function __construct(
        private array $row
    ) {
    }

    /**
     * @return EntityInterface
     */
    public function map(): EntityInterface
    {
        $type = $this->row['@type'] ?? '';
        if (array_key_exists($type, $this->map)) {
            /** @var ResultInterface $mapper */
            $mapper = new $this->map[$type]($this->row);

            return $mapper->result();
        }
        if ($type !== '') {
            throw new UnrecognizedPropertyException(sprintf('Unrecognized field %s.', $type));
        }
        throw new UnknownJsonObjectsException('Unknown json objects.');
    }
}
