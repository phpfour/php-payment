<?php

namespace Rbs\Payment;

class Factory
{
    public static function factory($provider)
    {
        $file = __DIR__ . '/Gateway/' . $provider . '.php';

        if (!file_exists($file)) {
            throw new \InvalidArgumentException('Invalid gateway specified.');
        }

        $class = 'Rbs\\Payment\\Gateway\\' . $provider;

        include_once ($file);
        $instance = new $class();
        
        return $instance;
    }
}