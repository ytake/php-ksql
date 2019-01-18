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

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\EntityInterface;
use Istyle\KsqlClient\Entity\KafkaTopicInfo;
use Istyle\KsqlClient\Entity\KafkaTopics;

/**
 * Class KafkaTopicMapper
 */
final class KafkaTopicMapper implements ResultInterface
{
    /** @var array */
    protected $rows;

    /**
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return EntityInterface
     */
    public function result(): EntityInterface
    {
        $topics = [];
        foreach ($this->rows['topics'] as $topic) {
            $topics[] = new KafkaTopicInfo(
                $topic['name'],
                $topic['registered'],
                (is_array($topic['replicaInfo'])) ? $topic['replicaInfo'] : [$topic['replicaInfo']],
                $topic['consumerCount'],
                $topic['consumerGroupCount']
            );
        }
        return new KafkaTopics($this->rows['statementText'], $topics);
    }
}
