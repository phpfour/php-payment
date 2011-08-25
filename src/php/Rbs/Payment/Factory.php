<?php

namespace Rbs\Payment;

class Factory
{
    public static function factory($provider)
    {
        $file = __DIR__ . '/Gateway/' . $provider . '.php';

        if (!include_once($file)) {
            throw new \Exception('Invalid gateway specified.');
        }

        $class = 'Rbs\\Payment\\Gateway\\' . $provider;
        $instance = new $class();
        
        return $instance;
    }
}