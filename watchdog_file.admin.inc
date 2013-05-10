<?php
/**
 * @file
 * Admin settings for the watchdog_file module.
 */

/**
 * Build the admin/settings form.
 */
function watchdog_file_admin_settings() {
  $form = array();

  # @todo provide a clearer intro message that makes it clear that
  # 1. this file must be web-writable
  # 2. this file must NOT be accessible *from* the website (security!)
  $form[WATCHDOG_FILE_VAR_FILENAME] = array(
    '#title'          => t("Full path/filename for the watchdog log file:"),
    '#type'           => 'textfield',
    '#description'    => t("Provide an explicit path to a file that is writable by the webserver. This file should NOT be within the public_html folder! It should not be accessible from the website!<br><br>e.g., /var/log/apache/MYSITE-watchdog.log"),
    '#default_value'  => variable_get(WATCHDOG_FILE_VAR_FILENAME, ''),
  );


  $form[WATCHDOG_FILE_VAR_DATE_FORMAT] = array(
    '#title'          => t("Date/time format for log entries:"),
    '#type'           => 'textfield',
    '#description'    => t("Enter the formatting string for PHP's date() function. This will be used for the timestamp on all log entries."),
    '#default_value'  => variable_get(WATCHDOG_FILE_VAR_DATE_FORMAT, WATCHDOG_FILE_DEFAULT_DATE_FORMAT),
  );


  # @todo need a better way of configuring this...
  $form[WATCHDOG_FILE_VAR_LOG_FORMAT] = array(
    '#title'          => t("Format an individual log entries:"),
    '#type'           => 'textfield',
    '#description'    => t("@todo define available variables with examples"),
    '#default_value'  => variable_get(WATCHDOG_FILE_VAR_LOG_FORMAT, WATCHDOG_FILE_DEFAULT_LOG_FORMAT),
  );





  // Validate the form: ensure the path/filename is writable.
  $form['#validate'][] = 'watchdog_file_admin_settings_validate';

  return system_settings_form($form);
}


/**
 * Validate the admin/settings form.
 */
function watchdog_file_admin_settings_validate(&$form, &$form_state) {
  $values = $form_state['values'];

  // Ensure the watchdog path/filename given is writable by the webserver.
  if (!watchdog_file_is_writable($values[WATCHDOG_FILE_VAR_FILENAME])) {
    form_set_error(WATCHDOG_FILE_VAR_FILENAME, t("The watchdog log filename cannot be written to. You'll need to change file permissions so the webserver is allowed to write to this file."));
  }
}

