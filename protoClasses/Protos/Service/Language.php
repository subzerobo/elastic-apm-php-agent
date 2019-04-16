<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protos/service.proto

namespace Protos\Service;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\GPBWrapperUtils;

/**
 * Generated from protobuf message <code>protos.Service.Language</code>
 */
class Language extends \Google\Protobuf\Internal\Message
{
    /**
     * Name and version of the programming language used
     *
     * Generated from protobuf field <code>string name = 1;</code>
     */
    private $name = '';
    /**
     * Generated from protobuf field <code>string version = 2;</code>
     */
    private $version = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $name
     *           Name and version of the programming language used
     *     @type string $version
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protos\Service::initOnce();
        parent::__construct($data);
    }

    /**
     * Name and version of the programming language used
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Name and version of the programming language used
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string version = 2;</code>
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Generated from protobuf field <code>string version = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setVersion($var)
    {
        GPBUtil::checkString($var, True);
        $this->version = $var;

        return $this;
    }

}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Language::class, \Protos\Service_Language::class);

