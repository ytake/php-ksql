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
 * Class QueryDescription
 */
final class QueryDescription implements EntityInterface
{
    /**
     * @param EntityQueryId $entityQueryId
     * @param string $statementText
     * @param array $fields
     * @param array $sources
     * @param array $sinks
     * @param string $topology
     * @param string $executionPlan
     * @param array $overriddenProperties
     */
    public function __construct(
        private EntityQueryId $entityQueryId,
        private string $statementText,
        private array $fields,
        private array $sources,
        private array $sinks,
        private string $topology,
        private string $executionPlan,
        private array $overriddenProperties
    ) {}

    /**
     * @return EntityQueryId
     */
    public function getEntityQueryId(): EntityQueryId
    {
        return $this->entityQueryId;
    }

    /**
     * @return string
     */
    public function getStatementText(): string
    {
        return $this->statementText;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @return array
     */
    public function getSinks(): array
    {
        return $this->sinks;
    }

    /**
     * @return string
     */
    public function getTopology(): string
    {
        return $this->topology;
    }

    /**
     * @return string
     */
    public function getExecutionPlan(): string
    {
        return $this->executionPlan;
    }

    /**
     * @return array
     */
    public function getOverriddenProperties(): array
    {
        return $this->overriddenProperties;
    }
}
