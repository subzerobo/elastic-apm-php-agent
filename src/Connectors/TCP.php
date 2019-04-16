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
use Subzerobo\ElasticApmPhpAgent\Misc\Config;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Payload;

class TCP
{
    /**
     * Agent Config
     *
     * @var Config
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    /**
     * Connector constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->configureHttpClient();
    }

    /**
     * Create and configure the HTTP client
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-03-17 13:35
     */
    private function configureHttpClient()
    {
        $httpClientDefaults = [
            'timeout' => $this->config->get('timeout'),
        ];
        $httpClientConfig = $this->config->get('httpClient') ?? [];
        $this->client = new Client(array_merge($httpClientDefaults, $httpClientConfig));
    }

    /**
     * @param Payload $payload
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-14 11:31
     */
    public function send(Payload $payload) : bool
    {
        $body = $payload->createPayloadNDJson();
        $request = new Request(
            'POST',
            $this->getEndpoint(),
            $this->getRequestHeaders(),
            $body
        );

        $response = $this->client->send($request);
        return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
    }

    /**
     * Get the Endpoint URI of the APM Intake API V2
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-03-17 13:41
     */
    private function getEndpoint() : string
    {
        return $this->config->get('serverUrl');
    }

    /**
     * Get the Headers for the POST Request
     *
     * @return array
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-03-17 13:41
     */
    private function getRequestHeaders() : array
    {
        // Default Headers Set
        $headers = [
            'Content-Type' => 'application/x-ndjson',
            'User-Agent'   => sprintf('elasticapm-php/%s', ApmAgent::VERSION),
            'Host'         => $this->config->get('host')
        ];

        // Add Secret Token to Header
        if ($this->config->get('secretToken') !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config->get('secretToken'));
        }

        return $headers;
    }

}