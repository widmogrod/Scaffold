<?php

class Scaffold_Extension_ConditionalTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$options = array(
			'extensions' => array(
				'Conditional'
			)
		);

		$system 			= realpath(__DIR__.'/../../../');	
		$container 			= new Scaffold_Container($system, $options);
		$this->scaffold 	= $container->build();
		$this->object 		= new Scaffold_Extension_Conditional();
	}

/*
	public function test_process()
	{
		$dir = __DIR__ . '/_files/original/';
		
		foreach(glob($dir.'*.css') as $original)
		{
			# The expected output
			$expected = file_get_contents(str_replace('/_files/original/','/_files/expected/',$original));
			
			# Create and parse the source
			$source = new Scaffold_Source_File($original);
			$this->object->process($source,$this->scaffold);
			
			// Remove unnecessary whitespace
			$actual = trim($source->contents);
			$actual = explode("\n",$actual);
			$actual = array_filter($actual, array($this,'remove_empty_lines'));
			$actual = implode("\n",$actual);
			
			# The source contents should equal the expect output
			$this->assertEquals($expected,$actual);
		}
	}
*/
	
	/**
	 * @test
	 */
	public function test_post_inline_default()
	{
		# Setup paths and expected file content
		$originalFilename = __DIR__ . '/_files/original/inline.css';
		$expectedFilename = str_replace('/_files/original/', '/_files/expected/', $originalFilename);
		$expectedContent = file_get_contents($expectedFilename);
		
		# Create and parse the source
		$source = new Scaffold_Source_File($originalFilename);
		$this->object->process($source, $this->scaffold);
	}
}