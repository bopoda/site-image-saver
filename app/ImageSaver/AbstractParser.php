<?php

abstract class ImageSaver_AbstractParser
{
	use Logger_Trait;

	private $maxIterations;
	private $currentIteration = 0;

	/**
	 * @param int $limit
	 * @return $this
	 */
	public function setMaxIterations($limit)
	{
		$this->maxIterations = $limit;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getIteration()
	{
		return $this->currentIteration;
	}

	/**
	 * @return bool
	 */
	protected function hasExceededMaxIterations()
	{
		if ($this->maxIterations && $this->currentIteration >= $this->maxIterations) {
			$this->writeToLog("Parser has exceeded iterations limit ($this->maxIterations).");
			return true;
		}

		return false;
	}

	/**
	 * Increment current iteration
	 */
	protected function nextIteration()
	{
		$this->currentIteration++;
	}

	/**
	 * The simplest check if a binary data
	 *
	 * @param string $imgBinary
	 * @return bool
	 */
	protected function isBinary($imgBinary)
	{
		return (bool) preg_match('~[^\x20-\x7E\t\r\n]~', $imgBinary);
	}

	/**
	 * @param Curl_Response $response
	 * @return array
	 */
	protected function parseAllLinks(Curl_Response $response)
	{
		$domDocument = new DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$body = $this->removeHTMLComments($response->getBody());
		$domDocument->loadHTML($body);
		libxml_use_internal_errors($internalErrors);
		$elements = $domDocument->getElementsByTagName('a');

		$links = array();
		foreach ($elements as $element) {
			$href = trim($element->getAttribute('href'));
			$href = preg_replace('/#.*/', '', $href); //remove anchors
			if (preg_match('/^(javascript|mail):/i', $href)) { //skip js and mail links
				continue;
			}
			$links[] = $response->absolutizeUrl($href);
		}

		$links = array_values(array_unique($links));

		return $links;
	}

	/**
	 * @param Curl_Response $response
	 * @return array
	 */
	protected function parseAllImagesLinks(Curl_Response $response)
	{
		$domDocument = new DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$body = $this->removeHTMLComments($response->getBody());
		$domDocument->loadHTML($body);
		libxml_use_internal_errors($internalErrors);
		$elements = $domDocument->getElementsByTagName('img');

		$images = array();
		foreach ($elements as $element) {
			$src = trim($element->getAttribute('src'));
			$images[] = $response->absolutizeUrl($src);
		}

		$images = array_values(array_unique($images));

		return $images;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	protected function removeHTMLComments($string)
	{
		return preg_replace('/<!--.*?-->/si', '', $string);
	}

	/**
	 * @param array $links
	 * @param string $baseHost
	 * @return array
	 */
	protected function getInternalLinks(array $links, $baseHost)
	{
		$internalLinks = array();
		foreach ($links as $link) {
			$urlParts = parse_url($link);
			if (!empty($urlParts['host']) && strnatcasecmp($urlParts['host'], $baseHost) === 0) {
				$internalLinks[] = $link;
			}
		}

		return $internalLinks;
	}
}

