<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-14
 * Time: 13:08
 */

namespace Subzerobo\ElasticApmPhpAgent\Connectors\Clients;


use Subzerobo\ElasticApmPhpAgent\Exceptions\SocketCreateFailedException;

class UDPClient
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var int
     */
    protected $port;

    /**
     * UDPClient constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->ip = $config['ip'];
        $this->port = $config['port'];
    }

    /**
     * @param $payload
     *
     * @return int
     * @throws SocketCreateFailedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-03-17 14:18
     */
    public function send($payload) {
        if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
            return socket_sendto($socket, $payload, strlen($payload), 0, $this->ip, $this->port);
        } else {
            throw new SocketCreateFailedException("");
        }
    }
}