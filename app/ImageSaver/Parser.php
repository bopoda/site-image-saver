<?php

class ImageSaver_Parser implements ImageSaver_Interface
{
	use Logger_Trait;

	private $maxImages;
	private $maxIterations;
	private $currentIteration;
	private $amountSavedImages;
	private $baseHost;
	private $allInternalLinks = array();
	private $visitedInternalLinks = array();

	public function parse($domain)
	{
		$domain = preg_match('@^https?://@', $domain) ? $domain : ('http://' . $domain);
		$urlParts = parse_url($domain);
		$this->baseHost = $urlParts['host'];
		$rootUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . '/';

		$this->allInternalLinks[] = $rootUrl;

		$curlClient = new Curl_Client();
		do {
			$this->currentIteration++;
			$notVisitedLinks = array_diff($this->allInternalLinks, $this->visitedInternalLinks);
			$url = current($notVisitedLinks);

			$this->visitedInternalLinks[] = $url;
			try {
				$response = $curlClient->sendGetRequest($url);
			}
			catch (Curl_Exception $e) {
				$this->writeToLog("$url exception: " . $e->getMessage());
				continue;
			}

			$links = $this->parseAllLinks($response);

			$internalLinks = $this->getInternalLinks($links);
			$this->addNewInternalLinksToProcessing($internalLinks);

			$this->writeToLog("$url - " . count($internalLinks) . ' internal link(s)');
		} while (
			$this->hasNotVisitedLinks()
			&& !$this->hasExceededMaxIterations()
			&& !$this->hasExceededMaxImages()
		);
	}

	public function parseAllImages(Curl_Response $response)
	{
		$domDocument = new DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$domDocument->loadHTML($response->getBody());
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

	public function setMaxIterations($limit)
	{
		$this->maxIterations = $limit;

		return $this;
	}

	public function setMaxImages($maxImages)
	{
		$this->maxImages = $maxImages;

		return $this;
	}

	private function hasExceededMaxIterations()
	{
		if ($this->maxIterations && $this->currentIteration >= $this->maxIterations) {
			$this->writeToLog("Parser has exceeded iterations limit ($this->maxIterations).");
			return true;
		}

		return false;
	}

	private function hasExceededMaxImages()
	{
		if ($this->maxImages && $this->amountSavedImages >= $this->maxImages) {
			$this->writeToLog("Parser has exceeded images limit ($this->maxImages).");
			return true;
		}

		return false;
	}

	private function hasNotVisitedLinks()
	{
		return count($this->allInternalLinks) > count($this->visitedInternalLinks);
	}

	/**
	 * @param Curl_Response $response
	 * @return array
	 */
	private function parseAllLinks(Curl_Response $response)
	{
		$domDocument = new DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$domDocument->loadHTML($response->getBody());
		libxml_use_internal_errors($internalErrors);
		$elements = $domDocument->getElementsByTagName('a');

		$links = array();
		foreach ($elements as $element) {
			$href = trim($element->getAttribute('href'));
			$href = preg_replace('/#.*/', '', $href); //remove anchors
			if (preg_match('/^javascript:/i', $href)) { //skip js links
				continue;
			}
			$links[] = $response->absolutizeUrl($href);
		}

		$links = array_values(array_unique($links));

		return $links;
	}

	private function addNewInternalLinksToProcessing(array $internalLinks)
	{
		foreach ($internalLinks as $internalLink) {
			if (!in_array($internalLink, $this->allInternalLinks)) {
				$this->allInternalLinks[] = $internalLink;
			}
		}
	}

	private function getInternalLinks(array $links)
	{
		$internalLinks = array();
		foreach ($links as $link) {
			$urlParts = parse_url($link);
			if (strnatcasecmp($urlParts['host'], $this->baseHost) === 0) {
				$internalLinks[] = $link;
			}
		}

		return $internalLinks;
	}
}

