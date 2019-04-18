<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-18
 * Time: 13:06
 */

namespace Subzerobo\ElasticApmPhpAgent\Factories;


interface TransactionNameGeneratorInterface
{
    public function generateTransactionName() :string;
}