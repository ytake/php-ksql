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

use Istyle\KsqlClient\Entity\EntityQueryId;
use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Entity\Queries;
use Istyle\KsqlClient\Entity\RunningQuery;
use Istyle\KsqlClient\Query\QueryId;

/**
 * Class QueriesMapper
 */
class QueriesMapper implements ResultInterface
{
    /**
     * @param array $rows
     *
     * @return KsqlEntity
     */
    public function result(array $rows): KsqlEntity
    {
        $queries = [];
        foreach ($rows['queries'] as $row) {
            $queries[] = new RunningQuery(
                $row['queryString'],
                $row['sinks'],
                new EntityQueryId(
                    new QueryId($row['id'])
                )
            );
        }
        return new Queries($rows['statementText'], $queries);
    }
}
