# Elastic-apm-php-agent

PHP Agent for Elastic APM With **Intake API v2 Support** + **UDP Support** + **ProtoBuf**

# Examples 

## Using PSR7Middleware
Project is using PSR7 Standard to wrap the whole app or any specific route you may need using PSR7Middleware Class

```
<?php
$app = new \Slim\App();
$container = $app->getContainer();

$settings = [
    'defaultConnector'  => 'udp', // Send Data Using UDP or TCP 
    'appName'           => 'Sample APP',
    'appVersion'        => '2.0.0',
    'active'            => true && PHP_SAPI !== 'cli',
    'serverUrl'         => 'http://your_elastic_apm_server_ip.com/intake/v2/events',
    'secretToken'       => null,
    'host'              => 'your_elastic_apm_server.com', // to disable dns resolve
    'hostname'          => gethostname(),
    'timeout'           => 5, // seconds
    'apmVersion'        => 'v2', 
    'env'               => ['DOCUMENT_ROOT', 'REMOTE_ADDR'],
    'cookies'           => [],
    'httpClient'        => [],
    'environment'       => 'development',
    'backtraceLimit'    => 0,
    'udpAgentIP'        => '144.22.22.22', // Go UDP Sidecar IP Address
    'udpAgentPort'      => 1113, // GO UDP Sidecar Port
    'udpUseProto'       => true,  // Use Protobuf Transport i GO UDP Sidecar
    'isDockerContainer' => false,
    'containerIdEnv'    => 'CONTAINER_ID',
    'isKubernetes'      => false,
    'kuberNamespaceEnv' => 'MY_POD_NAMESPACE',
    'kuberPodNameEnv'   => 'MY_POD_NAME',
    'kuberPodUidEnv'    => 'MY_POD_UID' ,
    'kuberNodeNameEnv'  => 'MY_NODE_NAME' ,
    'cleanup_rules' => [], // Cleanup naming
];

$apmAgent = new \Subzerobo\ElasticApmPhpAgent\ApmAgent($settings);

$mw = new \Subzerobo\ElasticApmPhpAgent\Middlewares\PSR7Middleware($container, $apmAgent)

$app->add($mw);

```
## Wrapping Redis/Mysql/other resources Weapper

[ActionWrappers](https://github.com/subzerobo/elastic-apm-php-agent/tree/master/src/ActionWrappers)



