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

namespace Ytake\KsqlClient\Properties;

use InvalidArgumentException;
use Ytake\KsqlClient\Config\ConsumerConfig;

use function array_merge;
use function sprintf;
use function strtolower;

/**
 * Class LocalPropertyValidator
 */
class LocalPropertyValidator implements PropertyValidatorInterface
{
    /** @var string[] */
    private array $immProperties = [
        'bootstrap.servers',
        'ksql.extension.dir',
        'ksql.query.persistent.active.limit'
    ];

    /**
     * @param array $immProperties
     */
    public function __construct(
        array $immProperties = []
    ) {
        $this->immProperties = array_merge($this->immProperties, $immProperties);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(
        string $name,
        mixed $value
    ): void {
        foreach ($this->immProperties as $property) {
            if ($name === $property) {
                throw new InvalidArgumentException(
                    sprintf("Cannot override property '%s'", $name)
                );
            }
        }
        if (ConsumerConfig::AUTO_OFFSET_RESET_CONFIG === $name) {
            $this->validateConsumerOffsetResetConfig($value);
        }
    }

    /**
     * @param string $value
     */
    private function validateConsumerOffsetResetConfig(string $value): void
    {
        if ('none' === strtolower($value)) {
            throw new InvalidArgumentException("'none' is not valid for this property within KSQL");
        }
    }
}
