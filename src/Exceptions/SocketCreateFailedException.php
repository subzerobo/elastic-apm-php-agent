<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-14
 * Time: 13:12
 */

namespace Subzerobo\ElasticApmPhpAgent\Exceptions;


class SocketCreateFailedException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Cannot Create Socket Connection. Please check error :"%s"', $message), $code, $previous);
    }
}