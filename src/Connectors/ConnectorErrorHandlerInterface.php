<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-14
 * Time: 12:28
 */

namespace Subzerobo\ElasticApmPhpAgent\Connectors;


interface ConnectorErrorHandlerInterface
{
    public function handleException(\Exception $ex);
}