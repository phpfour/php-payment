<?php

class PaypalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Rbs\Payment\Gateway\Paypal
     */
    private $paypal;

    public function setUp()
    {
        $this->paypal = new \Rbs\Payment\Gateway\Paypal();
    }

    public function testCanInitialize()
    {
        $this->assertInstanceOf("Rbs\\Payment\\Gateway\\AbstractGateway", $this->paypal);
    }

    /**
     * @expectedException Exception
     */
    public function testEnsuresMinimumConfiguration()
    {
        $this->paypal->proceed();
    }

    public function testProceedsToPaypalOnSingleItemPurchase()
    {
        $this->setupSingleItem();
        
        ob_start();
        $this->paypal->proceed();
        $output = ob_get_clean();

        $this->assertContains('<form method="POST" name="gateway_form" action="https://www.paypal.com/cgi-bin/webscr">', $output);
        $this->checkDefaultMessages($output);
        $this->checkSingleItem($output);
    }

    public function testProceedsToPaypalSandboxOnTestMode()
    {
        $this->setupSingleItem();
        $this->paypal->setTestMode(true);

        ob_start();
        $this->paypal->proceed();
        $output = ob_get_clean();

        $this->assertContains('<form method="POST" name="gateway_form" action="https://www.sandbox.paypal.com/cgi-bin/webscr">', $output);
    }

    public function testVerifiesSuccessfulTransaction()
    {
        $this->markTestIncomplete();
    }

    private function setupSingleItem()
    {
        $this->paypal->setAccountIdentifier('emran@rightbrainsolution.com');
        $this->paypal->setCurrency('USD');
        $this->paypal->setReturnOnSuccessUrl('http://phpfour.com/payment/paypal_success.php');
        $this->paypal->setReturnOnFailureUrl('http://phpfour.com/payment/paypal_failure.php');
        $this->paypal->setNotificationUrl('http://phpfour.com/payment/paypal_ipn.php');
        $this->paypal->setSingleItem('T-shirt', 4.99, "1001");
    }

    private function checkDefaultMessages($output)
    {
        $this->assertContains("Please wait, your order is being processed and you will be redirected to the payment website.", $output);
        $this->assertContains('<p>If you are not automatically redirected to payment website within 5 seconds, <input type="submit" value="Click here"></p>', $output);
    }

    private function checkSingleItem($output)
    {
        $this->assertContains('<input type="hidden" name="rm" value="2"/>', $output);
        $this->assertContains('<input type="hidden" name="cmd" value="_xclick"/>', $output);
        $this->assertContains('<input type="hidden" name="business" value="emran@rightbrainsolution.com"/>', $output);
        $this->assertContains('<input type="hidden" name="currency_code" value="USD"/>', $output);
        $this->assertContains('<input type="hidden" name="return" value="http://phpfour.com/payment/paypal_success.php"/>', $output);
        $this->assertContains('<input type="hidden" name="cancel_return" value="http://phpfour.com/payment/paypal_failure.php"/>', $output);
        $this->assertContains('<input type="hidden" name="notify_url" value="http://phpfour.com/payment/paypal_ipn.php"/>', $output);
        $this->assertContains('<input type="hidden" name="item_name" value="T-shirt"/>', $output);
        $this->assertContains('<input type="hidden" name="amount" value="4.99"/>', $output);
        $this->assertContains('<input type="hidden" name="item_number" value="1001"/>', $output);
    }
}