<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 16:06
 */
namespace Subzerobo\ElasticApmPhpAgent\Misc;

use Subzerobo\ElasticApmPhpAgent\Exception\MissingAppNameException;

class Config
{
    /**
     * Holds the APM Agent Configuration Data
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     *
     * @param array $config
     *
     * @throws MissingAppNameException
     */
    public function __construct(array $config)
    {
        if (isset($config['appName']) === false) {
            throw new MissingAppNameException();
        }

        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * Get Config Value
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed: value | null
     */
    public function get(string $key, $default = null)
    {
        return ($this->config[$key]) ?? $default;
    }

    /**
     * Get all Agent Configuration as array
     * @return array
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-08 16:35
     */
    public function toArray() {
        return $this->config;
    }

    private function getDefaultConfig() : array
    {
        return [
            'defaultConnector'  => 'tcp', // Send Data Using UDP
            'serverUrl'         => 'http://127.0.0.1:8200',
            'secretToken'       => null,
            'hostname'          => gethostname(),
            'host'              => null,
            'active'            => true,
            'timeout'           => 5,
            'apmVersion'        => 'v2',
            'env'               => [],
            'cookies'           => [],
            'httpClient'        => [],
            'environment'       => 'development',
            'backtraceLimit'    => 0,
            'udpAgentIP'        => '127.0.0.1',
            'udpAgentPort'      => 8300,
            'udpUseProto'       => false,  // User Protobuf Transport
            'isDockerContainer' => false,
            'containerIdEnv'    => 'CONTAINER_ID',
            'isKubernetes'      => false,
            'kuberNamespaceEnv' => 'MY_POD_NAMESPACE',
            'kuberPodNameEnv'   => 'MY_POD_NAME',
            'kuberPodUidEnv'    => 'MY_POD_UID' ,
            'kuberNodeNameEnv'  => 'MY_NODE_NAME' ,
            'cleanup_rules' => [],
            'max_level'         => 5, // Max Trace Log Level
        ];
    }
}