<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-11
 * Time: 11:18
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers;


class EventMetaData
{
    /**
     * Event MetaData
     * @var array
     */
    
    private $meta = [
        'result' => 200,
        'type'   => 'request'
    ];

    /**
     * EventMetaData constructor.
     *
     * @param array $meta
     */
    public function __construct(array $meta = [])
    {
        $this->meta = array_merge($this->meta, $meta);
    }

    public function setResult(string $result) {
        $this->meta['result'] = $result;
    }

    public function setType(string $type) {
        $this->meta['type'] = $type;
    }

    public function getResult() {
        return $this->meta['result'];
    }

    public function getType() {
        return $this->meta['type'];
    }


}