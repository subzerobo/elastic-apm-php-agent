<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-17
 * Time: 17:41
 */

namespace Subzerobo\ElasticApmPhpAgent\ActionWrappers;


use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Subzerobo\ElasticApmPhpAgent\Wrappers\SpanContextData;

class APMGuzzleActionWrapper extends APMHandlerAbstract
{
    const SPAN_TYPE = "request";

    /**
     * APM CURL Handler
     *
     * @param Request       $request
     * @param string $actionName
     * @param array  $actionData
     *
     * @return mixed|void
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerAlreadyStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\UnknownTransactionException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-17 18:18
     */
    public function handleBefore($request, string $actionName, array $actionData = [])
    {
        parent::handleBefore($request, $actionName, $actionData);
        if ($this->apmAgent->isActive()) {
            $txName = $this->apmAgent->getCurrentTransactionName();
            $tx     = $this->apmAgent->getTransactionEvent($txName);

            $this->span = $tx->startSpan("cURL" , self::SPAN_TYPE);
            $this->span->setSubType("http");

            $this->setData("url", $request->getUri()->__toString());
            $this->setData("method", $request->getMethod());
        }
    }

    /**
     * @param Response  $response
     * @param string    $actionName
     * @param array     $actionData
     *
     * @return mixed|void
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStoppedException
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-18 10:38
     */
    public function handleAfter($response, string $actionName, array $actionData = [])
    {
        parent::handleAfter($response, $actionName, $actionData);

        if ($this->apmAgent->isActive()) {

            $url = $this->getData("url");
            $method = $this->getData("method");
            $headers = $this->getData("headers");

            $spanContextData = new SpanContextData();
            $spanContextData->setHttp($url,$response->getStatusCode(),$method);

            $tags = [
                'response_headers' => json_encode($response->getHeaders()),
                'protocol_version' => $response->getProtocolVersion(),
            ];
            $spanContextData->setTags($tags);

            $this->span->setContextFromContextData($spanContextData);

            $this->span->stop();
        }
    }

}