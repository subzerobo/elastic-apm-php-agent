<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 16:16
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers;

use Protos\Error;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Traits\EventTrait;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventSharedData;

class ErrorEvent
{
    use EventTrait;

    /**
     * @var Error
     */
    protected $error;

    /**
     * Error | Exception
     *
     * @link http://php.net/manual/en/class.throwable.php
     *
     * @var \Throwable
     */
    private $throwable;

    /**
     * ErrorEvent constructor.
     *
     * @param \Throwable           $throwable
     * @param EventSharedData|null $sharedContext
     * @param null                 $start
     *
     * @throws \Exception
     */
    public function __construct(\Throwable $throwable,EventSharedData $sharedContext = null, $start = null)
    {
        // Sets Throwable

        $this->throwable = $throwable;

        $this->error = new Error();

        // Set Error Timestamp
        $this->error->setTimestamp( microtime(true) * 1000000);

        // Create the Context for Transaction based on array + Request Object
        $initContext = $this->InitContext($sharedContext);

        $this->error->setContext($initContext);

        $this->error->setId($this->getId());



    }

    /**
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-10 14:21
     */
    public function setException(int $maxTraceLevel = 0) {
        $this->error->setCulprit(sprintf('%s:%d', $this->throwable->getFile(), $this->throwable->getLine()));

        $pbException = new Error\Exception();
        $pbException->setMessage($this->throwable->getMessage());
        $pbException->setType(get_class($this->throwable));
        $pbException->setCode($this->throwable->getCode());

        $pbStackTrace = new StackTraceList();
        $n = 0;
        foreach ($this->throwable->getTrace() as $trace) {
            if ($maxTraceLevel!=0 && $n >= $maxTraceLevel)
                break;
            $pbStackTrace->addStackTraceFromData(
                $trace['file'],
                null,
                null,
                !empty($trace['file']) ? basename($trace['file']) : '(anonymous)',
                $trace['function'] ?? '(closure)',
                false,
                $trace['line'] ?? 1,
                $trace['class'],
                null,
                null,
                null
            );
            $n++;
        }

        $pbException->setStacktrace($pbStackTrace->stackTraceList());
        $this->error->setException($pbException);

    }

    /**
     * @param TransactionEvent $tEvent
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-11 16:36
     */
    public function setErrorTransaction(TransactionEvent $tEvent)  {
        $this->error->setTraceId($tEvent->getTraceId());
        $this->error->setParentId($tEvent->getId());
        $this->error->setTransactionId($tEvent->getId());
    }


    /**
     * Gets the Protobuf Transaction Object of ErrorEvent
     *
     * @return Error
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-10 14:28
     */
    public function getProtoBufError() {
        // Set Context to Transaction first again for possible changes
        $this->error->setContext($this->context);
        return $this->error;
    }


}