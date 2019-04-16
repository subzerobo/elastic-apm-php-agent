<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 16:51
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers;


use Protos\MetaData;
use Protos\User;
use Subzerobo\ElasticApmPhpAgent\ApmAgent;
use Subzerobo\ElasticApmPhpAgent\Misc\Config;
use Subzerobo\ElasticApmPhpAgent\NDJson\NDJsonHandler;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventSharedData;
use Subzerobo\ElasticApmPhpAgent\Misc\Timer;
use Subzerobo\ElasticApmPhpAgent\Stores\TransactionStore;

class Payload
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * NDJSON Payload MetaData
     * @var MetaData
     */
    protected $MetaData;

    /**
     * @var TransactionStore
     */
    protected $TransactionEventStore;


    /**
     * @var NDJsonHandler
     */
    private $ndjson;

    /**
     * Apm Timer
     *
     * @var Timer
     */
    private $timer;

    /**
     * Payload constructor.
     *
     * @param Config               $config
     * @param EventSharedData|null $sharedData
     *
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerAlreadyStartedException
     * @throws \Exception
     */
    public function __construct(Config $config, EventSharedData $sharedData = null)
    {
        // TODO: Initializer to be done
        $this->config = $config;

        // Initialize the Stores
        $this->TransactionEventStore = new TransactionStore();


        // Initialize MetaData Object
        $this->fillPayloadMetaData($sharedData->getMetaUserInfo());

        // Start Global Agent Timer
        $this->timer = new Timer();
        $this->timer->start();

        $this->ndjson = new NDJsonHandler();
    }


    /** ************************************************** **/
    /**                 Helper Functions                   **/
    /** ************************************************** **/

    /**
     * Sets User Information for MetaData
     *
     * @param string|null $id
     * @param string|null $name
     * @param string|null $email
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 13:27
     */
    public function setMetaDataUser(string $id = null, string $name= null, string $email = null) {
        if (is_null($this->MetaData->getUser())) {
            $this->MetaData->setUser(new User());
        }
        $this->MetaData->getUser()->setId($id);
        $this->MetaData->getUser()->setUsername($name);
        $this->MetaData->getUser()->setEmail($email);
    }

    /**
     * Gets the Payload Meta Portion
     *
     * @return MetaData
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 11:34
     */
    public function getMetaData() : MetaData {
        return $this->MetaData;
    }

    /**
     * Fills the Payload Meta Data Portion
     * 
     * @param array $sharedUserInfo
     *
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:35
     */
    private function fillPayloadMetaData(array $sharedUserInfo = []) {

        $metaDataInitArr = [
            'service' => [
                'name' => $this->config->get('appName'),
                'version' => $this->config->get('appVersion'),
                'environment' => $this->config->get('environment'),
                'framework' => [
                    'name' => $this->config->get('framework',''),
                    'version' => $this->config->get('frameworkVersion', ''),
                ],
                'language'  => [
                    'name' => 'php',
                    'version' => phpversion()
                ],
                'agent' => [
                    'name' => ApmAgent::NAME,
                    'version' => ApmAgent::VERSION
                ]
            ],
            'system' => [
                'hostname' => $this->config->get('hostname'),
                'architecture' => php_uname('m'),
                'platform'     => php_uname('s'),
            ],
            'process' => [
                'pid' => getmypid(),
            ]
        ];

        // Add Container Info to System
        if ($this->config->get('isDockerContainer') === true) {
            $metaDataInitArr['system']['container']['id'] = getenv($this->config->get('containerIdEnv'));
        }

        // Add Kubernetes Info to System
        if ($this->config->get('isKubernetes') === true) {
            $metaDataInitArr['system']['kubernetes'] = [
                'namespace' => getenv($this->config->get('kuberNamespaceEnv')),
                'pod' => [
                    'name' => getenv($this->config->get('kuberPodNameEnv')),
                    'uid'  => getenv($this->config->get('kuberPodUidEnv')),
                ],
                'node' => [
                    'name' => getenv($this->config->get('kuberNodeNameEnv')),
                ]
            ];
        }

        //dd($metaDataInitArr);
        $this->MetaData = new MetaData();
        $this->MetaData->mergeFromJsonString(json_encode($metaDataInitArr));

        // Fill User Data if Provided in constructor
        if (!empty($sharedUserInfo)) {
            $this->setMetaDataUser($sharedUserInfo['id'], $sharedUserInfo['name'], $sharedUserInfo['email']);
        }
    }

    /**
     * Gets the TransactionStore of Payload
     *
     * @return TransactionStore
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 11:31
     */
    public function getTransactionStore() : TransactionStore {
        return $this->TransactionEventStore;
    }

    /**
     * Creates Payload Protobuf Object
     *
     * @return \Protos\Payload
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-08 17:15
     */
    public function createPayloadObj() {
        // TODO : Create Payload Object
        $payloadObj = new \Protos\Payload();
        // Step 1 Set Meta
        $payloadObj->setMetadata($this->MetaData);
        // Step 2 Set Transactions and Spans
        /* @var $txEvents TransactionEvent[] */
        $txEvents = $this->TransactionEventStore->list();
        $txList = [];
        $spList = [];
        foreach ($txEvents as $txEvent){
            $txList[] = $txEvent->getProtoBufTransaction();
            $spans = $txEvent->getSpanList();
            foreach ($spans as $span) {
                $spList[] = $span;
            }
        }
        $payloadObj->setTransactions($txList);
        $payloadObj->setSpans($spList);

        return $payloadObj;
    }

    /**
     * Creates Payload NDJSON String
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-08 17:17
     */
    public function createPayloadNDJson() {

        $this->ndjson->flushOutput();

        // Step 1 Write Meta
        $this->ndjson->plainWrite("metadata",$this->MetaData->serializeToJsonString());

        // Step 2 Write Spans
        /* @var $txEvents TransactionEvent[] */
        $txEvents = $this->TransactionEventStore->list();

        foreach ($txEvents as $txEvent){
            $spans = $txEvent->getSpanList();
            foreach ($spans as $span) {
                $this->ndjson->plainWrite('span',$span->serializeToJsonString());
            }
        }

        // Step 3 Write Transactions
        foreach ($txEvents as $txEvent) {
            $this->ndjson->plainWrite("transaction", $txEvent->getProtoBufTransaction()->serializeToJsonString());
        }

        // Finally return payload ND-JSON string
        return $this->ndjson->getOutput();
    }

    public function isEmpty() :bool {
        return ($this->TransactionEventStore->isEmpty());
    }

    public function reset() {
        $this->TransactionEventStore->reset();
        $this->ndjson->flushOutput();
    }


}