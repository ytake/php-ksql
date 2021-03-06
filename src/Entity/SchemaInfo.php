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
 * Class SchemaInfo
 */
class SchemaInfo implements EntityInterface
{
    /** @var string */
    private $type;

    /** @var FieldInfo[]|null */
    private $fieldInfo;

    /** @var SchemaInfo|null */
    private $schemaInfo;

    /**
     * @param string           $type
     * @param FieldInfo[]|null $fieldInfo
     * @param SchemaInfo|null  $schemaInfo
     */
    public function __construct(
        string $type,
        ?array $fieldInfo,
        ?SchemaInfo $schemaInfo
    ) {
        $this->type = $type;
        $this->fieldInfo = $fieldInfo;
        $this->schemaInfo = $schemaInfo;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return FieldInfo[]|null
     */
    public function getFieldInfo(): ?array
    {
        return $this->fieldInfo;
    }

    /**
     * @return SchemaInfo|null
     */
    public function getSchemaInfo(): ?SchemaInfo
    {
        return $this->schemaInfo;
    }
}
