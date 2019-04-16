<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 11:01
 */

namespace Subzerobo\ElasticApmPhpAgent\Exceptions;


use Throwable;

class TimerAlreadyStartedException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct( 'Can\'t start a timer which is already started.', $code, $previous );
    }

}