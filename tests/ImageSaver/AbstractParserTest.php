<?php

class ImageSaver_AbstractParserTest extends PHPUnit_Framework_TestCase
{
	public function testParseAllLinks()
	{
		$body = "
			<h2>test</h2>
			<a href='mailto:mail@gmail.com'>
			<a href='javascript:void(0)'>
			<a href='http://google.com/'>
			<a href='http://google.com/'>
			<a href=\"http://example.com/path\">
			<a href='//domain.com/article/1'>
			<a href='/absolute'>
			<a href='relative'><img src='2.jpg'>
		";

		$response = new Curl_Response();
		$response
			->setBody($body)
			->setFinalUrl('http://example.com/chunk');

		$result = $this->callParseAllLinks($response);

		$this->assertEquals(
			array(
				'http://google.com/',
				'http://example.com/path',
				'http://domain.com/article/1',
				'http://example.com/absolute',
				'http://example.com/chunk/relative',
			),
			$result
		);
	}

	public function testParseAllImages()
	{
		$body = "
			<h2>test</h2>
			<a href='mail:mail@gmail.com'>
			<a href='javascript:void(0)'>
			<a href='http://google.com/'>
			<a href='http://google.com/'>
			<a href=\"http://example.com/path\">
			<a href='//domain.com/article/1'>
			<a href='/absolute'>
			<a href='relative'><img src='2.jpg'>
			<!--<img src='https://example2.net/img.jpg' alt='' />-->
			<img src='https://example3.net/img.jpg' alt='' />
		";

		$response = new Curl_Response();
		$response
			->setBody($body)
			->setFinalUrl('http://example.com/chunk');

		$result = $this->callParseAllImagesLinks($response);

		$this->assertEquals(
			array(
				'http://example.com/chunk/2.jpg',
				'https://example3.net/img.jpg',
			),
			$result
		);
	}

	private function callParseAllLinks(Curl_Response $response)
	{
		$class = new ReflectionClass('ImageSaver_AbstractParser');
		$method = $class->getMethod('parseAllLinks');
		$method->setAccessible(true);
		$stub = $this->getMockForAbstractClass('ImageSaver_AbstractParser');

		return $method->invoke($stub, $response);
	}

	private function callParseAllImagesLinks(Curl_Response $response)
	{
		$class = new ReflectionClass('ImageSaver_AbstractParser');
		$method = $class->getMethod('parseAllImagesLinks');
		$method->setAccessible(true);
		$stub = $this->getMockForAbstractClass('ImageSaver_AbstractParser');

		return $method->invoke($stub, $response);
	}
}