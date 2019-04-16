<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-11
 * Time: 15:08
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers;


use Protos\Span\Context;

class SpanContextData
{
    /**
     * @var Context
     */
    private $context;


    /**
     * SpanContextData constructor.
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data = [])
    {
        $this->context = new Context();
        if (!empty($data))
            $this->context->mergeFromJsonString(json_encode($data));
    }

    /**
     * Set the db for span context
     *
     * @param string|null $instance
     * @param string|null $statement
     * @param string|null $type
     * @param string|null $username
     *
     * @return SpanContextData
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 11:53
     */
    public function setDB(string $instance= null, string $statement=null, string $type=null, string $username=null):self {
        $dbArr = [
            'instance' => $instance,
            'statement' => $statement,
            'type' => $type,
            'user' => $username
        ];
        $this->context->setDb(new Context\DB($dbArr));
        return $this;
    }

    /**
     * Sets the http for span context
     * @param string|null $url
     * @param int|null    $status_code
     * @param string|null $method
     *
     * @return SpanContextData
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 11:53
     */
    public function setHttp(string $url=null, int $status_code=null, string $method=null):self {
        $httpArr = [
            'url' => $url,
            'status_code' => $status_code,
            'method' => $method
        ];
        $this->context->setHttp(new Context\HTTP($httpArr));
        return $this;
    }

    /**
     * Sets the Tags for the Span Context
     * @param array $tags
     *
     * @return SpanContextData
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 11:52
     */
    public function setTags(array $tags): self {
        $this->context->setTags($tags);
        return $this;
    }

    /**
     * Gets the Context Object
     *
     * @return Context
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 11:52
     */
    public function context() {
        return $this->context;
    }


}