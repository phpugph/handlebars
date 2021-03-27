<?php //-->
/**
 * This file is part of the Handlebars PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Handlebars;

use StdClass;
use PHPUnit\Framework\TestCase;
use UGComponents\Resolver\ResolverHandler;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:00.
 */
class Handlebars_HandlebarsCompiler_Test extends TestCase
{
   /**
   * @var HandlebarsCompiler
   */
  protected $object;

   /**
   * @var string
   */
  protected $source;

   /**
   * @var string
   */
  protected $template1;

   /**
   * @var string
   */
  protected $template2;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp()
  {
    $handler = new HandlebarsHandler;
    $this->source = file_get_contents(__DIR__.'/assets/tokenizer.html');
    $this->object = new HandlebarsCompiler($handler, $this->source);

    $this->template1 = file_get_contents(__DIR__.'/assets/template1.php');
    $this->template2 = file_get_contents(__DIR__.'/assets/template2.php');
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown()
  {
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::__construct
   */
  public function test__construct()
  {
    $handler = new HandlebarsHandler;
    $this->object->__construct($handler, $this->source);
    $this->assertInstanceOf('Handlebars\HandlebarsHandler', $handler);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::getSource
   */
  public function testGetSource()
  {
    $template = $this->object->getSource();
    $this->assertEquals($this->source, $template);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::compile
   * @covers Handlebars\HandlebarsCompiler::getTokenizeCallback
   * @covers Handlebars\HandlebarsCompiler::generateText
   * @covers Handlebars\HandlebarsCompiler::generateVariable
   * @covers Handlebars\HandlebarsCompiler::generateEscape
   * @covers Handlebars\HandlebarsCompiler::generateOpen
   * @covers Handlebars\HandlebarsCompiler::generateClose
   * @covers Handlebars\HandlebarsCompiler::generateHelpers
   * @covers Handlebars\HandlebarsCompiler::generatePartials
   * @covers Handlebars\HandlebarsCompiler::parseArguments
   * @covers Handlebars\HandlebarsCompiler::parseArgument
   * @covers Handlebars\HandlebarsCompiler::tokenize
   * @covers Handlebars\HandlebarsCompiler::prettyPrint
   * @covers Handlebars\HandlebarsCompiler::findSection
   * @covers Handlebars\HandlebarsCompiler::trim
   */
  public function testCompile()
  {
    $actual = $this->object->compile();
    $this->assertEquals(trim($this->template1), trim($actual));

    $actual = $this->object->compile(false);
    $this->assertEquals(trim($this->template2), trim($actual));
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::setOffset
   */
  public function testSetOffset()
  {
    $actual = $this->object->setOffset(3);
    $this->assertInstanceOf('Handlebars\HandlebarsCompiler', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::__callResolver
   */
  public function test__callResolver()
  {
    $actual = $this->object->__callResolver(ResolverCallStub::class, [])->foo('bar');
    $this->assertEquals('barfoo', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::addResolver
   */
  public function testAddResolver()
  {
    $actual = $this->object->addResolver(ResolverCallStub::class, function() {});
    $this->assertInstanceOf('Handlebars\HandlebarsCompiler', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::getResolverHandler
   */
  public function testGetResolverHandler()
  {
    $actual = $this->object->getResolverHandler();
    $this->assertInstanceOf('UGComponents\Resolver\ResolverInterface', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::resolve
   */
  public function testResolve()
  {
    $actual = $this->object->addResolver(
      ResolverCallStub::class,
      function() {
        return new ResolverAddStub();
      }
    )
    ->resolve(ResolverCallStub::class)
    ->foo('bar');

    $this->assertEquals('barfoo', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::resolveShared
   */
  public function testResolveShared()
  {
    $actual = $this
      ->object
      ->resolveShared(ResolverSharedStub::class)
      ->reset()
      ->foo('bar');

    $this->assertEquals('barfoo', $actual);

    $actual = $this
      ->object
      ->resolveShared(ResolverSharedStub::class)
      ->foo('bar');

    $this->assertEquals('barbar', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::resolveStatic
   */
  public function testResolveStatic()
  {
    $actual = $this
      ->object
      ->resolveStatic(
        ResolverStaticStub::class,
        'foo',
        'bar'
      );

    $this->assertEquals('barfoo', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::setResolverHandler
   */
  public function testSetResolverHandler()
  {
    $actual = $this->object->setResolverHandler(new ResolverHandlerStub);
    $this->assertInstanceOf('Handlebars\HandlebarsCompiler', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsCompiler::bindCallback
   */
  public function testBindCallback()
  {
    $trigger = new StdClass;
    $trigger->success = null;
    $trigger->test = $this;

    $this->object->bindCallback(function() use ($trigger) {
      $trigger->success = true;
      $trigger->test->assertInstanceOf('Handlebars\HandlebarsCompiler', $this);
    });

    $this->assertInstanceOf('Handlebars\HandlebarsCompiler', $this->object);
  }
}

if(!class_exists('Handlebars\ResolverCallStub')) {
  class ResolverCallStub
  {
    public function foo($string)
    {
      return $string . 'foo';
    }
  }
}

if(!class_exists('Handlebars\ResolverAddStub')) {
  class ResolverAddStub
  {
    public function foo($string)
    {
      return $string . 'foo';
    }
  }
}

if(!class_exists('Handlebars\ResolverSharedStub')) {
  class ResolverSharedStub
  {
    public $name = 'foo';

    public function foo($string)
    {
      $name = $this->name;
      $this->name = $string;
      return $string . $name;
    }

    public function reset()
    {
      $this->name = 'foo';
      return $this;
    }
  }
}

if(!class_exists('Handlebars\ResolverStaticStub')) {
  class ResolverStaticStub
  {
    public static function foo($string)
    {
      return $string . 'foo';
    }
  }
}

if(!class_exists('Handlebars\ResolverHandlerStub')) {
  class ResolverHandlerStub extends ResolverHandler
  {
  }
}
