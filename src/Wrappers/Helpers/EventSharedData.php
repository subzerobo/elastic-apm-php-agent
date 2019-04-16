<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-10
 * Time: 12:32
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers\Helpers;


class EventSharedData
{
    /**
     * Holds Context Data
     *
     * @var array
     */
    private $data = [];

    private $env = [];

    private $cookies = [];

    /**
     * EventSharedData constructor.
     *
     * @param array $input
     * @param array $env
     * @param array $cookies
     */
    public function __construct($input = array(), $env = array(), $cookies = array())
    {
        if (empty($input)) {
            $input = ['user' => [], 'tags'=> [], 'custom' => []];
        }
        $this->data     = $input;
        $this->env      = $env;
        $this->cookies  = $cookies;
    }

    public function setMetaUserInfo(string $id =null, string $username = null, string $email = null) {
        if ($id || $username || $email) {
            $this->data['user']['id'] = $id;
            $this->data['user']['username'] = $username;
            $this->data['user']['email'] = $email;
        }
    }

    public function setContextSharedTags(array $tags) {
        $this->data['tags'] = $tags;
    }

    public function setContextSharedCustoms(array $custom) {
        $this->data['custom'] = $custom;
    }

    public function setEnvWhiteListKeys(array $keys) {
        $this->env = $keys;
    }

    public function setCookiesWhiteListKeys(array $keys) {
        $this->cookies = $keys;
    }

    /**
     * @return array
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 13:46
     */
    public function getMetaUserInfo() {
        return $this->data['user'];
    }

    /**
     * @return array
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 13:46
     */
    public function getContextSharedTags() {
        return $this->data['tags'];
    }

    /**
     * @return array
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 13:46
     */
    public function getContextSharedCustoms() {
        return $this->data['custom'];
    }

    /**
     * @return array
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 14:31
     */
    public function getEnvWhiteListKeys() {
        return $this->env;
    }

    /**
     * @return array
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-10 14:31
     */
    public function getCookiesWhiteListKeys() {
        return $this->cookies;
    }


    public function toArray() {
        return ['data' => $this->data, 'env' => $this->env, 'cookies' => $this->cookies];
    }

    public function merge(EventSharedData $newData = null) :self {
        if (!is_null($newData)) {
            $newDataArr     = $newData->toArray();
            $current        = $this->toArray();

            $this->data     = array_replace_recursive($current['data'], $newDataArr['data']);
            $this->env      = array_replace_recursive($current['env'], $newDataArr['env']);
            $this->cookies  = array_replace_recursive($current['cookies'], $newDataArr['cookies']);
        }
        return $this;
    }


}