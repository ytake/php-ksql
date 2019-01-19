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

use Istyle\KsqlClient\Computation\CommandId;
use Istyle\KsqlClient\Entity\CommandStatus;
use Istyle\KsqlClient\Entity\CommandStatusEntity;
use Istyle\KsqlClient\Entity\EntityInterface;

use function intval;

/**
 * Class CurrentStatusMapper
 */
final class CurrentStatusMapper implements ResultInterface
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
     * {@inheritdoc}
     */
    public function result(): EntityInterface
    {
        $commandStatus = $this->rows['commandStatus'];

        return new CommandStatusEntity(
            $this->rows['statementText'],
            CommandId::fromString($this->rows['commandId']),
            new CommandStatus(
                $commandStatus['message'],
                $commandStatus['status']
            ),
            isset($this->rows['commandSequenceNumber']) ? intval($this->rows['commandSequenceNumber']) : - 1
        );
    }
}
