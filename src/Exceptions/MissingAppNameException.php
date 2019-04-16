<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 16:24
 */

namespace Subzerobo\ElasticApmPhpAgent\Exception;


class MissingAppNameException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('No app name registered in Agent config.', $message), $code, $previous);
    }
}