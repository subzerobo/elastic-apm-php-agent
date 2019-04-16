<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 14:28
 */

namespace Subzerobo\ElasticApmPhpAgent\Factories;


use Subzerobo\ElasticApmPhpAgent\Wrappers\SpanEvent;
use Subzerobo\ElasticApmPhpAgent\Wrappers\TransactionEvent;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventSharedData;

interface EventFactoryInterface
{
    public function createError(\Throwable $throwable, EventSharedData $contexts);

    public function createTransaction(string $name, EventSharedData $contexts, float $start = null): TransactionEvent;

}