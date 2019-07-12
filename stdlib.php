<?php
define('None', null);
define('True', true);
define('False', false);

class dict implements ArrayAccess {
    var $container = array();

    function __construct($items = array())
    {
        if (!$items) {
            $this->container = [];
        } else {
            $this->container = $items;
        }
    }

    function __toString()
    {
        return json_encode($this->container);
    }

    function __get($name)
    {
        return $this->container[$name];
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    function clear() {
        $this->container = array();
    }

    function get($name, $default=False)
    {
        if (array_key_exists($name, $this->container)) {
            $return_value = $this->container[$name];

        } elseif ($default) {
            $return_value = $default;

        } else {
            $return_value = None;
        }

        return $return_value;
    }

    function get_value($name, $default=False) {
        $value = $this->get($name, $default);

        if (empty($value)) {
            return $default;
        } else {
            return $value;
        }

    }

    function items() {
        return $this->container;
    }

    function iteritems() {
        return $this->container;
    }

    function keys() {
        return array_keys($this->container);
    }

    function pop($name) {
        $return_value = $this->container[$name];
        unset($this->container[$name]);

        return $return_value;
    }

    function popitem() {
        return array_pop($this->container);
    }

    function setdefault($name, $default=None) {
        if (array_key_exists($name, $this->container)) {
            return $this->container[$name];
        } else {
            $this->container[$name] = $default;
            return $default;
        }
    }

    function update(array $new_items) {
        $this->container = array_merge($this->container, $new_items);
    }

    function values() {
        return array_values($this->container);
    }
}

function isinstance($value, $object) {
    return (gettype($value) == gettype($object));
}

function len($iterable) {
    return count($iterable);
}

function int($value) {
    return intval($value);
}
