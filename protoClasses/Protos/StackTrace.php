<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protos/stacktrace.proto

namespace Protos;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\GPBWrapperUtils;

/**
 * Generated from protobuf message <code>protos.StackTrace</code>
 */
class StackTrace extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string abs_path = 1[json_name = "abs_path"];</code>
     */
    private $abs_path = '';
    /**
     * Generated from protobuf field <code>int32 colno = 2;</code>
     */
    private $colno = 0;
    /**
     * Generated from protobuf field <code>string context_line = 3[json_name = "context_line"];</code>
     */
    private $context_line = '';
    /**
     * Generated from protobuf field <code>string filename = 4;</code>
     */
    private $filename = '';
    /**
     * Generated from protobuf field <code>string function = 5;</code>
     */
    private $function = '';
    /**
     * Generated from protobuf field <code>bool library_frame = 6[json_name = "library_frame"];</code>
     */
    private $library_frame = false;
    /**
     * Generated from protobuf field <code>int32 lineno = 7;</code>
     */
    private $lineno = 0;
    /**
     * Generated from protobuf field <code>string module = 8;</code>
     */
    private $module = '';
    /**
     * Generated from protobuf field <code>repeated string post_context = 9[json_name = "post_context"];</code>
     */
    private $post_context;
    /**
     * Generated from protobuf field <code>repeated string pre_context = 10[json_name = "pre_context"];</code>
     */
    private $pre_context;
    /**
     * Generated from protobuf field <code>map<string, string> vars = 11;</code>
     */
    private $vars;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $abs_path
     *     @type int $colno
     *     @type string $context_line
     *     @type string $filename
     *     @type string $function
     *     @type bool $library_frame
     *     @type int $lineno
     *     @type string $module
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $post_context
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $pre_context
     *     @type array|\Google\Protobuf\Internal\MapField $vars
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protos\Stacktrace::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string abs_path = 1[json_name = "abs_path"];</code>
     * @return string
     */
    public function getAbsPath()
    {
        return $this->abs_path;
    }

    /**
     * Generated from protobuf field <code>string abs_path = 1[json_name = "abs_path"];</code>
     * @param string $var
     * @return $this
     */
    public function setAbsPath($var)
    {
        GPBUtil::checkString($var, True);
        $this->abs_path = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 colno = 2;</code>
     * @return int
     */
    public function getColno()
    {
        return $this->colno;
    }

    /**
     * Generated from protobuf field <code>int32 colno = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setColno($var)
    {
        GPBUtil::checkInt32($var);
        $this->colno = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string context_line = 3[json_name = "context_line"];</code>
     * @return string
     */
    public function getContextLine()
    {
        return $this->context_line;
    }

    /**
     * Generated from protobuf field <code>string context_line = 3[json_name = "context_line"];</code>
     * @param string $var
     * @return $this
     */
    public function setContextLine($var)
    {
        GPBUtil::checkString($var, True);
        $this->context_line = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string filename = 4;</code>
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Generated from protobuf field <code>string filename = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setFilename($var)
    {
        GPBUtil::checkString($var, True);
        $this->filename = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string function = 5;</code>
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Generated from protobuf field <code>string function = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setFunction($var)
    {
        GPBUtil::checkString($var, True);
        $this->function = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool library_frame = 6[json_name = "library_frame"];</code>
     * @return bool
     */
    public function getLibraryFrame()
    {
        return $this->library_frame;
    }

    /**
     * Generated from protobuf field <code>bool library_frame = 6[json_name = "library_frame"];</code>
     * @param bool $var
     * @return $this
     */
    public function setLibraryFrame($var)
    {
        GPBUtil::checkBool($var);
        $this->library_frame = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 lineno = 7;</code>
     * @return int
     */
    public function getLineno()
    {
        return $this->lineno;
    }

    /**
     * Generated from protobuf field <code>int32 lineno = 7;</code>
     * @param int $var
     * @return $this
     */
    public function setLineno($var)
    {
        GPBUtil::checkInt32($var);
        $this->lineno = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string module = 8;</code>
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Generated from protobuf field <code>string module = 8;</code>
     * @param string $var
     * @return $this
     */
    public function setModule($var)
    {
        GPBUtil::checkString($var, True);
        $this->module = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated string post_context = 9[json_name = "post_context"];</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPostContext()
    {
        return $this->post_context;
    }

    /**
     * Generated from protobuf field <code>repeated string post_context = 9[json_name = "post_context"];</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPostContext($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->post_context = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated string pre_context = 10[json_name = "pre_context"];</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPreContext()
    {
        return $this->pre_context;
    }

    /**
     * Generated from protobuf field <code>repeated string pre_context = 10[json_name = "pre_context"];</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPreContext($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->pre_context = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>map<string, string> vars = 11;</code>
     * @return \Google\Protobuf\Internal\MapField
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Generated from protobuf field <code>map<string, string> vars = 11;</code>
     * @param array|\Google\Protobuf\Internal\MapField $var
     * @return $this
     */
    public function setVars($var)
    {
        $arr = GPBUtil::checkMapField($var, \Google\Protobuf\Internal\GPBType::STRING, \Google\Protobuf\Internal\GPBType::STRING);
        $this->vars = $arr;

        return $this;
    }

}

