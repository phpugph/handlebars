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
class HandlebarsHandlerTest extends TestCase
{
  /**
   * @var HandlebarsHandler
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new HandlebarsHandler;
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Handlebars\HandlebarsHandler::__construct
   */
  public function test__construct()
  {
    $this->object = new HandlebarsHandler;

    $this->assertInstanceOf(HandlebarsHandler::class, $this->object);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::compile
   */
  public function testCompile()
  {
    $template = $this->object->compile('{{foo}}{{{foo}}}');

    $results = $template(['foo' => '<strong>foo</strong>']);

    $this->assertEquals('&lt;strong&gt;foo&lt;/strong&gt;<strong>foo</strong>', $results);

    $template = $this->object->compile('{{foo}}{{{foo}}}');

    $results = $template(['foo' => '<strong>foo</strong>']);

    $this->assertEquals('&lt;strong&gt;foo&lt;/strong&gt;<strong>foo</strong>', $results);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::setBars
   * @covers Handlebars\HandlebarsHandler::getBars
   */
  public function testSetBars()
  {
    $template = file_get_contents(__DIR__ . '/assets/barstest.html');
    $template = $this->object->setBars('[]')->compile($template);

    $expected = '1<b>Post</b>&lt;b&gt;Post&lt;/b&gt;{{post_title}}'."\n";

    $results = $template([
      'post_id' => '1',
      'post_title' => '<b>Post</b>'
    ]);

    $this->assertEquals($expected, $results);
    $this->assertEquals('[]', $this->object->getBars());
  }

  /**
   * @covers Handlebars\HandlebarsHandler::getCache
   */
  public function testGetCache()
  {
    $this->assertNull($this->object->getCache());
  }

  /**
   * @covers Handlebars\HandlebarsHandler::getHelper
   */
  public function testGetHelper()
  {
    $this->assertInstanceOf('Closure', $this->object->getHelper('if'));
    $this->assertNull($this->object->getHelper('foobar'));
  }

  /**
   * @covers Handlebars\HandlebarsHandler::getHelpers
   */
  public function testGetHelpers()
  {
    $helpers = $this->object->getHelpers();
    $this->assertTrue(is_array($helpers));
  }

  /**
   * @covers Handlebars\HandlebarsHandler::getPartial
   */
  public function testGetPartial()
  {
    $this->assertNull($this->object->getPartial('foobar'));
  }

  /**
   * @covers Handlebars\HandlebarsHandler::getPartials
   */
  public function testGetPartials()
  {
    $partials = $this->object->getPartials();
    $this->assertTrue(is_array($partials));
  }

  /**
   * @covers Handlebars\HandlebarsHandler::registerHelper
   */
  public function testRegisterHelper()
  {
    //simple helper
    $this->object->registerHelper('root', function() {
      return '/some/root';
    });

    $template = $this->object->compile('{{root}}/bower_components/eve-font-awesome/awesome.css');

    $results = $template();
    $this->assertEquals('/some/root/bower_components/eve-font-awesome/awesome.css', $results);

    $found = false;
    $self = $this;
    $template = $this
      ->object
      ->reset()
      ->registerHelper('foo', function(
        $bar,
        $four,
        $true,
        $null,
        $false,
        $zoo
      ) use ($self, &$found) {
        $self->assertEquals('', $bar);
        $self->assertEquals(4, $four);
        $self->assertTrue($true);
        $self->assertNull($null);
        $self->assertFalse($false);
        $self->assertEquals('foobar', $zoo);
        $found = true;
        return $four + 1;
      })
      ->compile('{{foo bar 4 true null false zoo}}');

    $results = $template(['zoo' => 'foobar']);
    $this->assertTrue($found);
    $this->assertEquals(5, $results);

    $found = false;
    $template = $this
      ->object
      ->reset()
      ->registerHelper('foo', function(
        $number,
        $something1,
        $number2,
        $something2
      ) use ($self, &$found) {
        $self->assertEquals(4.5, $number);
        $self->assertEquals(4, $number2);
        $self->assertEquals('some"thi " ng', $something1);
        $self->assertEquals("some'thi ' ng", $something2);
        $found = true;

        return $something1.$something2;
      })
      ->compile('{{{foo 4.5 \'some"thi " ng\' 4 "some\'thi \' ng"}}}');

    $results = $template();

    $this->assertTrue($found);
    $this->assertEquals('some"thi " ng'."some'thi ' ng", $results);

    //attributes test
    $found = false;
    $template = $this
      ->object
      ->reset()
      ->registerHelper('foo', function(
        $bar,
        $number,
        $something1,
        $number2,
        $something2,
        $options
      ) use ($self, &$found) {
        $self->assertEquals(4.5, $number);
        $self->assertEquals(4, $number2);
        $self->assertEquals('some"thi " ng', $something1);
        $self->assertEquals("some'thi ' ng", $something2);
        $self->assertFalse($options['hash']['dog']);
        $self->assertEquals('meow', $options['hash']['cat']);
        $self->assertEquals('squeak squeak', $options['hash']['mouse']);

        $found = true;
        return $number2 + 1;
      })
      ->compile(
        '{{foo 4bar4 4.5 \'some"thi " ng\' 4 "some\'thi \' ng" '
        .'dog=false cat="meow" mouse=\'squeak squeak\'}}');

    $results = $template(['zoo' => 'foobar']);
    $this->assertTrue($found);
    $this->assertEquals(5, $results);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::registerPartial
   */
  public function testRegisterPartial()
  {
    //basic
    $template = $this
      ->object
      ->reset()
      ->registerPartial('foo', 'This is {{ foo }}')
      ->registerPartial('bar', 'Foo is not {{ bar }}')
      ->compile('{{> foo }} ... {{> bar }}');

    $results = $template(['foo' => 'FOO', 'bar' => 'BAR']);

    $this->assertEquals('This is FOO ... Foo is not BAR', $results);

    //with scope
    $template = $this
      ->object
      ->reset()
      ->registerPartial('foo', 'This is {{ foo }}')
      ->registerPartial('bar', 'Foo is not {{ bar }}')
      ->compile('{{> foo }} ... {{> bar zoo}}');

    $results = $template(['foo' => 'FOO', 'bar' => 'BAR', 'zoo' => ['bar' => 'ZOO']]);

    $this->assertEquals('This is FOO ... Foo is not ZOO', $results);

    //with attributes
    $template = $this
      ->object
      ->reset()
      ->registerPartial('foo', 'This is {{ foo }}')
      ->registerPartial('bar', 'Foo is not {{ something }}')
      ->compile('{{> foo }} ... {{> bar zoo something="Amazing"}}');

    $results = $template(['foo' => 'FOO', 'bar' => 'BAR', 'zoo' => ['bar' => 'ZOO']]);

    $this->assertEquals('This is FOO ... Foo is not Amazing', $results);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::reset
   */
  public function testReset()
  {
    //simple helper
    $helper = $this
      ->object
      ->registerHelper('root', function() {
        return '/some/root';
      })
      ->reset()
      ->getHelper('root');

    $this->assertNull($helper);

    $helper = $this
      ->object
      ->getHelper('if');

    $this->assertInstanceOf('Closure', $helper);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::setCache
   */
  public function testSetCache()
  {
    $this->assertEquals('/foo/bar', $this->object->setCache('/foo/bar')->getCache());
  }

  /**
   * @covers Handlebars\HandlebarsHandler::setPrefix
   */
  public function testSetPrefix()
  {
    $instance = $this->object->setPrefix('foobar');
    $this->assertInstanceOf('Handlebars\HandlebarsHandler', $instance);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::unregisterHelper
   */
  public function testUnregisterHelper()
  {
    $instance = $this->object->unregisterHelper('if');
    $this->assertInstanceOf('Handlebars\HandlebarsHandler', $instance);

    $this->assertNull($instance->getHelper('if'));
  }

  /**
   * @covers Handlebars\HandlebarsHandler::unregisterPartial
   */
  public function testUnregisterPartial()
  {
    $instance = $this->object
      ->registerPartial('foo', 'bar')
      ->unregisterPartial('foo');

    $this->assertInstanceOf('Handlebars\HandlebarsHandler', $instance);

    $this->assertNull($instance->getPartial('foo'));
  }

  /**
   * @covers Handlebars\HandlebarsHandler::__callResolver
   */
  public function test__callResolver()
  {
    $actual = $this->object->__callResolver(ResolverCallStub::class, [])->foo('bar');
    $this->assertEquals('barfoo', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::addResolver
   */
  public function testAddResolver()
  {
    $actual = $this->object->addResolver(ResolverCallStub::class, function() {});
    $this->assertInstanceOf('Handlebars\HandlebarsHandler', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::getResolverHandler
   */
  public function testGetResolverHandler()
  {
    $actual = $this->object->getResolverHandler();
    $this->assertInstanceOf('UGComponents\Resolver\ResolverInterface', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::resolve
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
   * @covers Handlebars\HandlebarsHandler::resolveShared
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
   * @covers Handlebars\HandlebarsHandler::resolveStatic
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
   * @covers Handlebars\HandlebarsHandler::setResolverHandler
   */
  public function testSetResolverHandler()
  {
    $actual = $this->object->setResolverHandler(new ResolverHandlerStub);
    $this->assertInstanceOf('Handlebars\HandlebarsHandler', $actual);
  }

  /**
   * @covers Handlebars\HandlebarsHandler::i
   */
  public function testI()
  {
    $actual = HandlebarsHandler::i();
    $this->assertInstanceOf('Handlebars\HandlebarsHandler', $actual);
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