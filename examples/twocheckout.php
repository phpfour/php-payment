<?php

define('APP_TOPDIR', realpath(__DIR__ . '/../src/php'));
set_include_path(APP_TOPDIR . PATH_SEPARATOR . get_include_path());

require_once('gwc.autoloader.php');

$twoCheckout = \Rbs\Payment\Factory::factory('TwoCheckout');

$twoCheckout->setAccountIdentifier(array('sid' => 1234567, 'secret' => 'tango'));
$twoCheckout->setCurrency('USD');
$twoCheckout->setSingleItem('T-shirt', 4.99, "1001");
$twoCheckout->setOrderNumber(100);

$twoCheckout->setReturnOnSuccessUrl('http://phpfour.com/payment/twocheckout_success.php');

$twoCheckout->proceed();