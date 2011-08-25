<?php

namespace Rbs\Payment\Gateway;

abstract class AbstractGateway
{
    protected $fields;
    protected $gatewayUrl;
    protected $testMode;
    protected $template;

    public function __construct()
    {
        $this->setDefaults();
        $this->init();
    }

    private function setDefaults()
    {
        $this->testMode = false;
        $this->template = <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Proceeding to Payment</title>
    <style type="text/css">
        div.notice {
            border: 5px solid #ccc;
            background-color: #efefef;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="notice">
        Please wait, your order is being processed and you will be redirected to the payment website.
        [FORM]
    </div>
</body>
</html>
HTML;

    }

    public function setTemplate($html)
    {
        if ($this->isValidTemplate($html)) {
            $this->template = $html;
        } else {
            throw new \InvalidArgumentException("Provided template HTML does not contain required [FORM] placeholder.");
        }
    }

    private function isValidTemplate($html)
    {
        return (!empty($html) AND strpos($html, "[FORM]") !== false);
    }

    public function proceed()
    {
        if ($this->validate()) {

            $formHtml = $this->getPaymentForm();
            $output = str_replace('[FORM]', $formHtml, $this->template);
            $output = str_replace("<body>", '<body onLoad="document.forms[\'gateway_form\'].submit();">', $output);

            echo $output;
        }
    }

    private function getPaymentForm()
    {
        $formHtml = '';
        $formHtml .= '<form method="POST" name="gateway_form" action="' . $this->gatewayUrl . '">';

        foreach ($this->fields as $name => $value) {
             $formHtml .= "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\"/>";
        }

        $formHtml .= '<p>If you are not automatically redirected to payment website within 5 seconds.';
        $formHtml .= '<input type="submit" value="Click Here"></p>';
        $formHtml .= '</form>';

        return $formHtml;
    }

    protected function addField($key, $value)
    {
        if (!array_key_exists($key, $this->fields)) {
            $this->fields[$key] = $value;
        } else {
            throw new \InvalidArgumentException("Key $key already exists.");
        }
    }

    protected function getField($key)
    {
        if (!array_key_exists($key, $this->fields)) {
            return $this->fields[$key];
        } else {
            throw new \InvalidArgumentException("Key $key doesn't exist.");
        }
    }

    abstract protected function init();
    abstract protected function validate();
    
    abstract public function setCurrency($currency);
    abstract public function setAccountIdentifier($identifier);
    abstract public function setReturnUrl($url);
    abstract public function setNotificationUrl($url);
    abstract public function setCustomField($key, $value);
    abstract public function setSingleItem($name, $amount);
    abstract public function setOrderNumber($orderNumber);
    abstract public function setTestMode($mode);
}