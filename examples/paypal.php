<?php

define('APP_TOPDIR', realpath(__DIR__ . '/../src/php'));
set_include_path(APP_TOPDIR . PATH_SEPARATOR . get_include_path());

require_once('gwc.autoloader.php');

$paypal = \Rbs\Payment\Factory::factory('Paypal');

$paypal->setAccountIdentifier('payment@test.com');
$paypal->setCurrency('USD');
$paypal->setSingleItem('T-shirt', 4.99, "1001");
$paypal->setOrderNumber(100);

$paypal->setReturnOnSuccessUrl('http://phpfour.com/payment/paypal_success.php');
$paypal->setReturnOnFailureUrl('http://phpfour.com/payment/paypal_failure.php');
$paypal->setNotificationUrl('http://phpfour.com/payment/paypal_ipn.php');

$paypal->proceed();