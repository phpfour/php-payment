<?php

namespace Rbs\Payment\Template;

class Parser
{
    private $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function parse($placeholders = array())
    {
        $output = $this->loader->load();

        foreach ($placeholders as $key => $value) {
            $needle = '{' . $key . '}';
            $output = str_replace($needle, $value, $output);
        }

        return $output;
    }
}