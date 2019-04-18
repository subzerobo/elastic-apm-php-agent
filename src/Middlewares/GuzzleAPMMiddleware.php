<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-17
 * Time: 17:41
 */

namespace Subzerobo\ElasticApmPhpAgent\Middlewares;

use Psr\Http\Message\RequestInterface;
use Subzerobo\ElasticApmPhpAgent\ActionWrappers\APMHandlerAbstract;

class GuzzleAPMMiddleware
{
    /**
     * @var APMHandlerAbstract
     */
    private $actionWrapper;

    public function __construct(APMHandlerAbstract $actionWrapper)
    {
        $this->actionWrapper = $actionWrapper;
    }

    public function __invoke(callable $handler) {

        $apmActionWrapper = $this->actionWrapper;

        $before = function($request, $options) use ($apmActionWrapper) {
            $apmActionWrapper->handleBefore($request,'cURL',[]);
            //var_dump( "BTap " . microtime(true) );
        };

        $after = function($request,$option,$response) use ($apmActionWrapper) {
            $response->then(function (\Psr\Http\Message\ResponseInterface $response) use($apmActionWrapper) {
                $apmActionWrapper->handleAfter($response,'cURL',[]);
            });
        };

        // Tap Function of Guzzle

        return function ($request, array $options) use ($handler, $before, $after) {
            if ($before) {
                $before($request, $options);
            }
            $response = $handler($request, $options);
            if ($after) {
                $after($request, $options, $response);
            }
            return $response;
        };
    }

}