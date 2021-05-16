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
 * Class KafkaTopicInfo
 */
class KafkaTopicInfo implements EntityInterface
{
    /**
     * @param string $name
     * @param bool $registered
     * @param array<int> $replicaInfo
     * @param int $consumerCount
     * @param int $consumerGroupCount
     */
    public function __construct(
        private string $name,
        private bool $registered,
        private array $replicaInfo,
        private int $consumerCount,
        private int $consumerGroupCount
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
     * @return bool
     */
    public function getRegistered(): bool
    {
        return $this->registered;
    }

    /**
     * @return array<int>
     */
    public function getReplicaInfo(): array
    {
        return $this->replicaInfo;
    }

    /**
     * @return int
     */
    public function getConsumerCount(): int
    {
        return $this->consumerCount;
    }

    /**
     * @return int
     */
    public function getConsumerGroupCount(): int
    {
        return $this->consumerGroupCount;
    }
}
