<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-17
 * Time: 16:22
 */

namespace Subzerobo\ElasticApmPhpAgent\ActionWrappers;


use Subzerobo\ElasticApmPhpAgent\ApmAgent;
use Subzerobo\ElasticApmPhpAgent\Wrappers\SpanEvent;
use Subzerobo\PHPActionWrapper\HandlerAbstract;

class APMHandlerAbstract extends HandlerAbstract
{
    /**
     * @var ApmAgent
     */
    public $apmAgent;

    /**
     * @var SpanEvent
     */
    protected $span;

    public function __construct(ApmAgent $apmAgent)
    {
        $this->apmAgent = $apmAgent;
    }
}