<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 11:01
 */

namespace Subzerobo\ElasticApmPhpAgent\Exceptions;


class TimerNotStartedException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct('Can\'t stop a timer which isn\'t started.', $code, $previous);
    }

}