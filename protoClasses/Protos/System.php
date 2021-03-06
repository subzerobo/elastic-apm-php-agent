<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protos/system.proto

namespace Protos;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\GPBWrapperUtils;

/**
 * Generated from protobuf message <code>protos.System</code>
 */
class System extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string architecture = 1;</code>
     */
    private $architecture = '';
    /**
     * Generated from protobuf field <code>string hostname = 2;</code>
     */
    private $hostname = '';
    /**
     * Generated from protobuf field <code>string platform = 3;</code>
     */
    private $platform = '';
    /**
     * Generated from protobuf field <code>.protos.System.Container container = 4;</code>
     */
    private $container = null;
    /**
     * Generated from protobuf field <code>.protos.System.Kubernetes kubernetes = 5;</code>
     */
    private $kubernetes = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $architecture
     *     @type string $hostname
     *     @type string $platform
     *     @type \Protos\System\Container $container
     *     @type \Protos\System\Kubernetes $kubernetes
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protos\System::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string architecture = 1;</code>
     * @return string
     */
    public function getArchitecture()
    {
        return $this->architecture;
    }

    /**
     * Generated from protobuf field <code>string architecture = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setArchitecture($var)
    {
        GPBUtil::checkString($var, True);
        $this->architecture = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string hostname = 2;</code>
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Generated from protobuf field <code>string hostname = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setHostname($var)
    {
        GPBUtil::checkString($var, True);
        $this->hostname = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string platform = 3;</code>
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Generated from protobuf field <code>string platform = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setPlatform($var)
    {
        GPBUtil::checkString($var, True);
        $this->platform = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.protos.System.Container container = 4;</code>
     * @return \Protos\System\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Generated from protobuf field <code>.protos.System.Container container = 4;</code>
     * @param \Protos\System\Container $var
     * @return $this
     */
    public function setContainer($var)
    {
        GPBUtil::checkMessage($var, \Protos\System_Container::class);
        $this->container = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.protos.System.Kubernetes kubernetes = 5;</code>
     * @return \Protos\System\Kubernetes
     */
    public function getKubernetes()
    {
        return $this->kubernetes;
    }

    /**
     * Generated from protobuf field <code>.protos.System.Kubernetes kubernetes = 5;</code>
     * @param \Protos\System\Kubernetes $var
     * @return $this
     */
    public function setKubernetes($var)
    {
        GPBUtil::checkMessage($var, \Protos\System_Kubernetes::class);
        $this->kubernetes = $var;

        return $this;
    }

}

