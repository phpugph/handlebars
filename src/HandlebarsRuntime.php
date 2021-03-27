<?php //-->
/**
 * This file is part of the Handlebars PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Handlebars;

use Closure;

/**
 * Welcome to Handlebars!
 *
 * This definition wraps the Engine to match
 * the handlebars API as close as possible
 *
 * @vendor   PHPUGPH
 * @package  Handlebars
 * @standard PSR-2
 */
class HandlebarsRuntime
{
  /**
   * @var array $partials A raw list of partials
   */
  protected static $partials = [];

  /**
   * @var array $helpers A raw list of helpers
   */
  protected static $helpers = [];

  /**
   * Resets the helpers and partials
   */
  public static function flush()
  {
     self::$partials = [];
     self::$helpers = [];
  }

  /**
   * Returns a specific helper
   *
   * @param *string       $name The name of the helper
   * @param HandlebarsData|null $data If provided we will bind the callback with Data
   *
   * @return Closure|null
   */
  public static function getHelper($name, HandlebarsData $data = null)
  {
    if (isset(self::$helpers[$name])) {
      if (!is_null($data) && self::$helpers[$name] instanceof Closure) {
        return self::$helpers[$name]->bindTo($data, get_class($data));
      }

      return self::$helpers[$name];
    }

    return null;
  }

  /**
   * Returns all the registered helpers
   *
   * @param HandlebarsData|null $bind If provided we will bind the callbacks with Data
   *
   * @return array
   */
  public static function getHelpers(HandlebarsData $data = null)
  {
    if (is_null($data)) {
      return self::$helpers;
    }

    $helpers = [];

    foreach (self::$helpers as $name => $helper) {
      if ($helper instanceof Closure) {
        $helpers[$name] = $helper->bindTo($data, get_class($data));
      }
    }

    return $helpers;
  }

  /**
   * Returns a specific partial
   *
   * @param *string $name The name of the helper
   *
   * @return string|null
   */
  public static function getPartial($name)
  {
    if (isset(self::$partials[$name])) {
      return self::$partials[$name];
    }

    return null;
  }

  /**
   * Returns all the registered partials
   *
   * @return array
   */
  public static function getPartials()
  {
    return self::$partials;
  }

  /**
   * The famous register helper matching the Handlebars API
   *
   * @param *string   $name   The name of the helper
   * @param *function $helper The helper handler
   */
  public static function registerHelper($name, $helper)
  {
    self::$helpers[$name] = $helper;
  }

  /**
   * Delays registering partials to the engine
   * because there is no add partial method...
   *
   * @param *string $name  The name of the helper
   * @param *string $partial The helper handler
   */
  public static function registerPartial($name, $partial)
  {
    self::$partials[$name] = $partial;
  }

  /**
   * The opposite of registerHelper
   *
   * @param *string $name the helper name
   */
  public static function unregisterHelper($name)
  {
    if (isset(self::$helpers[$name])) {
      unset(self::$helpers[$name]);
    }
  }

  /**
   * The opposite of registerPartial
   *
   * @param *string $name the partial name
   */
  public static function unregisterPartial($name)
  {
    if (isset(self::$partials[$name])) {
      unset(self::$partials[$name]);
    }
  }
}
