<?php

class Curl_Response
{
	private $body;
	private $code = 200;
	private $finalUrl;
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

	public function absolutizeUrl($url)
	{
		$urlInfo = parse_url($url);

		if (!empty($urlInfo['scheme'])) {
			return $url;
		}
		else {
			$baseUrlInfo = parse_url($this->finalUrl);
			return $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . ltrim($url, '/');
		}
	}
}

