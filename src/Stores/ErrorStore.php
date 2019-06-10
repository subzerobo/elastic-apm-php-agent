<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-06-10
 * Time: 13:32
 */

namespace Subzerobo\ElasticApmPhpAgent\Stores;


use Subzerobo\ElasticApmPhpAgent\Wrappers\ErrorEvent;

class ErrorStore extends AbstractStore
{
    /**
     * Register an Error in AbstractStore
     * @param ErrorEvent $errorEvent
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-10 14:30
     */
    public function register(ErrorEvent $errorEvent)
    {
        $this->store[$errorEvent->getId()] = $errorEvent;
    }
}