<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 18:03
 */

namespace Subzerobo\ElasticApmPhpAgent\Stores;

use Subzerobo\ElasticApmPhpAgent\Wrappers\SpanEvent;

/**
 * Class SpanStore
 * Utilize the spans of a specific transaction
 *
 * @package Subzerobo\ElasticApmPhpAgent\Stores
 */
class SpanStore extends AbstractStore
{
    /**
     * Registers a SpanEvent in Store
     *
     * @param SpanEvent $span
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 14:24
     */
    public function register(SpanEvent $span)
    {
        // Push Span to AbstractStore
        $this->store[$span->getSpanName()] = $span;
    }
}