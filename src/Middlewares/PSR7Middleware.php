<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-18
 * Time: 12:44
 */

namespace Subzerobo\ElasticApmPhpAgent\Middlewares;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Subzerobo\ElasticApmPhpAgent\ApmAgent;

/**
 * Add this middleware as the first middleware to not lose any span
 *
 * Class PSR7Middleware
 * @package Subzerobo\ElasticApmPhpAgent\Middlewares
 */
class PSR7Middleware
{
    /**
     * @var ApmAgent
     */
    protected $apmAgent;

    protected $container;

    public function __construct($container, ApmAgent $apmAgent)
    {
        $this->container = $container;
        $this->apmAgent  = $apmAgent;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $requestHandler
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\DuplicateTransactionNameException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStoppedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\UnknownTransactionException|\GuzzleHttp\Exception\GuzzleException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-15 09:55
     */
    public function __invoke(Request $request, RequestHandler $requestHandler): Response
    {
        // Start Transaction
        $transactionEvent = $this->apmAgent->startTransaction();

        // TODO: Implement __invoke() method.
        $response = $requestHandler->handle($request);

        // Set and Stop Transaction
        $transactionEvent->setResult("HTTP " . $response->getStatusCode());
        $transactionEvent->setType("request");
        $transactionEvent->setIsSampled(true);
        $transactionEvent->setResponseFromArray([
                'finished'     => true,
                'headers_sent' => true,
                'status_code'  => $response->getStatusCode(),]
        );

        $transactionEvent->stop();

        $this->apmAgent->renameTransaction();
        $this->apmAgent->send();

        return $response;
    }
}