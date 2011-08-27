<?php

namespace Rbs\Payment\Template\Loader;
 
class Filesystem implements \Rbs\Payment\Template\LoaderInterface
{
    private $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function load()
    {
        if (!file_exists($this->source)) {
            throw new \InvalidArgumentException("Provided file source does not exist.");
        }

        $contents = file_get_contents($this->source);
        return $contents;
    }
}