<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-17
 * Time: 14:48
 */
namespace Subzerobo\ElasticApmPhpAgent\ActionWrappers;

use Subzerobo\ElasticApmPhpAgent\Wrappers\SpanContextData;

class APMRedisActionWrapper extends APMHandlerAbstract
{
    const SPAN_TYPE = "redis";

    /**
     * APM Redis Handler
     * @param        $redisObject
     * @param string $actionName
     * @param array  $actionData
     *
     * @return mixed|void
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerAlreadyStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\UnknownTransactionException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-17 16:29
     */
    public function handleBefore($redisObject, string $actionName, array $actionData = [])
    {
        parent::handleBefore($redisObject,$actionName,$actionData);
        if ($this->apmAgent->isActive()) {
            $txName = $this->apmAgent->getCurrentTransactionName();
            $tx     = $this->apmAgent->getTransactionEvent($txName);

            $this->span = $tx->startSpan("Redis : " . $redisObject->getServerIdentifier(), self::SPAN_TYPE);
        }
    }

    /**
     * APM Redis Handler
     *
     * @param        $redisObject
     * @param string $actionName
     * @param array  $actionData
     *
     * @return mixed|void
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStoppedException
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-17 16:28
     */
    public function handleAfter($redisObject, string $actionName, array $actionData = [])
    {
        parent::handleAfter($redisObject,$actionName,$actionData);

        $total  = $this->getData('total');

        // Store Duration in $GLOBALS to send as header
        $GLOBALS['redisProfileEach'][$redisObject->getServerIdentifier()] += $total;
        $GLOBALS['redisProfileEachCnt'][$redisObject->getServerIdentifier()]++;

        $GLOBALS['redisProfile'] += $total;
        $GLOBALS['redisProfileCnt']++;

        if ($this->apmAgent->isActive()) {
            $contextData = new SpanContextData();
            $contextData->setDB($redisObject->getServerIdentifier(),$actionName . " " . implode(' ', $actionData),'Redis');
            $this->span->setContextFromContextData($contextData);

            $this->span->stop();
        }
    }

}