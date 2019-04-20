<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 16:16
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers;

use Protos\Span;
use Protos\Transaction;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Traits\EventTrait;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventSharedData;
use Subzerobo\ElasticApmPhpAgent\Misc\Timer;
use Subzerobo\ElasticApmPhpAgent\Stores\SpanStore;

class TransactionEvent
{
    use EventTrait;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var SpanStore
     */
    protected $spanEventStore;

    /**
     * @var Timer
     */
    public $timer;

    /**
     * TransactionEvent constructor.
     *
     * @param string               $name
     * @param EventSharedData|null $sharedContext
     * @param null                 $start
     *
     * @throws \Exception
     */
    public function __construct(string $name, EventSharedData $sharedContext = null, $start = null)
    {
        $this->transaction = new Transaction();
        $this->transaction->setName($name);

        // Create the Context for Transaction based on array + Request Object
        $initContext = $this->InitContext($sharedContext);
        $this->transaction->setContext($initContext);

        $this->transaction->setId($this->getId());
        $this->transaction->setTraceId($this->getTraceId());

        $this->spanEventStore = new SpanStore();

        // Create the Timer of Transaction
        $this->timer = new Timer($start);
    }

    /**
     * Start the Transaction
     *
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerAlreadyStartedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 16:28
     */
    public function start()
    {
        /**
         * Set Timestamp in microseconds
         * @see https://github.com/elastic/apm-server/blob/v6.7.1/docs/spec/timestamp_epoch.json
         */
        $this->setTimestamp();
        $this->timestamp = microtime(true) * 1000000;
        $this->transaction->setTimestamp($this->getTimestamp());
        $this->timer->start();
    }

    /**
     * Stop the Transaction
     *
     * @param int|null $duration
     *
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStoppedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 16:28
     */
    public function stop(int $duration = null)
    {
        // Stop the Timer
        $this->timer->stop();
        $this->setDuration($duration ?? round($this->timer->getDurationInMilliseconds(), 3));
        // AbstractStore Summary
    }

    /** ************************************************** **/
    /**                 Span Sections                      **/
    /** ************************************************** **/

    /**
     * Starts the SpanEvent and Return it
     *
     * @param string     $name
     * @param string     $type
     * @param float|null $start
     * @param array       $data
     *
     * @return SpanEvent|null
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerAlreadyStartedException
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 19:05
     */
    public function startSpan(string $name,string $type, float $start = null, array $data =[]) {
        $spanEvent = new SpanEvent($name, $type,$this,$data, $start);
        if (!$start) {
            // Start span if $start is not provided
            $spanEvent->start();
        }
        $spanId = $spanEvent->getSpanID();
        $this->addSpan($spanEvent);
        return $this->spanEventStore->fetch($spanId);
    }

    /**
     * @param string     $spanId
     * @param float|null $duration
     *
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStoppedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 13:50
     */
    public function stopSpan(string $spanId, float $duration = null) {
        $spanEvent = $this->getSpanByID($spanId);
        $spanEvent->stop($duration);
    }

    /**
     * Adds span to the Transaction event
     *
     * @param SpanEvent $span
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:42
     */
    public function addSpan(SpanEvent $span)  {
        $this->spanEventStore->register($span);
    }

    /**
     * @param $Id
     *
     * @return SpanEvent|null
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 13:49
     */
    public function getSpanByID($Id) {
        return $this->spanEventStore->fetch($Id);
    }

    /**
     * Get List of Current Spans
     *
     * @return SpanEvent[]
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:39
     */
    public function getSpansEventList() {
        return $this->spanEventStore->list();
    }

    /**
     * @return Span[]
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 15:44
     */
    public function getSpanList() {
        $spans = [];
        foreach ($this->getSpansEventList() as $spanEvent) {
            $spans[] = $spanEvent->getSpanObject();
        }
        return $spans;
    }

    /** ************************************************** **/
    /**                 Helper Functions                   **/
    /** ************************************************** **/

    /**
     * Sets Duration of Transaction
     * @param float $duration
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 11:02
     */
    public function setDuration(float $duration) {
        $this->transaction->setDuration($duration);
    }
    public function getDuration() : float {
        return $this->transaction->getDuration();
    }


    public function setIsSampled(bool $state = true) {
        $this->transaction->setSampled($state);
    }
    public function getIsSampled() {
        return $this->transaction->getSampled();
    }


    public function getTransactionName() : string {
        return $this->transaction->getName();
    }
    public function setTransactionName(string $name) {
        $this->transaction->setName($name);
    }


    public function setType(string $type) {
        $this->metaData->setType($type);
    }
    public function getType() {
        return $this->metaData->getType();
    }


    public function setResult(string $result) {
        $this->metaData->setResult($result);
    }
    public function getResult() {
        return $this->metaData->getResult();
    }


    //TODO: Important.......... implement Marks


    /**
     * Gets the Protobuf Transaction Object of TransactionEvent
     *
     * @return Transaction
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 17:57
     */
    public function getProtoBufTransaction() {
        // Set Context to Transaction first again for possible changes
        $this->transaction->setContext($this->context);

        // Set MetaData to Transaction
        $this->transaction->setResult($this->metaData->getResult());
        $this->transaction->setType($this->metaData->getType());

        // Set Span Count
        $spanCountObject = new Transaction\SpanCount(['started' => $this->spanEventStore->count()]);
        $this->transaction->setSpanCount($spanCountObject);

        return $this->transaction;
    }


}