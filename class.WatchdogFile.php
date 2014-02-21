<?php
/**
 * @file
 * Generic class to write log entries to a flat file.
 *
 * This class has no Drupal dependencies.
 *
 * History:
 * 2013-03-24, Kendall Anderson: Initial design/development
 * 2013-06-23, Kendall Anderson: Refactored into generic class
 * 2014-02-21, Kendall Anderson: Removed final Drupal dependency
 */

class WatchdogFile {

  // This holds our logging configuration.
  protected $config;

  // This is the raw log entry array we were given.
  protected $rawLogEntry;

  // This is the rendered log entry, ready to add to the log file.
  protected $renderedLogEntry;


  /**
   * Constructor.
   */
  public function __construct($config) {
    $defaults = array(
      'log_filename'  => '',
      'log_format'    => '',
      'date_format'   => '',
      'includes'      => array(),
      'excludes'      => array(),
      'levels'        => array(
        0 => 'emergency',
        1 => 'alert',
        2 => 'critical',
        3 => 'error',
        4 => 'warning',
        5 => 'notice',
        6 => 'info',
        7 => 'debug',
      ),
    );
    
    $this->config = (object) ($config + $defaults);
  }


  /**
   * Write a log entry to disk.
   *
   * @param assoc array $log_entry
   *
   * @return boolean
   *   TRUE if write log entry successfully
   *   FALSE if failed writing log entry
   *
   * @return integer
   *   -1 if entry was not written because it was excluded by rules
   */
  public function log($log_entry) {
    $this->rawLogEntry = $log_entry;
    $this->renderEntry();

    if ($this->shouldLog()) {
      return $this->writeEntry();
    }
    else {
      return -1;
    }
  }



  /**
   * Determine if this particular log entry should be logged or ignored.
   */
  private function shouldLog() {
    // Determine if we should exclude the current log entry because of its
    // severity.
    $items = $this->config->includes;
    if (!in_array($this->rawLogEntry['severity'], $items)) {
      // This log entry has a severity which we are not logging.
      return FALSE;
    }

    // Determine if we should exclude the current log entry because of keywords
    // found in its rendered version.
    $keywords = $this->config->excludes;
    for ($i = 0; $i < count($keywords); $i++) {
      $keyword = trim($keywords[$i]);
      if (empty($keyword)) {
        continue;
      }

      if (strpos($this->renderedLogEntry, $keyword) !== FALSE) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Write a watchdog entry to the log file.
   */
  private function writeEntry() {
    $success = FALSE;

    if ($this->isWritable()) {
      $filename = $this->config->log_filename;
      $data     = $this->renderedLogEntry;
      $success  = file_put_contents($filename, $data . "\n", FILE_APPEND);
    }

    return $success;
  }

  /**
   * Check if we're allowed to write to the log file.
   *
   * @return boolean
   *   Returns TRUE if the given file is writable.
   *   Returns FALSE if the given file is not writable.
   */
  private function isWritable() {
    if (!file_exists($this->config->log_filename)) {
      // The defined log file doesn't exist yet. Try to create it.
      if (!touch($this->config->log_filename)) {
        return FALSE;
      }
    }

    return is_writable($this->config->log_filename);
  }

  /**
   * Render a log entry for insertion in the log file.
   *
   * The rendering is controlled by how we've defined the formatting within
   * the watchdog file configuration settings.
   *
   * @param array $log_entry
   *   An associative array representing a single watchdog event. 
   *
   * @return string
   *   Returns the formatted log entry string.
   */
  private function renderEntry() {
    $vars = array(
      '%date'     => $this->getFieldDate(),
      '%severity' => $this->getFieldSeverity(),
      '%type'     => $this->rawLogEntry['type'],
      '%uid'      => $this->rawLogEntry['uid'],
      '%user'     => $this->getFieldUser(),
      '%message'  => $this->getFieldMessage(),
      '%uri'      => $this->rawLogEntry['request_uri'],
      '%referer'  => $this->rawLogEntry['referer'],
      '%link'     => strip_tags($this->rawLogEntry['link']),
    );

    $template = $this->config->log_format;
    $data     = strtr($template, $vars);

    // Replace some encoded entities for readability.
    // @todo this is hacky...
    $data = str_replace('&gt;', '>', $data);
    $data = str_replace('&lt;', '<', $data);

    $this->renderedLogEntry = $data;
  }

  /**
   * Construct value of the '%user' variable.
   */
  private function getFieldUser() {
    if (isset($this->rawLogEntry['uid']) && (int) $this->rawLogEntry['uid'] > 0) {
      $value = $this->rawLogEntry['user']->name;
    }
    else {
      $value = 'anon';
    }

    $value .= ' ' . $this->rawLogEntry['ip'];

    return $value;
  }

  /**
   * Construct value of the '%date' variable.
   */
  private function getFieldDate() {
    return date($this->config->date_format, $this->rawLogEntry['timestamp']);
  }

  /**
   * Construct value of the '%message' variable.
   */
  private function getFieldMessage() {
    $vars = $this->rawLogEntry['variables'];
    if (!is_array($vars)) {
      $vars = array();
    };
    
    return strip_tags(strtr($this->rawLogEntry['message'], $vars));
  }

  /**
   * Construct value of the '%severity' variable.
   */
  private function getFieldSeverity() {
    $levels = $this->config->levels;
    if (isset($levels[$this->rawLogEntry['severity']])) {
      $value = $levels[$this->rawLogEntry['severity']];
    }
    else {
      $value = $this->rawLogEntry['severity'];
    }

    return $value;
  }

}
