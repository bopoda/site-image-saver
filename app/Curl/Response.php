<?php

class Curl_Response
{
	private $body;
	private $code = 200;
	private $finalUrl;
	private $initialUrl;
	private $headers;

	public function getBody()
	{
		return $this->body;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function getFinalUrl()
	{
		return $this->finalUrl;
	}

	public function getInitialUrl()
	{
		return $this->initialUrl;
	}

	public function setBody($body)
	{
		$this->body = $body;

		return $this;
	}

	public function setCode($code)
	{
		$this->code = $code;

		return $this;
	}

	public function setFinalUrl($finalUrl)
	{
		$this->finalUrl = $finalUrl;

		return $this;
	}

	public function setInitialUrl($initialUrl)
	{
		$this->initialUrl = $initialUrl;

		return $this;
	}

	public function absolutizeUrl($url)
	{
		$url = trim($url);
		if (!$url) {
			return $this->finalUrl;
		}

		$urlInfo = parse_url($url);

		//already has scheme
		if (!empty($urlInfo['scheme'])) {
			return $url;
		}

		$baseUrlInfo = parse_url($this->finalUrl);

		//use current scheme
		if (substr($url, 0, 2) == '//') {
			return $baseUrlInfo['scheme'] . ':' . $url;
		}
		else {
			//relative
			if ($url[0] != '/') {
				return $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . rtrim($baseUrlInfo['path'], '/') . '/' . $url;
			}
			//absolute
			else {
				return $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . ltrim($url, '/');
			}
		}
	}
}

