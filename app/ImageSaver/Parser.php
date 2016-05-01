<?php

class ImageSaver_Parser extends ImageSaver_AbstractParser implements ImageSaver_Interface
{
	private $maxImages;
	private $amountSavedImages = 0;
	private $allInternalLinks = array();
	private $visitedInternalLinks = array();
	private $processedImagesSrc = array();

	/**
	 * @param string $domain
	 * @throws Exception
	 */
	public function parse($domain)
	{
		if (!$domain) {
			throw new Exception('got empty domain');
		}
		$domain = preg_match('@^https?://@', $domain) ? $domain : ('http://' . $domain);
		$urlParts = parse_url($domain);
		if (!$urlParts) {
			throw new Exception('bad domain name');
		}
		$baseHost = $urlParts['host'];
		$rootUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . '/';

		$this->allInternalLinks[] = $rootUrl;

		$curlClient = new Curl_Client();
		do {
			$this->nextIteration();
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

			$internalLinks = $this->getInternalLinks($links, $baseHost);
			$this->addNewInternalLinksToProcessing($internalLinks);

			$urlParts = parse_url($response->getFinalUrl());
			if (strnatcasecmp($urlParts['host'], $baseHost) !== 0) { // skip other domains
				$this->writeToLog($url . ' redirected to other domain ' . $response->getFinalUrl() . '. Skip.');
				continue;
			}

			$images = $this->parseAllImagesLinks($response);
			$images = $this->getInternalLinks($images, $baseHost);

			$this->writeToLog("$url has " . count($internalLinks) . ' internal link(s), ' . count($images) . ' internal image(s)');

			$this->saveImages($baseHost, $images);
		} while (
			$this->hasNotVisitedLinks()
			&& !$this->hasExceededMaxIterations()
			&& !$this->hasExceededMaxImages()
		);

		$this->writeToLog("Finally, saved {$this->amountSavedImages} image(s) from {$baseHost} using {$this->getIteration()} iteration(s).");
	}

	/**
	 * @param int $maxImages
	 * @return $this
	 */
	public function setMaxImages($maxImages)
	{
		$this->maxImages = $maxImages;

		return $this;
	}

	private function saveImages($domain, $images)
	{
		$savedImages = array();

		foreach ($images as $src) {
			if (in_array($src, $this->processedImagesSrc)) {
				continue;
			}
			$this->processedImagesSrc[] = $src;

			$imgBinary = file_get_contents($src);

			if ($this->isBinary($imgBinary)) {
				$images = new Model\Images();
				$images->saveImage($domain, $src, $imgBinary);
				$this->amountSavedImages++;
				$savedImages[] = $src;
			}
			else {
				$this->writeToLog("img $src is not a binary");
			}
		}

		if ($savedImages) {
			$this->writeToLog("Saved " . count($savedImages) . ' image(s): ' . print_r($savedImages, true));
		}
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

	private function addNewInternalLinksToProcessing(array $internalLinks)
	{
		foreach ($internalLinks as $internalLink) {
			if (!in_array($internalLink, $this->allInternalLinks)) {
				$this->allInternalLinks[] = $internalLink;
			}
		}
	}
}

