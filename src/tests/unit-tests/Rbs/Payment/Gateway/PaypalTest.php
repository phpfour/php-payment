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
}