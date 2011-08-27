<?php

namespace Rbs\Payment\Template;

interface LoaderInterface
{
    public function __construct($source);
    public function load();
}