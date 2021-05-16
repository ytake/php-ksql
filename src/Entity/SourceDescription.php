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

namespace Ytake\KsqlClient\Entity;

/**
 * Class SourceDescription
 */
final class SourceDescription implements EntityInterface
{
    /**
     * @param string $name
     * @param array $readQueries
     * @param array $writeQueries
     * @param array $fields
     * @param string $type
     * @param string $key
     * @param string $timestamp
     * @param string $statistics
     * @param string $errorStats
     * @param bool $extended
     * @param string $format
     * @param string $topic
     * @param int $partitions
     * @param int $replication
     */
    public function __construct(
        private string $name,
        private array $readQueries,
        private array $writeQueries,
        private array $fields,
        private string $type,
        private string $key,
        private string $timestamp,
        private string $statistics,
        private string $errorStats,
        private bool $extended,
        private string $format,
        private string $topic,
        private int $partitions,
        private int $replication
    ) {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return RunningQuery[]
     */
    public function getReadQueries(): array
    {
        return $this->readQueries;
    }

    /**
     * @return RunningQuery[]
     */
    public function getWriteQueries(): array
    {
        return $this->writeQueries;
    }

    /**
     * @return FieldInfo[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getStatistics(): string
    {
        return $this->statistics;
    }

    /**
     * @return string
     */
    public function getErrorStats(): string
    {
        return $this->errorStats;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @return bool
     */
    public function isExtended(): bool
    {
        return $this->extended;
    }

    /**
     * @return int
     */
    public function getPartitions(): int
    {
        return $this->partitions;
    }

    /**
     * @return int
     */
    public function getReplication(): int
    {
        return $this->replication;
    }
}
