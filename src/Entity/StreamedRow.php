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

namespace Istyle\KsqlClient\Entity;

use Istyle\KsqlClient\GenericRow;

/**
 * Class StreamedRow
 */
final class StreamedRow implements EntityInterface
{
    /** @var array */
    private $rows = [];

    /** @var KsqlErrorMessage */
    private $ksqlErrorMessage;

    /**
     * @param array            $rows
     * @param KsqlErrorMessage $ksqlErrorMessage
     */
    public function __construct(array $rows, KsqlErrorMessage $ksqlErrorMessage)
    {
        $this->rows = $rows;
        $this->ksqlErrorMessage = $ksqlErrorMessage;
    }

    /**
     * @return GenericRow
     */
    public function getRow(): GenericRow
    {
        return new GenericRow($this->rows);
    }

    /**
     * @return KsqlErrorMessage
     */
    public function getKsqlErrorMessage(): KsqlErrorMessage
    {
        return $this->ksqlErrorMessage;
    }
}
