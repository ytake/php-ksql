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

namespace Ytake\KsqlClient\Computation;

use InvalidArgumentException;
use function explode;
use function sprintf;

/**
 * Class CommandId
 */
final class CommandId
{
    /** @var string */
    private $type;

    /** @var string */
    private $entity;

    /** @var string */
    private $action;

    /**
     * @param string $type
     * @param string $entity
     * @param string $action
     */
    public function __construct(
        string $type,
        string $entity,
        string $action
    ) {
        $this->type = $type;
        $this->entity = $entity;
        $this->action = $action;
    }

    /**
     * @param string $fromString
     *
     * @return CommandId
     */
    public static function fromString(string $fromString): CommandId
    {
        $split = explode('/', $fromString);
        if (count($split) != 3) {
            throw new InvalidArgumentException("Expected a string of the form <type>/<entity>/<action>");
        }

        return new CommandId($split[0], $split[1], $split[2]);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            "%s/%s/%s",
            strtolower($this->type),
            $this->entity,
            strtolower($this->action)
        );
    }
}
