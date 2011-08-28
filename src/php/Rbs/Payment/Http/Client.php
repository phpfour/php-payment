<?php

namespace Rbs\Payment\Http;

class Client
{
    private $url;
    private $port;
    private $data;
    private $method;

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function setData($data = array())
    {
        if (!empty($data)) {
            $this->data = $data;
        }
    }

    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    public function sendRequest()
    {
        $parsedUrl = parse_url($this->url);

        $postString = '';
		foreach ($this->data as $key => $value) {
			$postString .= $key .'=' . urlencode(stripslashes($value)) . '&';
		}

        $fp = fsockopen($parsedUrl['host'], $this->port, $errNum, $errStr, 30);

        if (!$fp) {
            throw new \Exception("Cannot open socket.");
        }

        fputs($fp, "{$this->method} {$parsedUrl['path']} HTTP/1.1\r\n");
        fputs($fp, "Host: {$parsedUrl['host']}\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($postString) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $postString . "\r\n\r\n");

        $response = '';
        while(!feof($fp)) {
            $response .= fgets($fp, 1024);
        }

        fclose($fp);

        return $response;
    }
}