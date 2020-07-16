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

use Ytake\KsqlClient\Entity\FieldInfo;
use Ytake\KsqlClient\Entity\SchemaInfo;

/**
 * Trait RecursiveFieldTrait
 */
trait RecursiveFieldTrait
{
    /**
     * @param array $rows
     *
     * @return array|null
     */
    private function parentFields(array $rows): ?array
    {
        $fields = [];
        foreach ($rows as $row) {
            $fields[] = $this->generateSchemaInfo($row);
        }
        return $fields;
    }

    /**
     * @param array|null $rows
     *
     * @return FieldInfo|null
     */
    private function recursiveFields(?array $rows): ?array
    {
        if (!is_null($rows)) {
            $fields = [];
            foreach ($rows as $row) {
                if (!is_null($row['schema'])) {
                    $fields[] = $this->generateSchemaInfo($row);
                }
                if (isset($row['memberSchema'])) {
                    if (!is_null($row['memberSchema'])) {
                        $fields[] = new FieldInfo(
                            $row['name'],
                            $this->recursiveSchemaInfo($row['memberSchema'] ?? [])
                        );
                    }
                }
            }
            return $fields;
        }
        return null;
    }

    /**
     * @param array|null $rows
     *
     * @return SchemaInfo|null
     */
    private function recursiveSchemaInfo(?array $rows): ?SchemaInfo
    {
        if (is_null($rows)) {
            return null;
        }
        return new SchemaInfo($rows['type'], $this->recursiveFields($rows['fields']), $rows['memberSchema']);
    }

    /**
     * @param array $row
     *
     * @return FieldInfo
     */
    private function generateSchemaInfo(array $row): FieldInfo
    {
        $schema = $row['schema'];
        return new FieldInfo(
            $row['name'],
            new SchemaInfo(
                $schema['type'],
                $this->recursiveFields($schema['fields']),
                $this->recursiveSchemaInfo($schema['memberSchema'])
            )
        );
    }
}
