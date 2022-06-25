<?php //-->
/**
 * This file is part of the Handlebars PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Handlebars;

use StdClass;
use SplFileObject;
use ReflectionFunction;
use UGComponents\Resolver\ResolverTrait;
use UGComponents\Helper\BinderTrait;
use UGComponents\Helper\InstanceTrait;

/**
 * Transforms Handlebars Templates to PHP equivilent
 *
 * @vendor   PHPUGPH
 * @package  Handlebars
 * @standard PSR-2
 */
class HandlebarsCompiler
{
  use ResolverTrait, BinderTrait, InstanceTrait;

  /**
   * @const string BLOCK_TEXT_LINE
   */
  const BLOCK_TEXT_LINE = '\r\t$buffer .= \'%s\'.\n;';

  /**
   * @const string BLOCK_TEXT_LAST
   */
  const BLOCK_TEXT_LAST = '\r\t$buffer .= \'%s\';';

  /**
   * @const string BLOCK_ESCAPE_VALUE
   */
  const BLOCK_ESCAPE_VALUE = '\r\t$buffer .= $data->find(\'%s\');\r';

  /**
   * @const string BLOCK_VARIABLE_VALUE
   */
  const BLOCK_VARIABLE_VALUE = '\r\t$buffer .= htmlspecialchars($data->find(\'%s\') ?? \'\', ENT_COMPAT, \'UTF-8\');\r';

  /**
   * @const string BLOCK_ESCAPE_HELPER_OPEN
   */
  const BLOCK_ESCAPE_HELPER_OPEN = '\r\t$buffer .= $helper[\'%s\'](';

  /**
   * @const string BLOCK_ESCAPE_HELPER_CLOSE
   */
  const BLOCK_ESCAPE_HELPER_CLOSE = '\r\t);\r';

  /**
   * @const string BLOCK_VARIABLE_HELPER_OPEN
   */
  const BLOCK_VARIABLE_HELPER_OPEN = '\r\t$buffer .= htmlspecialchars((string) $helper[\'%s\'](';

  /**
   * @const string BLOCK_VARIABLE_HELPER_CLOSE
   */
  const BLOCK_VARIABLE_HELPER_CLOSE = '\r\t), ENT_COMPAT, \'UTF-8\');\r';

  /**
   * @const string BLOCK_ARGUMENT_VALUE
   */
  const BLOCK_ARGUMENT_VALUE = '$data->find(\'%s\')';

  /**
   * @const string BLOCK_OPTIONS_OPEN
   */
  const BLOCK_OPTIONS_OPEN = 'array(';

  /**
   * @const string BLOCK_OPTIONS_CLOSE
   */
  const BLOCK_OPTIONS_CLOSE = '\r\t)';

  /**
   * @const string BLOCK_OPTIONS_FN_OPEN
   */
  const BLOCK_OPTIONS_FN_OPEN = '\r\t\'fn\' => function($context = null) use ($noop, $data, &$helper) {';

  /**
   * @const string BLOCK_OPTIONS_FN_BODY_1
   */
  const BLOCK_OPTIONS_FN_BODY_1 = '\r\t\1if(is_array($context)) {';

  /**
   * @const string BLOCK_OPTIONS_FN_BODY_2
   */
  const BLOCK_OPTIONS_FN_BODY_2 = '\r\t\1\1$data->push($context);';

  /**
   * @const string BLOCK_OPTIONS_FN_BODY_3
   */
  const BLOCK_OPTIONS_FN_BODY_3 = '\r\t\1}';

  /**
   * @const string BLOCK_OPTIONS_FN_BODY_4
   */
  const BLOCK_OPTIONS_FN_BODY_4 = '\r\r\t\1$buffer = \'\';';

  /**
   * @const string BLOCK_OPTIONS_FN_BODY_5
   */
  const BLOCK_OPTIONS_FN_BODY_5 = '\r\r\t\1if(is_array($context)) {';

  /**
   * @const string BLOCK_OPTIONS_FN_BODY_6
   */
  const BLOCK_OPTIONS_FN_BODY_6 = '\r\t\1\1$data->pop();';

  /**
   * @const string BLOCK_OPTIONS_FN_BODY_7
   */
  const BLOCK_OPTIONS_FN_BODY_7 = '\r\t\1}';

  /**
   * @const string BLOCK_OPTIONS_FN_CLOSE
   */
  const BLOCK_OPTIONS_FN_CLOSE = '\r\r\t\1return $buffer;\r\t},\r';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_OPEN
   */
  const BLOCK_OPTIONS_INVERSE_OPEN = '\r\t\'inverse\' => function($context = null) use ($noop, $data, &$helper) {';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_BODY_1
   */
  const BLOCK_OPTIONS_INVERSE_BODY_1 = '\r\t\1if(is_array($context)) {';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_BODY_2
   */
  const BLOCK_OPTIONS_INVERSE_BODY_2 = '\r\t\1\1$data->push($context);';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_BODY_3
   */
  const BLOCK_OPTIONS_INVERSE_BODY_3 = '\r\t\1}';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_BODY_4
   */
  const BLOCK_OPTIONS_INVERSE_BODY_4 = '\r\r\t\1$buffer = \'\';';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_BODY_5
   */
  const BLOCK_OPTIONS_INVERSE_BODY_5 = '\r\r\t\1if(is_array($context)) {';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_BODY_6
   */
  const BLOCK_OPTIONS_INVERSE_BODY_6 = '\r\t\1\1$data->pop();';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_BODY_7
   */
  const BLOCK_OPTIONS_INVERSE_BODY_7 = '\r\t\1}';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_CLOSE
   */
  const BLOCK_OPTIONS_INVERSE_CLOSE = '\r\r\t\1return $buffer;\r\t}\r';

  /**
   * @const string BLOCK_OPTIONS_FN_EMPTY
   */
  const BLOCK_OPTIONS_FN_EMPTY = '\r\t\'fn\' => $noop,';

  /**
   * @const string BLOCK_OPTIONS_INVERSE_EMPTY
   */
  const BLOCK_OPTIONS_INVERSE_EMPTY = '\r\t\'inverse\' => $noop';

  /**
   * @const string BLOCK_OPTIONS_NAME
   */
  const BLOCK_OPTIONS_NAME = '\r\t\'name\' => \'%s\',';

  /**
   * @const string BLOCK_OPTIONS_ARGS
   */
  const BLOCK_OPTIONS_ARGS = '\r\t\'args\' => \'%s\',';

  /**
   * @const string BLOCK_OPTIONS_HASH
   */
  const BLOCK_OPTIONS_HASH = '\r\t\'hash\' => array(%s),';

  /**
   * @const string BLOCK_OPTIONS_HASH_KEY_VALUE
   */
  const BLOCK_OPTIONS_HASH_KEY_VALUE = '\'%s\' => %s';

  /**
   * @const string LAST_OPEN
   */
  const LAST_OPEN = ' LAST ';

  /**
   * @var string|null $layout
   */
  protected static $layout = null;

  /**
   * @var HandlebarsHandler|null $handlebars
   */
  protected $handlebars = null;

  /**
   * @var string $source
   */
  protected $source = '';

  /**
   * @var int $offset
   */
  protected $offset = 1;

  /**
   * @var string $bars
   */
  protected $bars = '{}';

  /**
   * Just load the source template
   *
   * @param *HandlebarsHandler $handlebars
   * @param *string      $source
   */
  public function __construct(HandlebarsHandler $handlebars, $source)
  {
    $this->source = $source;
    $this->handlebars = $handlebars;
    $this->bars = $this->handlebars->getBars();

    if (is_null(self::$layout)) {
      self::$layout = file_get_contents(__DIR__.'/layout.template');
    }
  }

  /**
   * Partially renders the text tokens
   *
   * @param *array $node
   * @param *array $open
   *
   * @return string
   */
  public function generateText($node)
  {
    $buffer = '';

    $value = explode("\n", $node['value']);
    $last = count($value) - 1;

    foreach ($value as $i => $line) {
      $line = str_replace("'", '\\\'', $line);

      if ($i === $last) {
        $buffer .= $this->prettyPrint(sprintf(self::BLOCK_TEXT_LAST, $line));
        continue;
      }

      $buffer .= $this->prettyPrint(sprintf(self::BLOCK_TEXT_LINE, $line));
    }

    return $buffer;
  }

  /**
   * Returns the source
   *
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Transform the template to code
   * that can be used independently
   *
   * @param bool $layout Whether to use the layout or raw code
   *
   * @return string
   */
  public function compile($layout = true)
  {
    $code = $this->trim($this->source);

    $reference = new StdClass();
    $reference->buffer = '';
    $reference->open = [];

    $callback = $this->getTokenizeCallback($reference);
    $this->resolve(HandlebarsTokenizer::class, $code)
      ->setBars($this->bars)
      ->tokenize($callback);

    // @codeCoverageIgnoreStart
    if (count($reference->open)) {
      throw HandlebarsException::forMissingClosing($reference->open);
    }
    // @codeCoverageIgnoreEnd

    if (!$layout) {
      return $reference->buffer;
    }

    return sprintf(self::$layout, $reference->buffer);
  }

  /**
   * Returns a code snippet
   *
   * @param *int $offset This is to preset the tabbing when generating the code
   *
   * @return HandlebarsCompiler
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
    return $this;
  }

  /**
   * Returns the tokenizer callback
   *
   * @param *StdClass $reference
   *
   * @return Closure
   */
  protected function getTokenizeCallback(StdClass $reference)
  {
    return $this->bindCallback(function ($node) use ($reference) {
      switch ($node['type']) {
        case HandlebarsTokenizer::TYPE_TEXT:
          $reference->buffer .= $this->generateText($node, $reference->open);
          break;
        case HandlebarsTokenizer::TYPE_VARIABLE_ESCAPE:
          $reference->buffer .= $this->generateEscape($node, $reference->open);
          break;
        case HandlebarsTokenizer::TYPE_VARIABLE_UNESCAPE:
          $reference->buffer .= $this->generateVariable($node, $reference->open);
          break;
        case HandlebarsTokenizer::TYPE_SECTION_OPEN:
          $reference->buffer .= $this->generateOpen($node, $reference->open);
          break;
        case HandlebarsTokenizer::TYPE_SECTION_CLOSE:
          $reference->buffer .= $this->generateClose($node, $reference->open);
          break;
      }
    });
  }

  /**
   * Partially renders the unescaped variable tokens
   *
   * @param *array $node
   * @param *array $open
   *
   * @return string
   */
  protected function generateVariable($node, &$open)
  {
    $node['value'] = trim($node['value']);

    //look out for else
    if ($node['value'] === 'else') {
      $open[$this->findSection($open)]['else'] = true;

      return $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_5, -1)
        . $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_6)
        . $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_7)
        . $this->prettyPrint(self::BLOCK_OPTIONS_FN_CLOSE)
        . $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_OPEN)
        . $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_BODY_1)
        . $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_BODY_2)
        . $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_BODY_3)
        . $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_BODY_4, 0, 1);
    }

    //lookout for tokenizer
    $tokenized = $this->tokenize($node);
    if ($tokenized) {
      return $tokenized;
    }

    list($name, $args, $hash) = $this->parseArguments($node['value']);

    //if it's a helper
    $helper = $this->resolveStatic(HandlebarsRuntime::class, 'getHelper', $name);

    if ($helper) {
      //form hash
      foreach ($hash as $key => $value) {
        $hash[$key] = sprintf(self::BLOCK_OPTIONS_HASH_KEY_VALUE, $key, $value);
      }

      $args[] = $this->prettyPrint(self::BLOCK_OPTIONS_OPEN, 0, 2)
        . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_NAME, $name))
        . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_ARGS, str_replace("'", '\\\'', $node['value'])))
        . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_HASH, implode(', \r\t', $hash)))
        . $this->prettyPrint(self::BLOCK_OPTIONS_FN_EMPTY)
        . $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_EMPTY)
        . $this->prettyPrint(self::BLOCK_OPTIONS_CLOSE, -1);

      return $this->prettyPrint(sprintf(self::BLOCK_VARIABLE_HELPER_OPEN, $name), -1)
        . $this->prettyPrint('\r\t' . implode(', \r\t', $args), 1, -1)
        . $this->prettyPrint(self::BLOCK_VARIABLE_HELPER_CLOSE);
    }

    //it's a value ?
    $value = str_replace(['[', ']', '(', ')'], '', $node['value']);
    $value = str_replace("'", '\\\'', $value);
    return $this->prettyPrint(sprintf(self::BLOCK_VARIABLE_VALUE, $value));
  }

  /**
   * Partially renders the escaped variable tokens
   *
   * @param *array $node
   * @param *array $open
   *
   * @return string
   */
  protected function generateEscape($node, &$open)
  {
    $node['value'] = trim($node['value']);

    //lookout for tokenizer
    $tokenized = $this->tokenize($node);
    // @codeCoverageIgnoreStart
    if ($tokenized) {
      return $tokenized;
    }
    // @codeCoverageIgnoreEnd

    list($name, $args, $hash) = $this->parseArguments($node['value']);

    //if it's a helper
    $helper = $this->resolveStatic(HandlebarsRuntime::class, 'getHelper', $name);

    if ($helper) {
      //form hash
      foreach ($hash as $key => $value) {
        $hash[$key] = sprintf(self::BLOCK_OPTIONS_HASH_KEY_VALUE, $key, $value);
      }

      $args[] = $this->prettyPrint(self::BLOCK_OPTIONS_OPEN, 0, 2)
        . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_NAME, $name))
        . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_ARGS, str_replace("'", '\\\'', $node['value'])))
        . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_HASH, implode(', \r\t', $hash)))
        . $this->prettyPrint(self::BLOCK_OPTIONS_FN_EMPTY)
        . $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_EMPTY)
        . $this->prettyPrint(self::BLOCK_OPTIONS_CLOSE, -1);

      return $this->prettyPrint(sprintf(self::BLOCK_ESCAPE_HELPER_OPEN, $name), -1)
        . $this->prettyPrint('\r\t' . implode(', \r\t', $args), 1, -1)
        . $this->prettyPrint(self::BLOCK_ESCAPE_HELPER_CLOSE);
    }

    //it's a value ?
    $value = str_replace(['[', ']', '(', ')'], '', $node['value']);
    $value = str_replace("'", '\\\'', $value);
    return $this->prettyPrint(sprintf(self::BLOCK_ESCAPE_VALUE, $value));
  }

  /**
   * Partially renders the section open tokens
   *
   * @param *array $node
   * @param *array $open
   *
   * @return string
   */
  protected function generateOpen(array $node, array &$open)
  {
    $node['value'] = trim($node['value']);

    //push in the node, we are going to need this to close
    $open[] = $node;

    list($name, $args, $hash) = $this->parseArguments($node['value']);

    //if it's a value
    $helper = $this->resolveStatic(HandlebarsRuntime::class, 'getHelper', $name);

    if (!$helper) {
      //run noop
      $node['value'] = 'noop ' . $node['value'];
      list($name, $args, $hash) = $this->parseArguments($node['value']);
    }

    //it's a helper
    //form hash
    foreach ($hash as $key => $value) {
      $hash[$key] = sprintf(self::BLOCK_OPTIONS_HASH_KEY_VALUE, $key, $value);
    }

    $args[] = $this->prettyPrint(self::BLOCK_OPTIONS_OPEN, 0, 2)
      . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_NAME, $name))
      . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_ARGS, str_replace("'", '\\\'', $node['value'])))
      . $this->prettyPrint(sprintf(self::BLOCK_OPTIONS_HASH, implode(', \r\t', $hash)))
      . $this->prettyPrint(self::BLOCK_OPTIONS_FN_OPEN)
      . $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_1)
      . $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_2)
      . $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_3)
      . $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_4);

    return $this->prettyPrint(sprintf(self::BLOCK_ESCAPE_HELPER_OPEN, $name), -2)
      . $this->prettyPrint('\r\t' . implode(', \r\t', $args), 1, 2);
  }

  /**
   * Partially renders the section close tokens
   *
   * @param *array $node
   * @param *array $open
   *
   * @return string
   */
  protected function generateClose(array $node, array &$open)
  {
    $node['value'] = trim($node['value']);
    // @codeCoverageIgnoreStart
    if ($this->findSection($open, $node['value']) === false) {
      throw HandlebarsException::forUnknownEnd($node['value'], $node['line']);
    }
    // @codeCoverageIgnoreEnd

    $buffer = '';

    $i = $this->findSection($open);

    if (!isset($open[$i]['else'])) {
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_5, -1);
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_6);
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_FN_BODY_7);
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_FN_CLOSE);
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_EMPTY);
    } else {
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_BODY_5);
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_BODY_6);
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_BODY_7);
      $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_INVERSE_CLOSE, -1);
    }

    unset($open[$i]);

    $buffer .= $this->prettyPrint(self::BLOCK_OPTIONS_CLOSE, -1);
    $buffer .= $this->prettyPrint(self::BLOCK_ESCAPE_HELPER_CLOSE, -1);

    return $buffer;
  }

  /**
   * Generates helpers to add to the layout
   * This is a placeholder incase we want to add in the future
   *
   * @return string
   */
  protected function generateHelpers()
  {
    // @codeCoverageIgnoreStart
    $helpers = $this->handlebars->getHelpers();

    foreach ($helpers as $name => $helper) {
      $function = new ReflectionFunction($this->handlebars->getHelper($name));

      $path = $function->getFileName();
      $lines = file_get_contents($path);
      $file = new SplFileObject($path);
      $file->seek($function->getStartLine() - 2);
      $start = $file->ftell();
      $file->seek($function->getEndLine() - 1);
      $end = $file->ftell();

      $code = preg_replace(
        '/^.*?function(\s+[^\s\\(]+?)?\s*\\((.+)\\}.*?\s*$/s',
        'function($2}',
        substr($lines, $start, $end - $start)
      );

      $helpers[$name] = sprintf(self::BLOCK_OPTIONS_HASH_KEY_VALUE, $name, $code);
    }

    return $this->prettyPrint(self::BLOCK_OPTIONS_OPEN)
      . $this->prettyPrint('\r\t')
      . implode($this->prettyPrint(',\r\t'), $helpers)
      . $this->prettyPrint(self::BLOCK_OPTIONS_CLOSE);

    // @codeCoverageIgnoreEnd
  }

  /**
   * Generates partials to add to the layout
   * This is a placeholder incase we want to add in the future
   *
   * @return string
   */
  protected function generatePartials()
  {
    // @codeCoverageIgnoreStart
    $partials = $this->handlebars->getPartials();

    foreach ($partials as $name => $partial) {
      $partials[$name] = sprintf(
        self::BLOCK_OPTIONS_HASH_KEY_VALUE,
        $name,
        "'" . str_replace("'", '\\\'', $partial) . "'"
      );
    }

    return $this->prettyPrint(self::BLOCK_OPTIONS_OPEN)
      . $this->prettyPrint('\r\t')
      . implode($this->prettyPrint(',\r\t'), $partials)
      . $this->prettyPrint(self::BLOCK_OPTIONS_CLOSE);
    // @codeCoverageIgnoreEnd
  }

  /**
   * Handlebars will give arguments in a string
   * This will transform them into a legit argument
   * array
   *
   * @param *string $string The argument string
   *
   * @return array
   */
  protected function parseArguments($string)
  {
    $args = [];
    $hash = [];

    $regex = [
      '([a-zA-Z0-9\-_]+\="[^"]*")',          // cat="meow"
      '([a-zA-Z0-9\-_]+\=\'[^\']*\')',       // mouse='squeak squeak'
      '([a-zA-Z0-9\-_]+\=[a-zA-Z0-9_\./]+)', // dog=false dog=./woof dog=.././woof
      '([a-zA-Z0-9\-_]+\=@[a-zA-Z0-9_]+)',   // dog=@woof
      '("[^"]*")',                           // "some\'thi ' ng"
      '(\'[^\']*\')',                        // 'some"thi " ng'
      '([^\s]+)',                            // <any group with no spaces>
    ];

    preg_match_all('#'.implode('|', $regex).'#is', $string, $matches);

    $stringArgs = $matches[0];
    $name = array_shift($stringArgs);

    $hashRegex = [
      '([a-zA-Z0-9\-_]+\="[^"]*")',          // cat="meow"
      '([a-zA-Z0-9\-_]+\=\'[^\']*\')',       // mouse='squeak squeak'
      '([a-zA-Z0-9\-_]+\=[a-zA-Z0-9_\./]+)', // dog=false dog=./woof dog=.././woof
      '([a-zA-Z0-9\-_]+\=@[a-zA-Z0-9_]+)',   // dog=@woof
    ];

    foreach ($stringArgs as $arg) {
      //if it's an attribute
      if (!(substr($arg, 0, 1) === "'" && substr($arg, -1) === "'")
        && !(substr($arg, 0, 1) === '"' && substr($arg, -1) === '"')
        && preg_match('#' . implode('|', $hashRegex) . '#is', $arg)
      ) {
        list($hashKey, $hashValue) = explode('=', $arg, 2);
        $hash[$hashKey] = $this->parseArgument($hashValue);
        continue;
      }

      $args[] = $this->parseArgument($arg);
    }

    return [$name, $args, $hash];
  }

  /**
   * If there's a quote, null, bool,
   * int, float... it's the literal value
   *
   * @param *string $arg One string argument value
   *
   * @return mixed
   */
  protected function parseArgument($arg)
  {
    //if it's a literal string value
    if (strpos($arg, '"') === 0
      || strpos($arg, "'") === 0
    ) {
      return "'" . str_replace("'", '\\\'', substr($arg, 1, -1)) . "'";
    }

    //if it's null
    if (strtolower($arg) === 'null'
      || strtolower($arg) === 'true'
      || strtolower($arg) === 'false'
      || is_numeric($arg)
    ) {
      return $arg;
    }

    $arg = str_replace(['[', ']', '(', ')'], '', $arg);
    $arg = str_replace("'", '\\\'', $arg);
    return sprintf(self::BLOCK_ARGUMENT_VALUE, $arg);
  }

  /**
   * Calls an alternative helper to add on to the compiled code
   *
   * @param *array $node
   *
   * @return string|false
   */
  protected function tokenize(array $node)
  {
    //lookout for pre processors helper
    $value = explode(' ', $node['value']);

    //is it a helper ?
    $helper = $this->resolveStatic(HandlebarsRuntime::class, 'getHelper', 'tokenize-' . $value[0]);

    if (!$helper) {
      return false;
    }

    list($name, $args, $hash) = $this->parseArguments($node['value']);

    //options
    $args[] = [
      'node'     => $node,
      'name'     => $name,
      'args'     => $node['value'],
      'hash'     => $hash,
      'offset'   => $this->offset,
      'handlebars' => $this->handlebars
    ];

    //NOTE: Tokenized do not have data binded to it
    return call_user_func_array($helper, $args);
  }

  /**
   * Makes code look nicely spaced
   *
   * @param *string $code
   * @param int   $before Used to set the token before spacing
   * @param int   $after Used to set the token after spacing
   *
   * @return string
   */
  protected function prettyPrint($code, $before = 0, $after = 0)
  {
    $this->offset += $before;

    // @codeCoverageIgnoreStart
    if ($this->offset < 0) {
      $this->offset = 0;
    }
    // @codeCoverageIgnoreEnd

    $code = str_replace(
      ['\r', '\n', '\t', '\1', '\2'],
      [
        "\n",
        '"\n"',
        str_repeat('  ', $this->offset),
        str_repeat('  ', 1),
        str_repeat('  ', 2)
      ],
      $code
    );

    $this->offset += $after;

    // @codeCoverageIgnoreStart
    if ($this->offset < 0) {
      $this->offset = 0;
    }
    // @codeCoverageIgnoreEnd

    $code = str_replace('\\' . $this->bars[0], $this->bars[0], $code);
    $code = str_replace('\\' . $this->bars[1], $this->bars[1], $code);

    //''."\n"
    $code = str_replace(' \'\'."\n"', ' "\n"', $code);

    // @codeCoverageIgnoreStart
    if ($code === '$buffer .= \'\';') {
      return '';
    }
    // @codeCoverageIgnoreEnd

    return $code;
  }

  /**
   * Finds a particular node in the open sections
   *
   * @param *array  $open The open nodes
   * @param string  $name The last name of the node we are looking for
   *
   * @return int The index where the section is found
   */
  protected function findSection(array $open, $name = self::LAST_OPEN)
  {
    foreach ($open as $i => $item) {
      $item = explode(' ', $item['value']);

      if ($item[0] === $name) {
        return $i;
      }
    }

    if ($name == self::LAST_OPEN) {
      return $i;
    }

    // @codeCoverageIgnoreStart
    return false;
    // @codeCoverageIgnoreEnd
  }

  /**
   * Quick trim script
   *
   * @param *string $string
   *
   * @return string
   */
  protected function trim($string)
  {
    $doubleBarsOpen = $this->bars[0] . $this->bars[0];
    $tripleBarsOpen = $doubleBarsOpen . $this->bars[0];
    $doubleBarsClose = $this->bars[1] . $this->bars[1];
    $tripleBarsClose = $doubleBarsClose . $this->bars[1];

    $doubleBarsRegexOpen = '#\s*' . preg_quote($doubleBarsOpen) . '\~\s*#is';
    $tripleBarsRegexOpen = '#\s*' . preg_quote($tripleBarsOpen) . '\~\s*#is';
    $doubleBarsRegexClose = '#\s*\~' . preg_quote($doubleBarsClose) . '\s*#is';
    $tripleBarsRegexClose = '#\s*\~' . preg_quote($tripleBarsClose) . '\s*#is';

    $string = preg_replace($tripleBarsRegexOpen, $tripleBarsOpen, $string);
    $string = preg_replace($tripleBarsRegexClose, $tripleBarsClose, $string);
    $string = preg_replace($doubleBarsRegexOpen, $doubleBarsOpen, $string);
    $string = preg_replace($doubleBarsRegexClose, $doubleBarsClose, $string);

    return $string;
  }
}
