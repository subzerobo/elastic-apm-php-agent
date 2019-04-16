<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-14
 * Time: 12:29
 */

namespace Subzerobo\ElasticApmPhpAgent\Connectors;


class DefaultErrorHandler implements ConnectorErrorHandlerInterface
{
    /**
     * @param \Exception $ex
     *
     * @return mixed
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-14 12:29
     */
    public function handleException(\Exception $ex)
    {
        // Simple error Handler
        // You can call your logger here.
        die($ex->getMessage());
        // TODO: Implement handleException() method.
    }

}