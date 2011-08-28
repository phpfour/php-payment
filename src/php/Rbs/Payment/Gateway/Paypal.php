<?php

namespace Rbs\Payment\Gateway;
 
class Paypal extends \Rbs\Payment\Gateway\AbstractGateway
{
    protected function init()
    {
        $this->setTestMode(false);
        $this->setReturnMethodAsPost();
        $this->setCommandAsClick();
    }

    private function setCommandAsClick()
    {
        $this->fields->cmd = '_xclick';
    }

    private function setReturnMethodAsPost()
    {
        $this->fields->rm = '2';
    }

    public function setCurrency($currency)
    {
        if (!empty($currency)) {
            $this->fields->currency_code = $currency;
        }
    }

    public function setAccountIdentifier($identifier)
    {
        if (!empty($identifier)) {
            $this->fields->business = $identifier;
        }
    }

    public function setReturnOnSuccessUrl($url)
    {
        if (!empty($url)) {
            $this->fields->return = $url;
        }
    }

    public function setReturnOnFailureUrl($url)
    {
        if (!empty($url)) {
            $this->fields->cancel_return = $url;
        }
    }

    public function setNotificationUrl($url)
    {
        if (!empty($url)) {
            $this->fields->notify_url = $url;
        }
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
            $this->fields->amount = $amount;
        }

        if (!empty($id)) {
            $this->fields->item_number = $id;
        }
    }

    public function setOrderNumber($orderNumber)
    {
        if (!empty($orderNumber)) {
            $this->fields->order_number = $orderNumber;
        }
    }

    public function setTestMode($mode)
    {
        if ($mode) {
            $this->gatewayUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            $this->testMode = true;
        } else {
            $this->gatewayUrl = 'https://www.paypal.com/cgi-bin/webscr';
            $this->testMode = false;
        }
    }

    protected function validate()
    {
        $requiredFields = array(
            'business',
            'currency_code',
            'return',
            'cancel_return',
            'notify_url',
            'item_name',
            'amount'
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
        $this->fields->cmd = '_notify-validate';

        $this->httpClient->setUrl($this->gatewayUrl);
        $this->httpClient->setPort(80);
        $this->httpClient->setMethod("POST");
        $this->httpClient->setData($this->fields->getAll());

        $response = $this->httpClient->sendRequest();

        if (strpos($response, "VERIFIED") !== false) {
            return true;
        }

        return false;
    }
}