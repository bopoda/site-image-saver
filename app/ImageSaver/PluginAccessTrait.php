<?php

trait ImageSaver_PluginAccessTrait
{
	/**
	 * @var ImageSaver_PluginInterface[]
	 */
	private $plugins = array();

	public function registerPlugin(ImageSaver_PluginInterface $plugin)
	{
		$this->plugins[] = $plugin;

		return $this;
	}

	public function getPlugins()
	{
		return $this->plugins;
	}

	public function onStart()
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onStart();
		}
	}

	public function onFinish()
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onFinish();
		}
	}

	public function onError()
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onError();
		}
	}

	public function processResponse(Curl_Response $response)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->processResponse($response);
		}
	}
}