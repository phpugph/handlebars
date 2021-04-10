<?php //-->
/**
 * This file is part of the Handlebars PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Handlebars;

/**
 * Tokenizes Handlebars
 *
 * @vendor   PHPUGPH
 * @package  Handlebars
 * @standard PSR-2
 */
class HandlebarsTokenizer
{
  /**
   * @const string TYPE_TEXT
   */
  const TYPE_TEXT = 'text';

  /**
   * @const string TYPE_VARIABLE_ESCAPE
   */
  const TYPE_VARIABLE_ESCAPE = 'escape';

  /**
   * @const string TYPE_VARIABLE_UNESCAPE
   */
  const TYPE_VARIABLE_UNESCAPE = 'variable';

  /**
   * @const string TYPE_SECTION_OPEN
   */
  const TYPE_SECTION_OPEN = 'section';

  /**
   * @const string TYPE_SECTION_CLOSE
   */
  const TYPE_SECTION_CLOSE = 'close';

  /**
   * @var string $source
   */
  protected $source = null;

  /**
   * @var string $buffer
   */
  protected $buffer = '';

  /**
   * @var string $bars
   */
  protected $bars = '{}';

  /**
   * @var string $type
   */
  protected $type = self::TYPE_TEXT;

  /**
   * @var string $level
   */
  protected $level = 0;

  /**
   * Just load the source template
   *
   * @param *string $source
   */
  public function __construct($source)
  {
    $this->source = $source;
  }

  /**
   * Sets the handlebars characters
   *
   * @param string $bars
   *
   * @return HandlebarsTokenizer
   */
  public function setBars($bars)
  {
    if (is_string($bars) && strlen($bars) > 1) {
      $this->bars = $bars;
    }

    return $this;
  }

  /**
   * Main rendering function that will
   * callback tokens instead of storing them in memory
   *
   * @param callable|null $callback
   *
   * @return HandlebarsTokenizer
   */
  public function tokenize($callback = null)
  {
    if (!is_callable($callback)) {
      $callback = function () {
      };
    }

    $length = strlen($this->source);

    for ($line = 1, $i = 0; $i < $length; $i++) {
      if ($this->source[$i] == "\n") {
        $line++;
      }

      $doubleBars = $this->bars[0] . $this->bars[0];
      $tripleBars = $doubleBars . $this->bars[0];

      switch (true) {
        //section
        // @codeCoverageIgnoreStart
        //tested that it does go here, but it's not reported as covered
        case substr($this->source, $i, 3) == $tripleBars . '#':
          $i = $this->addNode($i, self::TYPE_SECTION_OPEN, $line, 4, 6, $callback);
          break;
        // @codeCoverageIgnoreEnd
        case substr($this->source, $i, 3) == $doubleBars . '#':
          $i = $this->addNode($i, self::TYPE_SECTION_OPEN, $line, 3, 5, $callback);
          break;
        // @codeCoverageIgnoreStart
        //tested that it does go here, but it's not reported as covered
        case substr($this->source, $i, 3) == $tripleBars . '/':
          $i = $this->addNode($i, self::TYPE_SECTION_CLOSE, $line, 4, 6, $callback);
          break;
        // @codeCoverageIgnoreEnd
        case substr($this->source, $i, 3) == $doubleBars . '/':
          $i = $this->addNode($i, self::TYPE_SECTION_CLOSE, $line, 3, 5, $callback);
          break;

        //variable
        case substr($this->source, $i, 3) == $tripleBars:
          $i = $this->addNode($i, self::TYPE_VARIABLE_ESCAPE, $line, 3, 6, $callback);
          break;
        case substr($this->source, $i, 2) == $doubleBars:
          $i = $this->addNode($i, self::TYPE_VARIABLE_UNESCAPE, $line, 2, 4, $callback);
          break;

        //text
        default:
          $this->buffer .= $this->source[$i];
          break;
      }
    }

    $this->flushText($i, $callback);
    return $this;
  }

  /**
   * Forms the node and passes to the callback
   *
   * @param *int    $start
   * @param *string   $type
   * @param *int    $line
   * @param *int    $offset1
   * @param *int    $offset2
   * @param *callable $callback
   *
   * @return HandlebarsTokenizer
   */
  protected function addNode($start, $type, $line, $offset1, $offset2, $callback)
  {
    $this->flushText($start, $callback);

    switch ($type) {
      case self::TYPE_VARIABLE_ESCAPE:
        $end = $this->findVariable($start, true);
        break;
      case self::TYPE_VARIABLE_UNESCAPE:
        $end = $this->findVariable($start, false);
        break;
      case self::TYPE_SECTION_OPEN:
        $end = $this->findVariable($start, false);
        break;
      case self::TYPE_SECTION_CLOSE:
      default:
        $end = $this->findVariable($start, false);
        $this->level --;
        break;
    }

    call_user_func($callback, [
      'type'  => $type,
      'line'  => $line,
      'start' => $start,
      'end'   => $end,
      'level' => $this->level,
      'value' => substr($this->source, $start + $offset1, $end - $start - $offset2)
    ], $this->source);

    if ($type === self::TYPE_SECTION_OPEN) {
      $this->level ++;
    }

    return $end - 1;
  }

  /**
   * Takes whatever is in the buffer
   * forms a node and passes it to
   * the callback
   *
   * @param *int    $i
   * @param *callable $callback
   *
   * @return HandlebarsTokenizer
   */
  protected function flushText($i, $callback)
  {
    // @codeCoverageIgnoreStart
    if ($this->type !== self::TYPE_TEXT || !strlen($this->buffer)) {
      return $this;
    }
    // @codeCoverageIgnoreEnd

    call_user_func($callback, [
      'type'  => $this->type,
      'start' => $i - strlen($this->buffer),
      'end'   => $i - 1,
      'level' => $this->level,
      'value' => $this->buffer
    ], $this->source);

    //flush
    $this->buffer = '';

    return $this;
  }

  /**
   * Since we know where the start is,
   * we need to find the end in the source
   *
   * @param *int  $i
   * @param *bool $escape
   *
   * @return int
   */
  protected function findVariable($i, $escape)
  {
    $close = $this->bars[1] . $this->bars[1];

    if ($escape) {
      $close = $close . $this->bars[1];
    }

    for (; substr($this->source, $i, strlen($close)) !== $close; $i++) {
    }

    return $i + strlen($close);
  }
}
