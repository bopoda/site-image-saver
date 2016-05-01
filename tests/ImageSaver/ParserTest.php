<?php

class ImageSaver_ParserTest extends DatabaseTestCase
{
	public function testParse()
	{
		$domain = 'jeka.by';
		$maxIterations = 2;

		$parser = new ImageSaver_Parser();
		$parser->setLog(new Logger_NullLog());
		$parser
			->setMaxIterations($maxIterations)
			->setMaxImages(100)
			->parse($domain);

		$this->assertEquals($maxIterations, $parser->getIteration(), 'iterations number failed');

		$images = new Model\Images;
		$countSavedImages = $images->fetchCountByDomain($domain);
		$this->assertGreaterThanOrEqual(4, $countSavedImages, 'count saved images');
	}

	protected function getDataSet()
	{
		return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/fixture.yaml');
	}
}