<?php

define('APP_TOPDIR', realpath(__DIR__ . '/../../php'));
set_include_path(APP_TOPDIR . PATH_SEPARATOR . get_include_path());

require_once('gwc.autoloader.php');