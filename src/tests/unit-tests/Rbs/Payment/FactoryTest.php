<?php

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInitialize()
    {
        $factory = \Rbs\Payment\Factory::factory('Paypal');
        $this->assertInstanceOf("Rbs\\Payment\\Gateway\\AbstractGateway", $factory);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDoesNotLoadInvalidGateway()
    {
        $factory = \Rbs\Payment\Factory::factory('Invalid');
    }
}