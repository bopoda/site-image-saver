<?php

class Curl_Client
{
	public function sendGetRequest($url, array $headers = array())
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($ch);

		if ($html === false) {
			$error = curl_error($ch);
			throw new Curl_Exception($error);
		}

		$curlInfo = curl_getinfo($ch);

		curl_close($ch);

		$response = new Curl_Response();
		$response
			->setBody($html)
			->setInitialUrl($url)
			->setFinalUrl($curlInfo['url'])
			->setCode($curlInfo['http_code']);

		return $response;
	}
}
