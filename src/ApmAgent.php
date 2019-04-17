<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 16:00
 */

namespace Subzerobo\ElasticApmPhpAgent;

use Subzerobo\ElasticApmPhpAgent\Connectors\ConnectorErrorHandlerInterface;
use Subzerobo\ElasticApmPhpAgent\Connectors\TCP;
use Subzerobo\ElasticApmPhpAgent\Connectors\UDP;
use Subzerobo\ElasticApmPhpAgent\Factories\DefaultEventFactory;
use Subzerobo\ElasticApmPhpAgent\Factories\EventFactoryInterface;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Payload;
use Subzerobo\ElasticApmPhpAgent\Wrappers\TransactionEvent;
use Subzerobo\ElasticApmPhpAgent\Exceptions\UnknownTransactionException;
use Subzerobo\ElasticApmPhpAgent\Misc\Config;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventMetaData;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventSharedData;

class ApmAgent
{
    /**
     * Agent Version
     */
    const VERSION = "1.0";

    /**
     * Agent Name
     */
    const NAME = "elastic-apm-php-upd-support";

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Payload
     */
    protected $payload;

    /**
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @var EventSharedData
     */
    private $sharedData;

    /**
     * @var string
     */
    private $currentTransactionName;

    /**
     * @var ConnectorErrorHandlerInterface
     */
    protected $tcpErrorHandler;

    /**
     * @var ConnectorErrorHandlerInterface
     */
    protected $udpErrorHandler;


    /**
     * ApmAgent constructor.
     *
     * @param array                      $config
     * @param EventFactoryInterface|null $eventFactory
     * @param EventSharedData|null       $sharedData
     *
     * @throws Exception\MissingAppNameException
     * @throws Exceptions\TimerAlreadyStartedException
     */

    public function __construct(array $config, EventFactoryInterface $eventFactory = null, EventSharedData $sharedData = null)
    {
        // Initialize ApmAgent Config
        $this->config = new Config($config);

        // Use the custom event factory provided or create the default one
        $this->eventFactory = $eventFactory ?? new DefaultEventFactory();

        // Set The User Provided Shared Data
        $this->sharedData = $sharedData ?? new EventSharedData();

        // Add Config Env & Cookie List to sharedData
        $this->sharedData->setEnvWhiteListKeys($this->config->get('env', []));
        $this->sharedData->setCookiesWhiteListKeys($this->config->get('cookies', []));

        // Initialize payload [MetaData will be initialized here]
        $this->payload = new Payload($this->config, $this->sharedData);
    }

    /**
     * @param string          $name
     * @param EventSharedData $ctxSharedData
     * @param float|null      $startedOn
     *
     * @return TransactionEvent
     * @throws Exceptions\DuplicateTransactionNameException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 14:26
     */
    public function startTransaction(string $name, EventSharedData $ctxSharedData = null, float $startedOn = null): TransactionEvent
    {

        $transactionStore = $this->payload->getTransactionStore();

        $transactionStore->register(
            $this->eventFactory->createTransaction(
                $name,
                $this->sharedData->merge($ctxSharedData),
                $startedOn
            )
        );

        // Get newly create transaction event object from store
        $transaction = $transactionStore->fetch($name);

        // Start the Transaction if $startedOn is not provided
        if ($startedOn === null) {
            $transaction->start();
        }

        return $transaction;
    }

    /**
     * Stops the Transaction
     * 
     * @param string             $name
     * @param EventMetaData|null $meta
     *
     * @throws Exceptions\TimerNotStartedException
     * @throws Exceptions\TimerNotStoppedException
     * @throws UnknownTransactionException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 12:07
     */
    public function stopTransaction(string $name, EventMetaData $meta = null) {
        $this->getTransactionEvent($name)->stop();
        if ($meta) {
            $this->getTransactionEvent($name)->setMeta($meta);
        }
    }
    
    /**
     * @param string $name
     *
     * @return TransactionEvent
     * @throws UnknownTransactionException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 10:53
     */
    public function getTransactionEvent(string $name) : TransactionEvent {
        $transactionEvent = $this->payload->getTransactionStore()->fetch($name);
        if ($transactionEvent === null) {
            throw new UnknownTransactionException($name);
        }
        return $transactionEvent;
    }

    /**
     * Get the Agent Config
     *
     * @return Config
     */
    public function getConfig() : Config
    {
        return $this->config;
    }

    public function getPayload() : Payload {
        return $this->payload;
    }

    /**
     * Sends the payload using defaultConnector specified in config
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-14 13:36
     */
    public function send() :bool {
        // Is the Agent enabled ?
        if ($this->config->get('active') === false) {
            return true;
        }

        if ($this->config->get('defaultConnector') === 'udp') {
            return $this->sendUDP(UDP::UDP_USE_CONFIG_SETTINGS);
        }else{
            // Send Using TCP
            return $this->sendTCP();
        }
    }

    /**
     * Sends the payload to apm server using tcp
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-14 13:36
     */
    public function sendTCP() : bool {
        try {
            // Is the Agent enabled ?
            if ($this->config->get('active') === false) {
                return true;
            }
            
            $connector = new TCP($this->config);
            $status = true;

            if ($this->payload->isEmpty() === false) {
                $status = $status && $connector->send($this->payload);
            }

            if ($status === true) {
                $this->payload->reset();
            }

            return $status;
        }catch (\Exception $ex) {
            // Call Error Handler
            if ($this->tcpErrorHandler) {
                $this->tcpErrorHandler->handleException($ex);
            }
            return false;
        }
    }

    /**
     * Sends the payload (ndjson,protos) to Go Middleware Agent using UDP
     *
     * @param int $overrideMethod
     *
     * @return bool
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-14 13:35
     */
    public function sendUDP(int $overrideMethod = UDP::UDP_USE_CONFIG_SETTINGS) : bool {
        try {
            // Is the Agent enabled ?
            if ($this->config->get('active') === false) {
                return true;
            }

            $connector = new UDP($this->config);
            $status = true;

            if ($this->payload->isEmpty() === false) {
                $status = $status && $connector->send($this->payload,$overrideMethod);
            }

            if ($status === true) {
                $this->payload->reset();
            }

            return $status;
        }catch (\Exception $ex) {
            // Call Error Handler
            if ($this->udpErrorHandler) {
                $this->udpErrorHandler->handleException($ex);
            }
            return false;
        }
    }

    public function setTCPErrorHandler(ConnectorErrorHandlerInterface $tcpError) {
        $this->tcpErrorHandler = $tcpError;
    }

    public function setUDPErrorHandler(ConnectorErrorHandlerInterface $udpError) {
        $this->udpErrorHandler = $udpError;
    }

    public function setCurrentTransactionName(string $name){
        $this->currentTransactionName = $name;
    }

    public function getCurrentTransactionName() {
        return $this->currentTransactionName;
    }

    public function isActive() :bool {
        return $this->config->get('active');
    }

}