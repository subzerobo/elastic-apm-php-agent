<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-08
 * Time: 17:27
 */

namespace Subzerobo\ElasticApmPhpAgent\Stores;


class AbstractStore
{
    /**
     * @var array
     */
    protected $store = [];

    /**
     * Gets the Store item by name
     *
     * @param string $name
     *
     * @return mixed|null
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 13:59
     */
    public function fetch(string $name) {
        return $this->store[$name] ?? null;
    }

    /**
     * Gets the list of store items
     *
     * @return array
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 13:59
     */
    public function list() : array {
        return $this->store;
    }

    /**
     * Check if store items exists
     *
     * @param $name
     *
     * @return bool
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 13:59
     */
    public function exists($name) : bool {
        return (isset($this->store[$name]) === true);
    }

    /**
     * Check if store is empty
     *
     * @return bool
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 13:59
     */
    public function isEmpty() : bool
    {
        return empty($this->store);
    }

    /**
     * Reset the Store | empty the store
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 14:23
     */
    public function reset()
    {
        $this->store = [];
    }

    /**
     * Returns number of objects in store
     * 
     * @return int
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-14 09:33
     */
    public function count() {
        return count($this->store);
    }

    /**
     * Returns last object in store
     * @return mixed
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-06-11 10:55
     */
    public function last() {
        return end($this->store);
    }

    public function rename($oldKey, $newKey) {
        $this->store[$newKey] = $this->store[$oldKey];
        unset($this->store[$oldKey]);
    }
}