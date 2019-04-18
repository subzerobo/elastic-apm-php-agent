<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-18
 * Time: 12:54
 */

namespace Subzerobo\ElasticApmPhpAgent\Factories;


class DefaultTransactionNameFactoryAbstract extends TransactionNameGeneratorAbstract
{

    public function generateTransactionName(): string
    {
            $RawMethodName = $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'];
            $transactionName =  preg_replace('/\?.*/', '', $RawMethodName);
            $cleanup_rules = $this->config->get("cleanup_rules");
            if ($cleanup_rules && count($cleanup_rules)>0) {
                $patterns = array_keys($cleanup_rules);
                $replacements = array_values($cleanup_rules);
                $transactionName = preg_replace($patterns, $replacements, $transactionName);
            }
            return $transactionName;
    }

}