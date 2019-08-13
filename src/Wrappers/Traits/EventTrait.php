<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 16:36
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers\Traits;


use Protos\Context;
use Protos\User;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\MapField;
use Subzerobo\ElasticApmPhpAgent\Misc\UID;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventMetaData;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventSharedData;

trait EventTrait
{
    /**
     * UUID
     *ra
     * @var string
     */
    private $id;

    /**
     * Trace ID
     *
     * @var string
     */
    private $trace_id;

    /**
     * Error occurred on Timestamp
     *
     * @var int
     */
    private $timestamp;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EventMetaData
     */
    private $metaData;

    /**
     * @var EventSharedData
     */
    private $sharedData;

    /**
     *  Initialize context
     *
     * @param EventSharedData $sharedData
     *
     * @return Context
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 14:56
     */
    private function initContext(EventSharedData $sharedData) : Context
    {
        // Generate Random UUIDs
        $this->id = UID::Generate(16);       //Uuid::uuid4()->toString();
        $this->trace_id = $this->trace_id = $_SERVER['HTTP_ELASTIC_APM_TRACEPARENT'] ? : UID::Generate(16); //Uuid::uuid4()->toString();

        // Set Shared Context Variable for further use
        $this->sharedData = $sharedData;

        // Set Initialized Meta
        $this->metaData   = new EventMetaData();

        // Initialize Protobuf Empty Context
        $this->context = new Context();


        // Initialize Context Here
        $this->setResponseFromArray([
            'finished'     => true,
            'headers_sent' => true,
            'status_code'  => 200,]
        );

        // Fill User from sharedData
        $this->setUserContextFromArray();

        // Fill Tags from sharedData
        $this->setTags();

        // Fill Custom from sharedData
        $this->setCustom();

        // Fill Request from Request and sharedData
        $this->setRequest();

        return $this->context;
    }

    /**
     * Get the Event Id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Get the Event Id
     *
     * @return string
     */
    public function getTraceId() : string
    {
        return $this->trace_id;
    }

    public function setTimestamp() {
        /**
         * Set Timestamp in microseconds
         * @see https://github.com/elastic/apm-server/blob/v6.7.1/docs/spec/timestamp_epoch.json
         */
        $this->timestamp = microtime(true) * 1000000;
    }

    /**
     * Get the Event's Timestamp
     *
     * @return string
     */
    public function getTimestamp() : string
    {
        return $this->timestamp;
    }

    /**
     * Returns the Context Protobuf
     * 
     * @return Context
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 12:08
     */
    public function getContext() : Context {
        return $this->context;
    }

    // Context->User //
    /**
     * Set Meta data of User Context From array
     *
     * @param array $userArr
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 17:25
     */
    final public function setUserContextFromArray(array $userArr = [])
    {
        $user = array_merge($this->sharedData->getMetaUserInfo(), $userArr);
        $this->context->setUser(new User($user));
    }

    /**
     * Set Meta data of User Context
     *
     * @param User $userContext
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 18:12
     */
    final public function setUserContext(User $userContext)
    {
        $this->context->setUser($userContext);
    }

    // Context->Custom //
    /**
     * Set Meta data of Custom Context
     * @param array $customArr
     *
     * @throws \ErrorException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 17:39
     */
    final public function setCustom(array $customArr = [])
    {
        $customs = array_merge($this->sharedData->getContextSharedCustoms(), $customArr);
        $customMapField = new MapField(GPBType::STRING, GPBType::STRING);
        foreach ($customs as $key => $value) {
            $customMapField->offsetSet($key, $value);
        }
        $this->context->setCustom($customMapField);
    }

    // Context->Tags //
    /**
     * Set Meta data of Tags Context
     * @param array $tagsArr
     *
     * @throws \ErrorException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 17:39
     */
    final public function setTags(array $tagsArr = [])
    {
        $tags = array_merge($this->sharedData->getContextSharedTags(), $tagsArr);
        $tagsMapField = new MapField(GPBType::STRING, GPBType::STRING);
        foreach ($tags as $key => $value) {
            $tagsMapField->offsetSet($key, $value);
        }
        $this->context->setTags($tagsMapField);
    }

    // Context->Response //
    /**
     * Set Meta data of Context Response from Array
     *
     * @param array $responseArray
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 17:46
     */
    final public function setResponseFromArray(array $responseArray)
    {
        $this->context->setResponse(new Context\Response($responseArray));
    }

    /**
     * Set Meta data of Context Response
     *
     * @param Context\Response $response
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 17:47
     */
    final public function setResponse(Context\Response $response)
    {
        $this->context->setResponse($response);
    }

    // Context->Request //

    /**
     * Set Meta data of Context Request
     *
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 14:47
     */
    final public function setRequest() {
        $headers = getallheaders();
        $http_or_https = isset($_SERVER['HTTPS']) ? 'https' : 'http';

        $SERVER_PROTOCOL = $_SERVER['SERVER_PROTOCOL'] ?? '';

        $contextRequestArr = [
            'http_version'  => substr($SERVER_PROTOCOL, strpos($SERVER_PROTOCOL, '/')),
            'method'        => $_SERVER['REQUEST_METHOD'] ?? 'cli',
            'socket'        => [
                'remote_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'encrypted'      => isset($_SERVER['HTTPS'])
            ],
            'url'           => [
                'protocol'  => $http_or_https,
                'hostname'  => $_SERVER['SERVER_NAME'] ?? '',
                'port'      => $_SERVER['SERVER_PORT'] ?? '',
                'pathname'  => $_SERVER['SCRIPT_NAME'] ?? '',
                'search'    => '?' . (($_SERVER['QUERY_STRING'] ?? '') ?? ''),
                'full'      => isset($_SERVER['HTTP_HOST']) ? $http_or_https . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '',
                'raw'       => isset($_SERVER['HTTP_HOST']) ? $http_or_https . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '',
            ],
            'headers' => [
                'content_type' => $headers['Content-Type'] ?? '',
                'user_agent' => $headers['User-Agent'] ?? '',
                'cookie'     => $this->getCookieHeader($headers['Cookie'] ?? ''),
            ],
            'env' => $this->getEnv(),
            'cookies' => $this->getCookies(),
        ];

        $ctxRequest = new Context\Request();
        $ctxRequest->mergeFromJsonString(json_encode($contextRequestArr));

        $this->context->setRequest($ctxRequest);
    }

    /**
     * Set Transaction and Error Meta Data
     *
     * @param EventMetaData $eMeta
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 11:25
     */
    final public function setMeta(EventMetaData $eMeta) {
        $this->metaData = $eMeta;
    }

    /**
     * Get the Environment Variables Based on Provided Mask
     *
     * @return object
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 11:34
     */
    final protected function getEnv()
    {
        $envMask = $this->sharedData->getEnvWhiteListKeys();
        $env = empty($envMask)
            ? $_SERVER
            : array_intersect_key($_SERVER, array_flip($envMask));

        return (object) $env;
    }

    /**
     * Get the Cookies Based on Provided Mask
     * @return object
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 14:42
     */
    final protected function getCookies()
    {
        $cookieMask = $this->sharedData->getCookiesWhiteListKeys();
        $cookies = empty($cookieMask)
            ? $_COOKIE
            : array_intersect_key($_COOKIE, array_flip($cookieMask));

        return (object) $cookies;
    }

    /**
     * Get the cookie header
     *
     * @param string $cookieHeader
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 14:43
     */
    final protected function getCookieHeader(string $cookieHeader) : string
    {
        $cookieMask = $this->sharedData->getCookiesWhiteListKeys();

        // Returns an empty string if cookies are masked.
        return empty($cookieMask) ? $cookieHeader : '';
    }


}
