<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-18
 * Time: 12:53
 */

namespace Subzerobo\ElasticApmPhpAgent\Factories;


use Subzerobo\ElasticApmPhpAgent\Misc\Config;

abstract class TransactionNameGeneratorAbstract implements TransactionNameGeneratorInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * TransactionNameGeneratorInterface constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    abstract public function generateTransactionName() :string;

}