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

/**
 * Class KsqlStatementErrorMessage
 */
class KsqlStatementErrorMessage extends AbstractKsql
{
    /** @var KsqlErrorMessage */
    private $errorMessage;

    /**
     * @param string           $statementText
     * @param KsqlErrorMessage $errorMessage
     */
    public function __construct(string $statementText, KsqlErrorMessage $errorMessage)
    {
        parent::__construct($statementText);
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getStatementText(): string
    {
        return $this->statementText;
    }

    /**
     * @return KsqlErrorMessage
     */
    public function getErrorMessage(): KsqlErrorMessage
    {
        return $this->errorMessage;
    }
}
