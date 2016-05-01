<?php

class Plugin_Frontpage implements ImageSaver_PluginInterface
{
	public function onStart()
	{

	}

	public function onError()
	{

	}

	public function onFinish()
	{

	}

	public function processResponse(Curl_Response $response)
	{
		/*$frontpage = new Model\Frontpages;
		$frontpage->saveBody($response->getInitialUrl(), $response->getBody());*/
	}

	public function toString()
	{
		return get_class($this);
	}
}