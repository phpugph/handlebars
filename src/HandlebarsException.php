<?php //-->
/**
 * This file is part of the Handlebars PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Handlebars;

use Exception;

/**
 * Handlebars exceptions
 *
 * @vendor   PHPUGPH
 * @package  Handlebars
 * @standard PSR-2
 */
class HandlebarsException extends Exception
{
  /**
   * @const string ERROR_AND
   */
  const ERROR_AND = ' AND ';

  /**
   * @const string ERROR_LINE
   */
  const ERROR_LINE = '"%s" on line %s';

  /**
   * @const string ERROR_MISSING_CLOSING
   */
  const ERROR_MISSING_CLOSING = 'Missing closing tags for: %s';

  /**
   * @const string ERROR_UNKNOWN_END
   */
  const ERROR_UNKNOWN_END = 'Unknown close tag: "%s" on line %s';

  /**
   * @const string FILE_PREFIX
   */
  const COMPILE_ERROR = "%s on line %s \n```\n%s\n```\n";

  /**
   * Triggered when we are missing closing tags
   *
   * @param *array $open A list of open nodes
   *
   * @return HandlebarsException
   */
  public static function forMissingClosing(array $open)
  {
    foreach ($open as $i => $item) {
      $open[$i] = sprintf(self::ERROR_LINE, $item['value'], $item['line']);
    }

    $message = implode(self::ERROR_AND, $open);
    $message = sprintf(self::ERROR_MISSING_CLOSING, $message);

    return new static($message);
  }

  /**
   * Triggered when we are missing closing tags
   *
   * @param *string $tag  Open tag
   * @param *string $line Line it was openned
   *
   * @return HandlebarsException
   */
  public static function forUnknownEnd($tag, $line)
  {
    return new static(sprintf(self::ERROR_UNKNOWN_END, $tag, $line));
  }

  /**
   * Triggered when there is a compiler error
   *
   * @param *array  $error Error trace
   * @param *string $code
   * @param int   $limit The amount of lines to show
   *
   * @return HandlebarsException
   */
  public static function forCompileError(array $error, $code, $limit = 25)
  {
    $code = explode("\n", $code);
    $start = $error['line'] - $limit;
    if ($start < 0) {
      $start = 0;
    }

    $code = array_splice($code, $start, $limit * 2);

    foreach ($code as $i => $line) {
      $code[$i] = (++$start) . ': ' . $line;
    }

    $message = sprintf(
      self::COMPILE_ERROR,
      $error['message'],
      $error['line'],
      implode("\n", $code)
    );

    return new static($message);
  }
}
