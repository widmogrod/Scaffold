<?php

require_once __DIR__ .'/../_init.php';

/**
 * Test class for Scaffold_Factory.
 * Generated by PHPUnit on 2010-04-05 at 15:23:02.
 */
class Scaffold_ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Scaffold_Container
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    	$system = realpath('../');
    	$options = array
    	(
    		'production' => false,
    		'max_age' => 3600,
    		'load_paths' => array(),
    		'output_compression' => false,
    		'output_style' => 'none'
    	);
        $this->object = new Scaffold_Container($system,$options);
    }

    public function testGetResponse()
    {
    	$obj = $this->object->getResponse();
    	$this->assertEquals(get_class($obj),'Scaffold_Response');
    }

    public function testGetResponseEncoder()
    {
        $obj = $this->object->getResponseEncoder();
        $this->assertEquals(get_class($obj),'Scaffold_Response_Compressor');
    }

    public function testGetResponseCache()
    {
        $obj = $this->object->getResponseCache();
        $this->assertEquals(get_class($obj),'Scaffold_Response_Cache');
    }

    public function testGetCache()
    {
        $obj = $this->object->getCache();
        $this->assertEquals(get_class($obj),'Scaffold_Cache_File');
    }
    
    public function testBuild()
    {
    	$obj = $this->object->build();
    	$this->assertEquals(get_class($obj),'Scaffold');
    }
}
