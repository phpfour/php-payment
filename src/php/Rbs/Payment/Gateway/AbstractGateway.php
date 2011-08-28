<?php

namespace Rbs\Payment\Gateway;

abstract class AbstractGateway
{
    protected $fields;
    protected $gatewayUrl;
    protected $testMode;
    protected $placeholders;
    protected $templateLoader;
    protected $httpClient;
    
    public function __construct()
    {
        $this->setDefaults();
        $this->init();
    }

    private function setDefaults()
    {
        $this->fields = new \Rbs\Payment\Fields();

        $this->placeholders = array(
            'paymentForm' => '',
            'pageTitle' => 'Proceeding to payment website...',
            'redirectButton' => "Click here",
            'primaryMessage' => "Please wait, your order is being processed and you will be taken to the payment website.",
            'redirectMessage' => "If you are not automatically redirected to payment website within 5 seconds, <br /><br />"
        );

        $this->setTemplateLoader(new \Rbs\Payment\Template\Loader\Filesystem(__DIR__ . "/../templates/basic.html"));
    }

    public function setTemplateLoader(\Rbs\Payment\Template\LoaderInterface $loader)
    {
        if ($this->isValidTemplate($loader)) {
            $this->templateLoader = $loader;
        } else {
            throw new \InvalidArgumentException("Provided template loader does not return required placeholders.");
        }
    }

    private function isValidTemplate(\Rbs\Payment\Template\LoaderInterface $loader)
    {
        $html = $loader->load();
        
        if (strpos($html, 'paymentForm') === false) {
            return false;
        }

        return true;
    }

    public function proceed()
    {
        if ($this->validate()) {
            $this->placeholders['paymentForm'] = $this->getPaymentForm();
            $parser = new \Rbs\Payment\Template\Parser($this->templateLoader);
            echo $parser->parse($this->placeholders);
        }
    }

    private function getPaymentForm()
    {
        $formHtml = '';
        $formHtml .= '<form method="POST" name="gateway_form" action="' . $this->gatewayUrl . '">';

        foreach ($this->fields->getAll() as $name => $value) {
            $formHtml .= "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\"/>";
        }

        $formHtml .= '<p>{redirectMessage}';
        $formHtml .= '<input type="submit" value="{redirectButton}"></p>';
        $formHtml .= '</form>';

        return $formHtml;
    }

    public function populate($fields = array())
    {
        foreach ($fields as $key => $value) {
            $this->fields->$key = $value;
        }
    }

    public function setHttpClient(\Rbs\Payment\Http\Client $client)
    {
        $this->httpClient = $client;
    }

    abstract protected function init();
    abstract protected function validate();
    
    abstract public function setCurrency($currency);
    abstract public function setAccountIdentifier($identifier);
    abstract public function setReturnOnSuccessUrl($url);
    abstract public function setReturnOnFailureUrl($url);
    abstract public function setNotificationUrl($url);
    abstract public function setCustomField($key, $value);
    abstract public function setSingleItem($name, $amount, $id = null);
    abstract public function setOrderNumber($orderNumber);
    abstract public function setTestMode($mode);
    abstract public function verify();
}