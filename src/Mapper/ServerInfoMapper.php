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

use GuzzleHttp\Utils;
use Ytake\KsqlClient\Entity\EntityInterface;
use Ytake\KsqlClient\Entity\ServerInfo;

/**
 * Class ServerInfoMapper
 */
final class ServerInfoMapper extends AbstractMapper
{
    /**
     * @return EntityInterface
     */
    public function result(): EntityInterface
    {
        $decode = Utils::jsonDecode(
            $this->response->getBody()->getContents(),
            true
        );
        $serverInfo = $decode['KsqlServerInfo'];

        return new ServerInfo(
            $serverInfo['version'],
            $serverInfo['kafkaClusterId'] ?? '',
            $serverInfo['ksqlServiceId'] ?? ''
        );
    }
}
