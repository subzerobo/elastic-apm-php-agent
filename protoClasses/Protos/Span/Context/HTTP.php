<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protos/spanv2.proto

namespace Protos\Span\Context;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\GPBWrapperUtils;

/**
 * Generated from protobuf message <code>protos.Span.Context.HTTP</code>
 */
class HTTP extends \Google\Protobuf\Internal\Message
{
    /**
     * The raw url of the correlating http request.
     *
     * Generated from protobuf field <code>string url = 1;</code>
     */
    private $url = '';
    /**
     * The status code of the http request.
     *
     * Generated from protobuf field <code>int32 status_code = 2[json_name = "status_code"];</code>
     */
    private $status_code = 0;
    /**
     * The method of the http request.
     *
     * Generated from protobuf field <code>string method = 3;</code>
     */
    private $method = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $url
     *           The raw url of the correlating http request.
     *     @type int $status_code
     *           The status code of the http request.
     *     @type string $method
     *           The method of the http request.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protos\Spanv2::initOnce();
        parent::__construct($data);
    }

    /**
     * The raw url of the correlating http request.
     *
     * Generated from protobuf field <code>string url = 1;</code>
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * The raw url of the correlating http request.
     *
     * Generated from protobuf field <code>string url = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setUrl($var)
    {
        GPBUtil::checkString($var, True);
        $this->url = $var;

        return $this;
    }

    /**
     * The status code of the http request.
     *
     * Generated from protobuf field <code>int32 status_code = 2[json_name = "status_code"];</code>
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * The status code of the http request.
     *
     * Generated from protobuf field <code>int32 status_code = 2[json_name = "status_code"];</code>
     * @param int $var
     * @return $this
     */
    public function setStatusCode($var)
    {
        GPBUtil::checkInt32($var);
        $this->status_code = $var;

        return $this;
    }

    /**
     * The method of the http request.
     *
     * Generated from protobuf field <code>string method = 3;</code>
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * The method of the http request.
     *
     * Generated from protobuf field <code>string method = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setMethod($var)
    {
        GPBUtil::checkString($var, True);
        $this->method = $var;

        return $this;
    }

}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HTTP::class, \Protos\Span_Context_HTTP::class);

