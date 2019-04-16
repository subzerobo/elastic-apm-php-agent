<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 17:48
 */

namespace Subzerobo\ElasticApmPhpAgent\Stores;


use Subzerobo\ElasticApmPhpAgent\Exceptions\DuplicateTransactionNameException;
use Subzerobo\ElasticApmPhpAgent\Wrappers\TransactionEvent;

/**
 * Class TransactionStore
 * Utilizes the Transaction(s) of specific payload
 *
 * @package Subzerobo\ElasticApmPhpAgent\Stores
 */
class TransactionStore extends AbstractStore
{
    /**
     * Register a Transaction in AbstractStore
     *
     * @param TransactionEvent $transEvent
     *
     * @throws DuplicateTransactionNameException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-08 17:55
     */
    public function register(TransactionEvent $transEvent)
    {
        $name = $transEvent->getTransactionName();
        
        if ($this->exists($name)) {
            throw new DuplicateTransactionNameException($name);
        }

        // Push Transaction to AbstractStore
        $this->store[$name] = $transEvent;
    }



}