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

use Istyle\KsqlClient\Entity\AbstractKsql;
use Istyle\KsqlClient\Entity\Description;
use Istyle\KsqlClient\Entity\EntityInterface;
use Istyle\KsqlClient\Entity\FieldSchema;
use Istyle\KsqlClient\Entity\KafkaTopicInfo;
use Istyle\KsqlClient\Entity\KafkaTopics;
use Istyle\KsqlClient\Entity\KsqlCollection;
use Istyle\KsqlClient\Entity\KsqlErrorMessage;
use Istyle\KsqlClient\Entity\KsqlStatementErrorMessage;
use Istyle\KsqlClient\Entity\Queries;
use Istyle\KsqlClient\Entity\RunningQuery;
use Istyle\KsqlClient\Exception\UnknownJsonObjectsException;

/**
 * Class KsqlResult
 */
class KsqlMapper extends AbstractMapper
{
    /**
     * @return EntityInterface|KsqlCollection
     */
    public function result(): EntityInterface
    {
        $decode = \GuzzleHttp\json_decode(
            $this->response->getBody()->getContents(), true
        );
        $collect = new KsqlCollection();
        foreach ($decode as $row) {
            $collect->addKsql($this->detectEntity($row));
        }

        return $collect;
    }

    /**
     * @param array $row
     *
     * @return AbstractKsql
     */
    protected function detectEntity(array $row): AbstractKsql
    {
        $type = $row['@type'] ?? '';
        if($type === '') {
            throw new UnknownJsonObjectsException('Unknown json objects.');
        }
        if (isset($row['queries'])) {
            $queries = [];
            foreach ($row['queries']['queries'] as $query) {
                $queries[] = new RunningQuery(
                    $query['statementText'],
                    $query['sinks'],
                    $query['id']
                );
            }

            return new Queries($row['queries']['statementText'], $queries);
        }
        if (isset($row['description'])) {
            $read = $write = $schema = [];
            foreach ($row['description']['readQueries'] as $query) {
                $read[] = new RunningQuery(
                    $query['statementText'],
                    $query['sinks'],
                    $query['id']
                );
            }
            foreach ($row['description']['writeQueries'] as $query) {
                $write[] = new RunningQuery(
                    $query['statementText'],
                    $query['sinks'],
                    $query['id']
                );
            }
            foreach ($row['description']['schema'] as $query) {
                $schema[] = new FieldSchema(
                    $query['name'],
                    $query['type']
                );
            }
            $description = $row['description'];

            return new Description(
                $row['description']['statementText'],
                $description['name'],
                $read,
                $write,
                $schema,
                $description['type'],
                $description['key'],
                $description['timestamp'],
                $description['statistics'],
                $description['errorStats'],
                $description['extended'],
                $description['replication'],
                $description['partitions']
            );
        }
        if (isset($row['error'])) {
            if (isset($row['error']['errorMessage'])) {
                $errorMessage = $row['error']['errorMessage'];

                return new KsqlStatementErrorMessage(
                    $row['error']['statementText'],
                    new KsqlErrorMessage($errorMessage['message'], $errorMessage['stackTrace'])
                );
            }
        }
        if ($type === 'kafka_topics') {
            $topics = [];
            foreach ($row['topics'] as $topic) {
                $topics[] = new KafkaTopicInfo(
                    $topic['name'],
                    $topic['registered'],
                    (is_array($topic['replicaInfo'])) ? $topic['replicaInfo'] : [$topic['replicaInfo']],
                    $topic['consumerCount'],
                    $topic['consumerGroupCount']
                );
            }

            return new KafkaTopics($row['statementText'], $topics);
        }
    }
}
