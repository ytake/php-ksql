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

/**
 * Class LocalProperties
 */
class LocalProperties
{
    /** @var array */
    private $props = [];

    /** @var PropertyValidatorInterface */
    private $validator;

    /**
     * @param array                      $props
     * @param PropertyValidatorInterface $validator
     */
    public function __construct(array $props, PropertyValidatorInterface $validator)
    {
        $this->props = $props;
        $this->validator = $validator;
    }

    /**
     * @param string $property
     * @param        $value
     */
    public function set(string $property, $value)
    {
        $this->validator->validate($property, $value);
        $this->props[$property] = $value;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function get(string $property)
    {
        if (isset($this->props[$property])) {
            return $this->props[$property];
        }
        return null;
    }

    /**
     * @param string $property
     */
    public function remove(string $property): void
    {
        if (isset($this->props[$property])) {
            unset($this->props[$property]);
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->props;
    }
}
