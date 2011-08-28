<?php

namespace Rbs\Payment\Template\Loader;
 
class String implements \Rbs\Payment\Template\LoaderInterface
{
    private $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function load()
    {
        if (!empty($this->source)) {
            throw new \InvalidArgumentException("Provided source is empty.");
        }

        return $this->source;
    }
}