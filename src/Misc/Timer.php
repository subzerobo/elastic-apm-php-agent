<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 18:37
 */

namespace Subzerobo\ElasticApmPhpAgent\Misc;


use Subzerobo\ElasticApmPhpAgent\Exceptions\TimerAlreadyStartedException;
use Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStartedException;
use Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStoppedException;

class Timer
{
    /**
     * Starting Timestamp
     *
     * @var double
     */
    private $startedOn = null;

    /**
     * Ending Timestamp
     *
     * @var double
     */
    private $stoppedOn = null;

    /**
     * Timer constructor can be initialized with Specific time as startedOn
     *
     * @param float|null $startTime
     */
    public function __construct(float $startTime = null)
    {
        $this->startedOn = $startTime;
    }

    /**
     * Start the Timer
     *
     * @return void
     * @throws TimerAlreadyStartedException
     */
    public function start()
    {
        if (null !== $this->startedOn) {
            throw new TimerAlreadyStartedException();
        }

        $this->startedOn = microtime(true); // As Seconds with 4 Decimal Points
    }

    /**
     * Stops the Timer
     *
     * @throws TimerNotStartedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 11:09
     */
    public function stop() {
        if ($this->startedOn === null) {
            throw new TimerNotStartedException();
        }

        $this->stoppedOn = microtime(true);
    }

    /**
     * Get the elapsed Duration of this Timer in MicroSeconds
     *
     * @return float
     * @throws TimerNotStoppedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 11:17
     */
    public function getDurationInMicroseconds() : float
    {
        if ($this->stoppedOn === null) {
            throw new TimerNotStoppedException();
        }

        return $this->toMicroSeconds($this->stoppedOn - $this->startedOn);
    }

    /**
     * Get the elapsed Duration of this Timer in MicroSeconds
     *
     * @return float
     * @throws TimerNotStoppedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 11:17
     */
    public function getDurationInMilliseconds() : float
    {
        if ($this->stoppedOn === null) {
            throw new TimerNotStoppedException();
        }

        return $this->toMilliSeconds($this->stoppedOn - $this->startedOn);
    }

    /**
     * Get the current elapsed Interval of the Timer in MicroSeconds
     *
     * @return float
     * @throws TimerNotStartedException
     * @throws TimerNotStoppedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 11:20
     */
    public function getElapsedMicroseconds() : float
    {
        if ($this->startedOn === null) {
            throw new TimerNotStartedException();
        }

        return ($this->stoppedOn === null) ?
            $this->toMicroSeconds(microtime(true) - $this->startedOn) :
            $this->getDurationInMicroseconds();
    }

    /**
     * Get the current elapsed Interval of the Timer in MilliSeconds
     *
     * @return float
     * @throws TimerNotStartedException
     * @throws TimerNotStoppedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 11:26
     */
    public function getElapsedMilliseconds() : float
    {
        if ($this->startedOn === null) {
            throw new TimerNotStartedException();
        }

        return ($this->stoppedOn === null) ?
            $this->toMilliSeconds(microtime(true) - $this->startedOn) :
            $this->getDurationInMilliseconds();
    }


    /**
     * Converts Float Seconds to MicroSeconds
     *
     * @param float $num
     *
     * @return float
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 11:16
     */
    private function toMicroSeconds(float $num) : float
    {
        return $num * 1000000;
    }

    /**
     * Converts Float Seconds to MilliSeconds
     *
     * @param float $num
     *
     * @return float
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-09 11:15
     */
    private function toMilliSeconds(float $num) : float
    {
        return $num * 1000;
    }

}