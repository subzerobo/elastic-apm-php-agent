<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 17:54
 */

namespace Subzerobo\ElasticApmPhpAgent\Exceptions;


class DuplicateTransactionNameException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('A transaction with the name %s is already registered.', $message), $code, $previous);
    }
}