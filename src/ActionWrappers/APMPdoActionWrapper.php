<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-17
 * Time: 16:21
 */

namespace Subzerobo\ElasticApmPhpAgent\ActionWrappers;


use Subzerobo\ElasticApmPhpAgent\Wrappers\SpanContextData;

class APMPdoActionWrapper extends APMHandlerAbstract
{

    const SPAN_TYPE = "db.mysql.query";

    /**
     * APM PDO Handler
     *
     * @param        $stmt
     * @param string $actionName
     * @param array  $actionData
     *
     * @return mixed|void
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerAlreadyStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\UnknownTransactionException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-17 16:47
     */
    public function handleBefore($stmt, string $actionName, array $actionData = [])
    {
        parent::handleBefore($stmt, $actionName, $actionData);
        if ($this->apmAgent->isActive()) {
            $txName = $this->apmAgent->getCurrentTransactionName();
            $tx     = $this->apmAgent->getTransactionEvent($txName);

            $spanName = "PDO Query : " . $stmt->getServerName();

            $this->span = $tx->startSpan($spanName, self::SPAN_TYPE);
        }

    }

    /**
     * APM PDO Handler
     *
     * @param        $stmt
     * @param string $actionName
     * @param array  $actionData
     *
     * @return mixed|void
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStartedException
     * @throws \Subzerobo\ElasticApmPhpAgent\Exceptions\TimerNotStoppedException
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-17 16:51
     */
    public function handleAfter($stmt, string $actionName, array $actionData = [])
    {
        parent::handleAfter($stmt, $actionName, $actionData);
        if ($this->apmAgent->isActive()) {
            $contextData = new SpanContextData();
            $contextData->setDB($stmt->getServerName(),$stmt->debug()->preview(),'sql','Redis');
            $this->span->setContextFromContextData($contextData);
            $this->span->stop();
        }
    }


}