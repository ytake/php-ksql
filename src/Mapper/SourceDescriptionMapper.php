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
use Ytake\KsqlClient\Entity\SourceDescription;
use Ytake\KsqlClient\Entity\SourceDescriptionEntity;
use Ytake\KsqlClient\Entity\RunningQuery;
use Ytake\KsqlClient\Entity\EntityQueryId;
use Ytake\KsqlClient\Query\QueryId;

/**
 * Class SourceDescriptionMapper
 */
final class SourceDescriptionMapper implements ResultInterface
{
    use RecursiveFieldTrait;

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
        $description = $this->rows['sourceDescription'];
        $sourceDescription = new SourceDescription(
            $description['name'],
            $this->getQueries($description['readQueries'] ?? []),
            $this->getQueries($description['writeQueries'] ?? []),
            $this->parentFields($description['fields'] ?? []),
            $description['type'],
            $description['key'],
            $description['timestamp'],
            $description['statistics'],
            $description['errorStats'],
            $description['extended'],
            $description['format'],
            $description['topic'],
            $description['partitions'],
            $description['replication']
        );
        return new SourceDescriptionEntity(
            $this->rows['statementText'],
            $sourceDescription
        );
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    private function getQueries(array $rows): array
    {
        $queries = [];
        foreach ($rows as $row) {
            $queries[] = new RunningQuery(
                $row['queryString'],
                $row['sinks'],
                new EntityQueryId(
                    new QueryId($row['id'])
                )
            );
        }
        return $queries;
    }
}
