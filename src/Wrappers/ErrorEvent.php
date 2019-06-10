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
        // $this->error->setTraceId($this->getTraceId());

        // Fill Exception Data to Error Proto Object
        $this->setException();

    }

    /**
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-10 14:21
     */
    private function setException() {
        $this->error->setCulprit(sprintf('%s:%d', $this->throwable->getFile(), $this->throwable->getLine()));
        $pbException = new Error\Exception();
        $pbException->setMessage($this->throwable->getMessage());
        $pbException->setType(get_class($this->throwable));
        $pbException->setCode($this->throwable->getCode());

        $pbStackTrace = new StackTraceList();
        foreach ($this->throwable->getTrace() as $trace) {
            $pbStackTrace->addStackTraceFromData(
                $trace['file'],
                null,
                null,
                basename($trace['file']) ?? '(anonymous)',
                $trace['function'] ?? '(closure)',
                null,
                $trace['line'] ?? 0,
                $trace['class'],
                null,
                null,
                null
            );
        }

        $pbException->setStacktrace($pbStackTrace->stackTraceList());
        $this->error->setException($pbException);

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