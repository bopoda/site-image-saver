<?php

class Curl_ResponseTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider urlsProvider
	 */
	public function testAbsolutizeUrl($baseUrl, $url, $expectedUrl)
	{
		$response = new Curl_Response();
		$response->setFinalUrl($baseUrl);

		$result = $response->absolutizeUrl($url);

		$this->assertEquals($expectedUrl, $result);
	}

	public function urlsProvider()
	{
		return array(
			array(
				'base' => 'http://example.com/',
				'url' => '/',
				'expected' => 'http://example.com/',
			),
			array(
				'base' => 'http://example.com/',
				'url' => '',
				'expected' => 'http://example.com/',
			),
			array(
				'base' => 'http://example.com/',
				'url' => '/absolute',
				'expected' => 'http://example.com/absolute',
			),
			array(
				'base' => 'http://example.com/path',
				'url' => '/absolute',
				'expected' => 'http://example.com/absolute',
			),
			array(
				'base' => 'http://example.com/',
				'url' => 'relative/path',
				'expected' => 'http://example.com/relative/path',
			),
			array(
				'base' => 'https://example.com/path',
				'url' => 'relative/path',
				'expected' => 'https://example.com/path/relative/path',
			),
			array(
				'base' => 'http://example.com/',
				'url' => 'http://test.com/',
				'expected' => 'http://test.com/',
			),
			array(
				'base' => 'http://example.com/',
				'url' => 'https://test.com/',
				'expected' => 'https://test.com/',
			),
			array(
				'base' => 'http://example.com/',
				'url' => '//test.com/',
				'expected' => 'http://test.com/',
			),
			array(
				'base' => 'https://example.com/',
				'url' => '//test.com/',
				'expected' => 'https://test.com/',
			),
		);
	}
}