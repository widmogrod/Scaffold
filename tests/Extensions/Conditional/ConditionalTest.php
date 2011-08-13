<?php

class Scaffold_Extension_ConditionalTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$options = array(
			// 'extensions' => array(
			// 	'Conditional'
			// ),
			'Conditional' => array(
				'commentNotMatched' => false
			)
		);

		$system 			= realpath(__DIR__.'/../../../');	
		$container 			= new Scaffold_Container($system, $options);
		$this->scaffold 	= $container->build();
		$this->object 		= new Scaffold_Extension_Conditional($options);
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
	// public function test_browser_data()
	// 	{
	// 		$expected = Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME;
	// 		$actual = $this->object->getBrowserData();
	// 
	// 		$this->assertEquals($expected,$actual);
	// 	}

	# test browser name

	public function test_browser_name_default()
	{
		$expected = Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME;
		$this->object->setBrowserName($expected);
		$actual = $this->object->getBrowserName();
		
		$this->assertEquals($expected,$actual);
	}
	
	public function test_browser_name_auto()
	{
		$expected = 'msie'; // default user agent for PHPUnit
		$actual = $this->object->getBrowserName();
		
		$this->assertEquals($expected,$actual);
	}
	
	public function test_browser_name_set()
	{
		$expected = 'firefox';
		$this->object->setBrowserName($expected);
		$actual = $this->object->getBrowserName();
		
		$this->assertEquals($expected,$actual);
	}
	
	# test browser abbr name
	
	public function test_browser_name_abbr_default()
	{
		# Set default browser name
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		
		$expected = Scaffold_Extension_Conditional::DEFAULT_BROWSER_ABBR_NAME;
		$actual = $this->object->getBrowserAbbrName();
		
		$this->assertEquals($expected,$actual);
	}
	
	public function test_browser_name_abbr_auto()
	{
		# Set enviromental browser name
		$browserName = 'msie'; // default user agent for PHPUnit
		$this->object->setBrowserName($browserName);
		
		$expected = 'ie';
		$actual = $this->object->getBrowserAbbrName();
		
		$this->assertEquals($expected,$actual);
	}
	
	public function test_browser_name_abbr_set()
	{
		$expected = 'o';
		$this->object->setBrowserName('Opera');
		$actual = $this->object->getBrowserAbbrName();
		
		$this->assertEquals($expected,$actual);
	}
	
	# test version
		
	public function test_browser_varsion_default()
	{
		# Set default browser version
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		$expected = Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION;
		$actual = $this->object->getBrowserVersion();
		
		$this->assertEquals($expected, $actual);
	}
	
	public function test_browser_varsion_auto()
	{
		$expected = 7; // defaul user agent version MSIE 7
		$actual = $this->object->getBrowserVersion();
		$this->assertEquals($expected, $actual);
	}
	
	public function test_browser_version_set()
	{
		$expected = 1.2;
		$this->object->setBrowserVersion($expected);
		$actual = $this->object->getBrowserVersion();
		
		$this->assertEquals($expected,$actual);
	}
	
	
	
	/**
	 * @test
	 */
	public function test_post_inline_default()
	{
		# Setup paths and expected file content
		$originalFilename = __DIR__ . '/_files/original/inline.css';
		$expectedFilename = str_replace('/_files/original/', '/_files/expected/', $originalFilename);
		$expectedContent = file_get_contents($expectedFilename);
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_File($originalFilename);
		$this->object->process($source, $this->scaffold);
		$actualContent = $source->contents;
		
		$expectedContent = $this->removeWhiteSpace($expectedContent);
		$actualContent = $this->removeWhiteSpace($actualContent);

		$this->assertEquals($expectedContent, $actualContent);
	}
	
	/**
	 * @test
	 */
	public function test_post_inline_auto_detect()
	{
		# Setup paths and expected file content
		$originalFilename = __DIR__ . '/_files/original/inline.css';
		$expectedFilename = __DIR__ . '/_files/expected/inline_ie.css'; // Default PHPnit detect MSIE 7
		$expectedContent = file_get_contents($expectedFilename);
		
		# Create and parse the source
		$source = new Scaffold_Source_File($originalFilename);
		$this->object->process($source, $this->scaffold);
		$actualContent = $source->contents;
		
		$expectedContent = $this->removeWhiteSpace($expectedContent);
		$actualContent = $this->removeWhiteSpace($actualContent);

		$this->assertEquals($expectedContent, $actualContent);
	}
	
	/**
	 * @test
	 */
	public function test_post_block_default()
	{
		# Setup paths and expected file content
		$originalFilename = __DIR__ . '/_files/original/block.css';
		$expectedFilename = str_replace('/_files/original/', '/_files/expected/', $originalFilename);
		$expectedContent = file_get_contents($expectedFilename);
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_File($originalFilename);
		$this->object->process($source, $this->scaffold);
		$actualContent = $source->contents;
		
		$expectedContent = $this->removeWhiteSpace($expectedContent);
		$actualContent = $this->removeWhiteSpace($actualContent);

		$this->assertEquals($expectedContent, $actualContent);
	}
	
	/**
	 * @test
	 */
	public function test_post_block_auto_detect()
	{
		# Setup paths and expected file content
		$originalFilename = __DIR__ . '/_files/original/block.css';
		$expectedFilename = __DIR__ . '/_files/expected/block_ie.css'; // Default PHPnit detect MSIE 7
		$expectedContent = file_get_contents($expectedFilename);
		
		# Create and parse the source
		$source = new Scaffold_Source_File($originalFilename);
		$this->object->process($source, $this->scaffold);
		$actualContent = $source->contents;
		
		$expectedContent = $this->removeWhiteSpace($expectedContent);
		$actualContent = $this->removeWhiteSpace($actualContent);

		$this->assertEquals($expectedContent, $actualContent);
	}
	
	
	
	public function test_compare_version_default_eq()
	{
		$original = '[FF 3] display:block';
		$expected = 'display:block';
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_String($original);
		$this->object->process($source, $this->scaffold);
		
		$actual = $source->contents;

		$actual = $this->removeWhiteSpace($actual);
		$expected = $this->removeWhiteSpace($expected);

		$this->assertEquals($expected, $actual);
	}
	
	public function test_compare_version_default_lte()
	{
		$original = '[FF <= 3] display:block';
		$expected = 'display:block';
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_String($original);
		$this->object->process($source, $this->scaffold);
		
		$actual = $source->contents;

		$actual = $this->removeWhiteSpace($actual);
		$expected = $this->removeWhiteSpace($expected);

		$this->assertEquals($expected, $actual);
	}
	
	public function test_compare_version_default_gte()
	{
		$original = '[FF >= 3] display:block';
		$expected = 'display:block';
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_String($original);
		$this->object->process($source, $this->scaffold);
		
		$actual = $source->contents;

		$actual = $this->removeWhiteSpace($actual);
		$expected = $this->removeWhiteSpace($expected);

		$this->assertEquals($expected, $actual);
	}
	
	public function test_compare_version_default_nwq()
	{
		$original = '[FF != 2] display:block';
		$expected = 'display:block';
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_String($original);
		$this->object->process($source, $this->scaffold);
		
		$actual = $source->contents;

		$actual = $this->removeWhiteSpace($actual);
		$expected = $this->removeWhiteSpace($expected);

		$this->assertEquals($expected, $actual);
	}
	
	public function test_compare_version_default_lt()
	{
		$original = '[FF < 4] display:block';
		$expected = 'display:block';
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_String($original);
		$this->object->process($source, $this->scaffold);
		
		$actual = $source->contents;

		$actual = $this->removeWhiteSpace($actual);
		$expected = $this->removeWhiteSpace($expected);

		$this->assertEquals($expected, $actual);
	}
	
	public function test_compare_version_default_gt()
	{
		$original = '[FF > 2] display:block';
		$expected = 'display:block';
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_String($original);
		$this->object->process($source, $this->scaffold);
		
		$actual = $source->contents;

		$actual = $this->removeWhiteSpace($actual);
		$expected = $this->removeWhiteSpace($expected);

		$this->assertEquals($expected, $actual);
	}

	public function test_compare_version_float_default_gt()
	{
		$original = '[FF > 2.2] display:block';
		$expected = 'display:block';
		
		# Set default values for Extension
		$this->object->setBrowserName(Scaffold_Extension_Conditional::DEFAULT_BROWSER_NAME);
		$this->object->setBrowserVersion(Scaffold_Extension_Conditional::DEFAULT_BROWSER_VERSION);
		
		# Create and parse the source
		$source = new Scaffold_Source_String($original);
		$this->object->process($source, $this->scaffold);
		
		$actual = $source->contents;

		$actual = $this->removeWhiteSpace($actual);
		$expected = $this->removeWhiteSpace($expected);

		$this->assertEquals($expected, $actual);
	}
	
	
	# test helpers
	
	private function removeWhiteSpace($content)
	{
		// Remove unnecessary whitespace
		$content = trim($content);
		$content = explode("\n",$content);
		$content = array_filter($content, array($this,'remove_empty_lines'));
		$content = implode("\n",$content);
		return $content;
	}
	
	private function remove_empty_lines($value)
	{
		$value = trim($value);
		return ($value != '');
	}
}