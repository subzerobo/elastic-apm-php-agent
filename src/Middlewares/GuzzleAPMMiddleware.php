<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-17
 * Time: 17:41
 */

namespace Subzerobo\ElasticApmPhpAgent\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

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

    public function __invoke(Request $request, RequestHandler $requestHandler): Response
    {
        $type = $options['type'] ?? "General";

        $this->actionWrapper->handleBefore($request,$type,[]);

        $response = $requestHandler->handle($request);

        $this->actionWrapper->handleAfter($response,$type,[]);

        return $response;
    }

}