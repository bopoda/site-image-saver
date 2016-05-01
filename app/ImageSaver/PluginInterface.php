<?php

interface ImageSaver_PluginInterface
{
	public function onStart();
	public function onFinish();
	public function onError();
	public function toString();
	public function processResponse(Curl_Response $response);
}