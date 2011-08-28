<?php

namespace Rbs\Payment;

class Fields
{
    private $fields;

    public function __get($key)
    {
        if (array_key_exists($key, $this->fields)) {
            return $this->fields[$key];
        }
    }

    public function __set($key, $value)
    {
        $this->fields[$key] = $value;
    }

    public function __isset($key)
    {
        return array_key_exists($key, $this->fields);
    }

    public function getAll()
    {
        return $this->fields;
    }
}