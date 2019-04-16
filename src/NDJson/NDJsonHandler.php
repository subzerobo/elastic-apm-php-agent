<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-13
 * Time: 17:09
 */

namespace Subzerobo\ElasticApmPhpAgent\NDJson;


class NDJsonHandler
{

    private $depth;
    private $output;

    public function __construct($depth = 512)
    {
        if ($depth !== 512 && PHP_VERSION < 5.5) {
            throw new \BadMethodCallException('Depth parameter is only supported on PHP 5.5+');
        }
    }

    public function write($name,array $data) {
        if (PHP_VERSION_ID < 50500) {
            $found = null;
            set_error_handler(function ($error) use (&$found) {
                $found = $error;
            });
        }

        // encode data with options given in ctor
        if ($this->depth === 512) {
            $data = json_encode($data);
        } else {
            $data = json_encode($data, 0, $this->depth);
        }

        $this->plainWrite($name,$data);
    }

    public function plainWrite($name, $data) {
        $this->output.= "{\"$name\": " . $data . "}" . "\n";
    }

    public function getOutput() {
        // Convert timestamp values from string to integer

        $re = '/("timestamp":("(\d{16})"))/m';
        $this->output = preg_replace($re, '"timestamp":$3',$this->output);
        return $this->output;
    }

    public function flushOutput() {
        $this->output = "";
    }
}