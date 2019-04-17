<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-17
 * Time: 17:41
 */

namespace Subzerobo\ElasticApmPhpAgent\ActionWrappers;


use GuzzleHttp\Psr7\Request;
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
        }
    }

    /**
     * @param Request       $request
     * @param string $actionName
     * @param array  $actionData
     *
     * @return mixed|void
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-17 18:19
     */
    public function handleAfter($request, string $actionName, array $actionData = [])
    {
        parent::handleAfter($request, $actionName, $actionData);
        if ($this->apmAgent->isActive()) {

            $contextData = new SpanContextData();
            $contextData->setHttp('');
            $this->span->setContextFromContextData($contextData);

            $this->span->stop();

            "context" => [
                'http' => [
                    'url'               => $response->request->raw_headers,
                    'method'            => $type,
                    'status_code'       => $response->code
                ],
                'tags' => [
                    'response_headers'  => $response->raw_headers,
                    'curl_info'         => urldecode(http_build_query($response->meta_data,'',', ')),
                    'body'              => $response->raw_body,
                ]
            ]
        }
    }

}