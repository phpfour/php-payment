PHP Payment
===========

A payment library for PHP that supports Paypal, Authorize.net and 2Checkout.

* Easy to integrate
* Supports single order checkout
* Unified interface for all payment gateways
* Template for redirection page

Usage
=====

Initiating a paypal checkout

    $paypal = \Rbs\Payment\Factory::factory('Paypal');

    $paypal->setAccountIdentifier('emran@rightbrainsolution.com');
    $paypal->setCurrency('USD');
    $paypal->setSingleItem('T-shirt', 4.99, "1001");

    $paypal->setReturnOnSuccessUrl('http://phpfour.com/payment/paypal_success.php');
    $paypal->setReturnOnFailureUrl('http://phpfour.com/payment/paypal_failure.php');
    $paypal->setNotificationUrl('http://phpfour.com/payment/paypal_ipn.php');

    $paypal->proceed();

Verifying paypal IPN response

    $paypal = \Rbs\Payment\Factory::factory('Paypal');
    $client = \Rbs\Payment\Http\Client();

    $paypal->populate($_POST);
    $paypal->setHttpClient($client);

    $status = $paypal->verify();

More updates coming soon.

Dependencies
============

To run the unit tests, you must have the following excellent libraries installed via PEAR:

* PHPUnit - https://github.com/sebastianbergmann/phpunit
* Mockery - https://github.com/padraic/mockery
* Gradwell Autoloader - https://github.com/Gradwell/Autoloader