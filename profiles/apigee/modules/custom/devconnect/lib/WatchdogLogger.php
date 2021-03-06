<?php
/**
 * @file
 * Adapter class to write Psr\Log-formatted messages to Watchdog.
 */

namespace Drupal\devconnect;

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;

/**
 * A logger class that implements Psr\Log\LoggerInterface. This class writes
 * log data to Drupal's watchdog table.
 */
class WatchdogLogger extends AbstractLogger {

  /**
   * The severity level below which log entries are not written to the log.
   * A level of -1 (the default level) indicates that everything is written.
   *
   * @var int
   */
  private static $logThreshold = -1;

  /**
   * If set, calls to self::log() will use this severity level rather than the
   * one suggested by the caller.
   *
   * @var null|int
   */
  private static $forcedLogLevel = NULL;

  /**
   * Sets the logging threshold, below which log entries will be dropped.
   *
   * @param int $threshold
   *   One of the WATCHDOG_* constants defined in bootstrap.inc, or -1 to
   *   write everything to the log.
   */
  public static function setLogThreshold($threshold) {
    self::$logThreshold = intval($threshold);
  }

  /**
   * Sets the log level to be unconditionally used for log messages.
   *
   * @param int|null $level
   *   The severity level to be written to all subsequent log entries, or NULL
   *   to use the severity level suggested by the caller. If non-null, this
   *   should be one of the WATCHDOG_* constants defined in bootstrap.inc.
   */
  public static function setForcedLogLevel($level) {
    if ($level === NULL) {
      unset(self::$forcedLogLevel);
    }
    else {
      self::$forcedLogLevel = intval($level);
    }
  }

  /**
   * Logs an event to watchdog.
   *
   * This method is called automatically but you can call it
   * from your own code as well.
   *
   * @param int $level
   *   The log level for this message, expressed as a Psr\LogLevel constant.
   * @param mixed $message
   *   The string or object to be logged. If the object is an Exception, we
   *   log its string representation if it has a __toString() method;
   *   otherwise we invoke watchdog_exception().
   * @param array $context
   *   Optional associative array of context info. If the 'type' member is
   *   populated, we use that as the $type parameter for watchdog(); otherwise
   *   we use a backtrace to find the filename containing the code that called
   *   self::log().
   */
  public function log($level, $message, array $context = array()) {

    // If we were directed to force a log level, ignore the incoming level.
    if (isset(self::$forcedLogLevel)) {
      $severity = self::$forcedLogLevel;
    }
    else {
      // Translate Psr\LogLevel class constants to WATCHDOG_* constants.
      $severity = self::log2drupal($level);
    }

    // Short-circuit if this event is too insignificant to log.
    // Note that Drupal's severity is in decreasing order, i.e.
    // 0 = emergency and 7 = debug.
    if ($severity > self::$logThreshold) {
      return;
    }

    // Determine how we're going to handle this.
    $use_watchdog_exception = FALSE;
    if ($message instanceof \Exception && !method_exists($message, '__toString')) {
      // Use watchdog_exception ONLY for exceptions that don't have an explicit
      // __toString() method.
      $use_watchdog_exception = TRUE;
    }
    elseif (is_array($message) || is_object($message)) {
      // Massage non-strings into loggable strings.
      if (is_object($message) && method_exists($message, '__toString')) {
        $message = (string) $message;
      }
      else {
        ob_start();
        var_dump($message);
        $message = ob_get_clean();
      }
    }

    if (count($context) > 0) {
      $message = $this->interpolate($message, $context);
    }

    // Find the "type" (source) of the log request. Generally this is a class
    // or file name. It may be specified in $context, or it can be derived from
    // a debug_backtrace.
    if (isset($context['type'])) {
      $type = $context['type'];
    }
    else {
      if (version_compare(PHP_VERSION, '5.4', '>=')) {
        // Be more efficient when running PHP 5.4
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
      }
      elseif (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
        // Sigh. Pull entire backtrace.
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
      }
      else {
        // Double-sigh. We are dealing with PHP < 5.3.6 dinosaurs.
        $backtrace = debug_backtrace();
      }
      $type = basename($backtrace[1]['file']);
      $type = preg_replace('!\.(module|php)$!', '', $type);
    }

    $message = filter_xss($message);
    $message = preg_replace("!Authorization: Basic [A-Za-z0-9+\\=]+!", 'Authorization: Basic [**masked**]', $message);

    $message = '<pre>' . $message . '</pre>';

    if ($use_watchdog_exception) {
      watchdog_exception($type, $message, NULL, array(), $severity);
    }
    else {
      watchdog($type, $message, array(), $severity);
    }
  }

  /**
   * Translates Psr\LogLevel constants into WATCHDOG_* constants.
   *
   * @param string $level
   *   The Psr\LogLevel constant to change into a WATCHDOG_* constant.
   *
   * @return int
   *   The WATCHDOG_* constant corresponding to the specified Psr\LogLevel.
   */
  private static function log2drupal($level) {
    switch ($level) {
      case LogLevel::ALERT:
        $level = WATCHDOG_ALERT;
        break;

      case LogLevel::CRITICAL:
        $level = WATCHDOG_CRITICAL;
        break;

      case LogLevel::DEBUG:
        $level = WATCHDOG_DEBUG;
        break;

      case LogLevel::ERROR:
        $level = WATCHDOG_ERROR;
        break;

      case LogLevel::EMERGENCY:
        $level = WATCHDOG_EMERGENCY;
        break;

      case LogLevel::INFO:
        $level = WATCHDOG_INFO;
        break;

      case LogLevel::NOTICE:
        $level = WATCHDOG_NOTICE;
        break;

      case LogLevel::WARNING:
        $level = WATCHDOG_WARNING;
        break;

      default:
        $level = WATCHDOG_NOTICE;
    }
    return $level;
  }

  /**
   * Interpolates context values into the message placeholders.
   *
   * @param string $message
   *   The message format string to use as a template.
   * @param array $context
   *   Key-value pairs to be interpolated into the template.
   *
   * @return string
   *   The log entry with all template placeholders filled.
   */
  private function interpolate($message, array $context = array()) {
    // Build a replacement array with braces around the context keys.
    $replace = array();
    foreach ($context as $key => $val) {
      if (!is_scalar($val)) {
        if (is_object($val) && method_exists($val, '__toString')) {
          $val = $val->__toString();
        }
        else {
          $val = print_r($val, TRUE);
        }
      }
      $replace['{' . $key . '}'] = $val;
    }

    // Interpolate replacement values into the message and return.
    return strtr($message, $replace);
  }
}
