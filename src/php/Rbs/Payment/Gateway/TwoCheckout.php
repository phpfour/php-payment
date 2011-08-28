<?php

namespace Rbs\Payment\Gateway;
 
class TwoCheckout extends \Rbs\Payment\Gateway\AbstractGateway
{
    protected function init()
    {
        $this->gatewayUrl = 'https://www.2checkout.com/checkout/purchase';
        $this->setTestMode(false);
    }

    public function setCurrency($currency)
    {
        if (!empty($currency)) {
            $this->fields->tco_currency = $currency;
        }
    }

    public function setAccountIdentifier($identifier)
    {
        if (!empty($identifier)) {
            $this->fields->sid = $identifier['sid'];
            $this->fields->secret = $identifier['secret'];
        }
    }

    public function setReturnOnSuccessUrl($url)
    {
        if (!empty($url)) {
            $this->fields->x_Receipt_Link_URL = $url;
        }
    }

    public function setReturnOnFailureUrl($url)
    {
        throw new \Exception("2CO does not support a failure URL.");
    }

    public function setNotificationUrl($url)
    {
        throw new \Exception("2CO does not support an IPN URL.");
    }

    public function setCustomField($key, $value)
    {
        if (!empty($key) && !empty($value)) {
            $this->fields->$key = $value;
        }
    }

    public function setSingleItem($name, $amount, $id = null)
    {
        if (!empty($name)) {
            $this->fields->item_name = $name;
        }

        if (!empty($amount)) {
            $this->fields->total = $amount;
        }
    }

    public function setOrderNumber($orderNumber)
    {
        if (!empty($orderNumber)) {
            $this->fields->cart_order_id = $orderNumber;
        }
    }

    public function setTestMode($mode)
    {
        if ($mode) {
            $this->fields->demo = "Y";
            $this->testMode = true;
        }
    }

    protected function validate()
    {
        $requiredFields = array(
            'sid',
            'tco_currency',
            'x_Receipt_Link_URL',
            'total',
            'cart_order_id'
        );

        foreach ($requiredFields as $field) {
            if (!isset($this->fields->$field)) {
                throw new \Exception("Required parameter {$field} has not been set.");
            }
        }

        return true;
    }

    public function verify()
    {
        $vendorNumber = ($this->fields->vendor_number != '') ? $this->fields->vendor_number : $this->fields->sid;
        $orderNumber = $this->fields->order_number;
        $orderTotal = $this->fields->total;

        // If demo mode, the order number must be forced to 1
        if($this->testMode) {
            $orderNumber = "1";
        }

        // Calculate md5 hash as 2co formula: md5(secret_word + vendor_number + order_number + total)
        $key = strtoupper(md5($this->fields->secret . $vendorNumber . $orderNumber . $orderTotal));

        // Verify if the key is accurate
        if($this->fields->key == $key || $this->fields->x_MD5_Hash == $key) {
            return true;
        } else {
            return false;
        }
    }
}