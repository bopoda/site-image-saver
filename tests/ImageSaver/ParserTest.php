<?php

class ImageSaver_ParserTest extends PHPUnit_Framework_TestCase
{
	public function testParse()
	{
		$parser = new ImageSaver_Parser();
		$parser
			->setMaxIterations(2)
			->setMaxImages(100)
			->parse('http://jeka.by/');
	}
}