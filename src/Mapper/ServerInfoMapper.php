<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\EntityInterface;
use Istyle\KsqlClient\Entity\ServerInfo;

/**
 * Class ServerInfoMapper
 */
final class ServerInfoMapper extends AbstractMapper
{
    /**
     * @return EntityInterface|ServerInfo
     */
    public function result(): EntityInterface
    {
        $decode = \GuzzleHttp\json_decode(
            $this->response->getBody()->getContents(), true
        );
        $serverInfo = $decode['KsqlServerInfo'];

        return new ServerInfo(
            $serverInfo['version'],
            $serverInfo['kafkaClusterId'] ?? '',
            $serverInfo['ksqlServiceId'] ?? ''
        );
    }
}
