<?php

use \Mockery as m;

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
        $client = $this->setupHttpClient('VERIFIED');
        $ipnData = $this->setupTestIpnData();

        $this->paypal->populate($ipnData);
        $this->paypal->setHttpClient($client);
        $status = $this->paypal->verify();

        $this->assertTrue($status);
    }

    public function testVerifiesInvalidTransaction()
    {
        $client = $this->setupHttpClient('INVALID');
        $ipnData = $this->setupTestIpnData();

        $this->paypal->populate($ipnData);
        $this->paypal->setHttpClient($client);
        $status = $this->paypal->verify();

        $this->assertFalse($status);
    }

    private function setupTestIpnData()
    {
        $testIpnResponse = "mc_gross=19.95&protection_eligibility=Eligible&address_status=confirmed&payer_id=LPLWNMTBWMFAY&tax=0.00&address_street=1+Main+St&payment_date=20%3A12%3A59+Jan+13%2C+2009+PST&payment_status=Completed&charset=windows-1252&address_zip=95131&first_name=Test&mc_fee=0.88&address_country_code=US&address_name=Test+User&notify_version=2.6&custom=&payer_status=verified&address_country=United+States&address_city=San+Jose&quantity=1&verify_sign=AtkOfCXbDm2hu0ZELryHFjY-Vb7PAUvS6nMXgysbElEn9v-1XcmSoGtf&payer_email=gpmac_1231902590_per%40paypal.com&txn_id=61E67681CH3238416&payment_type=instant&last_name=User&address_state=CA&receiver_email=gpmac_1231902686_biz%40paypal.com&payment_fee=0.88&receiver_id=S8XGHLYDW9T3S&txn_type=express_checkout&item_name=&mc_currency=USD&item_number=&residence_country=US&test_ipn=1&handling_amount=0.00&transaction_subject=&payment_gross=19.95&shipping=0.00";
        $dataItems = explode("&", $testIpnResponse);
        $ipnData = array();

        foreach ($dataItems as $item) {
            $values = explode("=", $item);
            $ipnData[$values[0]] = $values[1];
        }
        return $ipnData;
    }

    private function setupHttpClient($status)
    {
        $client = m::mock('Rbs\Payment\Http\Client');
        $client->shouldReceive('sendRequest')->andReturn($status);
        $client->shouldReceive('setUrl');
        $client->shouldReceive('setPort');
        $client->shouldReceive('setMethod');
        $client->shouldReceive('setData');
        return $client;
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