<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-14
 * Time: 10:52
 */

namespace Subzerobo\ElasticApmPhpAgent\Connectors;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Subzerobo\ElasticApmPhpAgent\ApmAgent;
use Subzerobo\ElasticApmPhpAgent\Exceptions\SocketCreateFailedException;
use Subzerobo\ElasticApmPhpAgent\Misc\Config;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Payload;

class UDP
{
    const UDP_USE_CONFIG_SETTINGS = 0;

    const UDP_USE_PROTOBUF = 1;

    const UDP_USE_NDJSON = 2;

    /**
     * Agent Config
     *
     * @var Config
     */
    private $config;

    /**
     * @var Client
     */
    private $udpClientConfig;

    /**
     * Connector constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }


    /**
     * Send UDP Payload
     *
     * @param Payload $payload
     * @param int     $mode
     *
     * @return bool
     * @throws SocketCreateFailedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-18 12:00
     */
    public function send(Payload $payload, int $mode = self::UDP_USE_CONFIG_SETTINGS) : bool
    {

        if ($mode == self::UDP_USE_CONFIG_SETTINGS) {
            $payloadString = ($this->config->get('udpUseProto')) ? $payload->createPayloadObj()->serializeToString() : $payload->createPayloadNDJson();
        }else{
            $payloadString = ($mode == self::UDP_USE_PROTOBUF) ? $payload->createPayloadObj()->serializeToString() : $payload->createPayloadNDJson();
        }

        $server_ip      = $this->getUDPServerIP();
        $server_port    = $this->getUDPServerPort();
        $result         = null;
        
        if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
                $result = socket_sendto($socket, $payloadString, strlen($payloadString), 0, $server_ip, $server_port);
        } else {
            throw new SocketCreateFailedException("can't create socket");
        }

        return $result;
    }

    /**
     * Get the UDP Endpoint IP
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-18 12:07
     */
    private function getUDPServerIP() : string
    {
        return $this->config->get('udpAgentIP');
    }

    /**
     * Get the UDP Endpoint Port
     *
     * @return int
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-18 12:07
     */
    private function getUDPServerPort() : int
    {
        // Default Headers Set
        return $this->config->get('udpAgentPort');

    }

}