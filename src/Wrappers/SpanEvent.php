<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 16:17
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers;


use Protos\Span;
use Protos\StackTrace;
use Subzerobo\ElasticApmPhpAgent\Misc\Timer;
use Subzerobo\ElasticApmPhpAgent\Misc\UID;

class SpanEvent
{
    /**
     * @var Span
     */
    protected $span;

    /**
     * @var StackTraceList
     */
    protected $stackTraceDataList;

    /**
     * @var Timer
     */
    public $timer;

    /**
     * SpanEvent constructor.
     *
     * @param string           $name
     * @param string           $type
     * @param TransactionEvent $tEvent
     * @param array|null       $data
     * @param null             $start
     *
     * @throws \Exception
     */
    public function __construct(string $name,string $type, TransactionEvent $tEvent, array $data =null, $start = null)
    {
        $this->span = new Span($data);
        $this->setId(UID::Generate(16));
        $this->setTraceId($tEvent->getTraceId());
        $this->setParentId($tEvent->getId());
        $this->setTransactionId($tEvent->getId());
        $this->setSpanName($name);
        $this->setType($type);
        
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
        // Set Timestamp in microseconds
        $this->span->setTimestamp(microtime(true) * 1000000);
        // Start Timer
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
    }


    /**
     * Sets the Span Name
     *
     * @param string $name
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:56
     */
    public function setSpanName(string $name) {
        $this->span->setName($name);
    }
    /**
     * Gets the name of current span
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:38
     */
    public function getSpanName() {
        return $this->span->getName();
    }


    /**
     * Sets Span Type
     *
     * @param string $type
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:57
     */
    public function setType(string $type) {
        $this->span->setType($type);
    }
    /**
     * Gets the Span Type
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:57
     */
    public function getType(){
        return $this->span->getType();
    }


    /**
     * Sets Span SubType
     *
     * @param string $type
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:57
     */
    public function setSubType(string $type) {
        $this->span->setSubType($type);
    }
    /**
     * Gets the Span SubType
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:57
     */
    public function getSubType(){
        return $this->span->getSubType();
    }


    /**
     * Sets Span Action
     *
     * @param string $action
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:57
     */
    public function setAction(string $action) {
        $this->span->setAction($action);
    }
    /**
     * Gets the Span Action
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 13:57
     */
    public function getAction(){
        return $this->span->getAction();
    }


    /**
     * Sets the id of span
     *
     * @param string $id
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:05
     */
    public function setId(string $id) {
        $this->span->setId($id);
    }
    /**
     * Gets the id of Span
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:05
     */
    public function getId() {
        return $this->span->getId();
    }


    /**
     * Sets the Hex encoded 64 random bits ID of the parent transaction or span.
     * @param string $id
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:06
     */
    public function setParentId(string $id) {
        $this->span->setParentId($id);
    }
    /**
     * Gets the ID of the parent transaction or span
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:07
     */
    public function getParentId() {
        return $this->span->getParentId();
    }


    /**
     * Sets the Hex encoded 64 random bits ID of the parent transaction.
     *
     * @param string $id
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:06
     */
    public function setTransactionId(string $id) {
        $this->span->setTransactionId($id);
    }
    /**
     * Gets the ID of the parent transaction
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:07
     */
    public function getTransactionId() {
        return $this->span->getTransactionId();
    }


    /**
     * Sets the Hex encoded 128 random bits ID of the correlated trace.
     *
     * @param string $id
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:09
     */
    public function setTraceId(string $id) {
        $this->span->setTraceId($id);
    }
    /**
     * Gets the TraceId of span
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:09
     */
    public function getTraceId() {
        return $this->span->getTraceId();
    }


    /**
     * Sets Timestamp of span
     *
     * @param int $ts
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:11
     */
    public function setTimestamp(int $ts = null) {
        if (is_null($ts)) {
            $ts = microtime(true) * 1000000;
        }
        $this->span->setTimestamp($ts);
    }
    /**
     * Gets the timestamp of span
     *
     * @return int
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:12
     */
    public function getTimestamp() {
        return $this->span->getTimestamp();
    }


    /**
     * Sets the milliseconds passed after start time of parent transaction or span
     * 
     * @param float $milliseconds
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:25
     */
    public function setStart(float $milliseconds) {
        $this->span->setStart($milliseconds);
    }
    /**
     * Returns the milliseconds passed after start time of parent transaction or span
     *
     * @return float
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:27
     */
    public function getStart() {
        return $this->span->getStart();
    }


    /**
     * Sets the Span duration in milliseconds
     *
     * @param float $milliseconds
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 15:02
     */
    public function setDuration(float $milliseconds) {
        $this->span->setDuration($milliseconds);
    }
    /**
     * Returns Duration in milliseconds
     *
     * @return float
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:27
     */
    public function getDuration() {
        return $this->span->getDuration();
    }


    /**
     * Sets the if the span is synced
     *
     * @param bool $isSync
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:05
     */
    public function setIsSynced(bool $isSync) {
        $this->span->setSync($isSync);
    }
    /**
     * Gets sync status of span
     *
     * @return bool
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-11 14:05
     */
    public function getIsSynced() {
        return $this->span->getSync();
    }


    /**
     * Set the Span Context
     *
     * @param Span\Context $spanContext
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 11:56
     */
    public function setContext(Span\Context $spanContext) {
        $this->span->setContext($spanContext);
    }
    /**
     * Set the Span Context
     *
     * @param SpanContextData $span_context_data
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 11:55
     */
    public function setContextFromContextData(SpanContextData $span_context_data) {
        $this->span->setContext($span_context_data->context());
    }
    /**
     * Gets the span Context of Span
     * @return Span\Context
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 11:56
     */
    public function getContext(): Span\Context {
        return $this->span->getContext();
    }


    /**
     * @param StackTrace $stackTrace
     *
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 14:58
     */
    public function addStackTrace(StackTrace $stackTrace) {
        if ($this->stackTraceDataList) {
            $this->stackTraceDataList->addStackTrace($stackTrace);
        }else{
            $this->stackTraceDataList = new StackTraceList();
            $this->stackTraceDataList->addStackTrace($stackTrace);
        }
    }

    /**
     * @param string|null $abs_path
     * @param int|null    $colno
     * @param string|null $contextLine
     * @param string|null $filename
     * @param string|null $function
     * @param bool        $library_frame
     * @param int|null    $lineno
     * @param string|null $module
     * @param array|null  $post_context
     * @param array|null  $pre_context
     * @param array|null  $vars
     *
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 14:59
     */
    public function addStackTraceFromData(string $abs_path = null,
                                          int $colno = null,
                                          string $contextLine = null,
                                          string $filename = null,
                                          string $function = null,
                                          bool $library_frame = false,
                                          int $lineno = null,
                                          string $module = null,
                                          array $post_context = null,
                                          array $pre_context = null,
                                          array $vars = null) {
        // Initialize StackTraceList if empty
        if (!$this->stackTraceDataList)
            $this->stackTraceDataList = new StackTraceList();
        $this->stackTraceDataList->addStackTraceFromData($abs_path,$colno,$contextLine,$filename,$function,$library_frame,$lineno,$module,$post_context,$pre_context,$vars);
    }
    /**
     * Gets array of StackTraces added to span
     *
     * @return StackTrace[]
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 12:37
     */
    public function getStackTraceList() {
        return $this->stackTraceDataList->stackTraceList();
    }


    /**
     * Gets the Span Protobuf Object
     * @return Span
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 12:41
     */
    public function getSpanObject() {
        // Fill the StackTrace Data into span
        if (!empty($this->stackTraceDataList)) {
            $this->span->setStacktrace($this->getStackTraceList());
        }

        return $this->span;
    }
}