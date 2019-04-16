<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-09
 * Time: 14:26
 */

namespace Subzerobo\ElasticApmPhpAgent\Factories;


use Subzerobo\ElasticApmPhpAgent\Wrappers\SpanEvent;
use Subzerobo\ElasticApmPhpAgent\Wrappers\TransactionEvent;
use Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers\EventSharedData;

class DefaultEventFactory implements EventFactoryInterface
{

    /**
     * @param \Throwable      $throwable
     * @param EventSharedData $contexts
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 14:15
     */
    public function createError(\Throwable $throwable, EventSharedData $contexts)
    {
        // TODO: Implement createError() method.
    }

    /**
     * @param string          $name
     * @param EventSharedData $sharedContexts
     * @param float|null      $start
     *
     * @return TransactionEvent
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 14:15
     */
    public function createTransaction(string $name, EventSharedData $sharedContexts, float $start = null): TransactionEvent
    {
        // TODO: Implement createTransaction() method.

        $transactionEvent = new TransactionEvent($name,$sharedContexts,$start);

        return $transactionEvent;
    }

}