PHP Payment
===========

A payment library for PHP that supports Paypal, Authorize.net and 2Checkout.

* Easy to integrate
* Supports single order checkout
* Unified interface for all payment gateways
* Template for redirection page

Usage
=====

    $paypal = \Rbs\Payment\Factory::factory('Paypal');
    $paypal->setAccountIdentifier('emran@rightbrainsolution.com');
    $paypal->setCurrency('USD');
    $paypal->setReturnOnSuccessUrl('http://phpfour.com/payment/paypal_success.php');
    $paypal->setReturnOnFailureUrl('http://phpfour.com/payment/paypal_failure.php');
    $paypal->setNotificationUrl('http://phpfour.com/payment/paypal_ipn.php');
    $paypal->setSingleItem('T-shirt', 4.99, "1001");
    $paypal->proceed();

More updates coming.