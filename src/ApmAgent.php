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
use Subzerobo\ElasticApmPhpAgent\Factories\DefaultTransactionNameFactoryAbstract;
use Subzerobo\ElasticApmPhpAgent\Factories\EventFactoryInterface;
use Subzerobo\ElasticApmPhpAgent\Factories\TransactionNameGeneratorInterface;
use Subzerobo\ElasticApmPhpAgent\Wrappers\ErrorEvent;
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
    const VERSION = "1.0.10";

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
     * @var TransactionNameGeneratorInterface
     */
    protected $nameGenerator;
    /**
     * ApmAgent constructor.
     *
     * @param array                             $config
     * @param TransactionNameGeneratorInterface $nameGenerator
     * @param EventFactoryInterface|null        $eventFactory
     * @param EventSharedData|null              $sharedData
     *
     * @throws Exception\MissingAppNameException
     * @throws Exceptions\TimerAlreadyStartedException
     */

    public function __construct(array $config, TransactionNameGeneratorInterface $nameGenerator = null, EventFactoryInterface $eventFactory = null, EventSharedData $sharedData = null)
    {
        // Initialize ApmAgent Config
        $this->config = new Config($config);

        // Use the custom event factory provided or create the default one
        $this->eventFactory = $eventFactory ?? new DefaultEventFactory();

        // Set The User Provided Shared Data
        $this->sharedData = $sharedData ?? new EventSharedData();

        $this->nameGenerator = $nameGenerator ?? new DefaultTransactionNameFactoryAbstract($this->config);

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
    public function startTransaction(string $name = null, EventSharedData $ctxSharedData = null, float $startedOn = null): TransactionEvent
    {
        $transactionName = $name ?? $this->nameGenerator->generateTransactionName();

        $this->setCurrentTransactionName($transactionName);

        $transactionStore = $this->payload->getTransactionStore();

        $transactionStore->register(
            $this->eventFactory->createTransaction(
                $transactionName,
                $this->sharedData->merge($ctxSharedData),
                $startedOn
            )
        );

        // Get newly create transaction event object from store
        $transaction = $transactionStore->fetch($transactionName);

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
    public function stopTransaction(string $name = null, EventMetaData $meta = null) {

        $transactionName = $name ?? $this->getCurrentTransactionName();

        $this->getTransactionEvent($transactionName)->stop();
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
     * Registers an Error on Server
     *
     * @param \Throwable           $throwable
     * @param EventSharedData|null $ctxSharedData
     *
     * @return ErrorEvent
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-10 14:58
     */
    public function reportError(\Throwable $throwable, array $attributes, EventSharedData $ctxSharedData = null) {
        $transactionEvent = $this->getTransactionEvent($this->currentTransactionName);

        $errorStore = $this->payload->getErrorStore();

        $errEvent = $this->eventFactory->createError(
            $throwable,
            $this->sharedData->merge($ctxSharedData)
        );

        $normalizedAttrArray = [];

        $this->normalizeArray($normalizedAttrArray, $attributes,"");

        //echo "<pre>";var_dump($normalizedAttrArray);exit;

        $errEvent->setTags($normalizedAttrArray);

        $errEvent->setException($this->config->get("max_level"));

        $errEvent->setErrorTransaction($transactionEvent);

        $errorStore->register($errEvent);

        return $errEvent;
    }

    /**
     * Normalize Error Attributes Multilevel to SingleLevel
     *
     * @param array  $data
     * @param array  $attributes
     * @param string $prefix
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-15 11:32
     */
    private function normalizeArray(array &$data, array $attributes, string $prefix = '') {
        foreach ($attributes as $key => $value){
            if (is_array($value)) {
                $this->normalizeArray($data, $value, $prefix . $key . "_");
            }else{
                $data[$prefix . $key] = $value;
            }
        }
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

    /**
     * Reset the transaction name generator
     *
     * @param TransactionNameGeneratorInterface $nameGenerator
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-15 09:53
     */
    public function setNameGenerator(TransactionNameGeneratorInterface $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }

    /**
     * Rename Transaction both in Store and The Transaction Itself
     *
     * @param string|null $newName
     *
     * @throws UnknownTransactionException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-15 09:50
     */
    public function renameTransaction(string $newName = null) {
        $currentTransactionName = $this->currentTransactionName;
        // We Regenerate new name based on the name generator function again or the name provided by user
        $transactionNewName = $newName ?? $this->nameGenerator->generateTransactionName();

        // Get the TxEvent
        $txEvent = $this->getTransactionEvent($currentTransactionName);

        // Rename the TxEvent Name
        $txEvent->setTransactionName($transactionNewName);

        // Rename key of the Event in Store
        $this->payload->getTransactionStore()->rename(
            $currentTransactionName,
            $transactionNewName
        );
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
