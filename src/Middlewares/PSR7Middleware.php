<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-18
 * Time: 12:44
 */

namespace Subzerobo\ElasticApmPhpAgent\Middlewares;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Subzerobo\ElasticApmPhpAgent\ApmAgent;
use Subzerobo\ElasticApmPhpAgent\Factories\DefaultTransactionNameFactoryAbstract;
use Subzerobo\ElasticApmPhpAgent\Factories\TransactionNameGeneratorInterface;

/**
 * Add this middleware as the first middleware to not loose any span
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     *
     * @return ResponseInterface
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\DuplicateTransactionNameException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-18 13:20
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next=null)
    {
        // Start Transaction
        $transactionEvent = $this->apmAgent->startTransaction();

        // TODO: Implement __invoke() method.
        $response = $next($request, $response);
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
        $this->apmAgent->send();
        return $response;
    }
}