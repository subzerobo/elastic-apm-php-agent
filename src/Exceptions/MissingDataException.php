<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 13:23
 */

namespace Subzerobo\ElasticApmPhpAgent\Exceptions;


class MissingDataException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('%s is not initialized yet', $message), $code, $previous);
    }
}