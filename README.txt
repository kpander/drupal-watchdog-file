README.txt
==========
WATCHDOG FILE is a module that records all watchdog() events to a log file on
your server rather than using the database. The goals are to reduce database
load in general, and to provide an easier way of keeping track of your watchdog
activity.

This module is intended to replace the Watchdog module.

It looks like the Raw Log module (http://drupal/project/rawlog) is similar to
this module, but it is only available for Drupal 6.x.



WHY USE THIS MODULE?
====================
You want to keep Watchdog logs, but you don't want errors hammering your
database server, nor do you want the {watchdog} table to grow to a huge size.

You're comfortable (and perhaps prefer) reviewing log messages in a text file,
likely with grep.



HOW IT WORKS
============
This module reacts to hook_watchdog() events. If the event passes the criteria 
for logging, we append the event details to a log file on disk.

Watchdog events are selected for logging based on the event severity (which can
be configured).

An event can be excluded from logging if the rendered log entry includes any
of a set of defined keywords or strings.

This allows fairly precise control over what will and will not be logged.



INSTALLATION
============
Install like any Drupal module. If you want the configuration UI, be sure to
also install the watchdog_file_admin module.



CONFIGURATION
=============
If you installed the configuration UI (the watchdog_file_admin module), go to
/admin/config/system/watchdog-file to change the module settings.

Otherwise, ensure the configuration settings are defined within your site's
settings.php file. For example:

$conf['watchdog_file_filename'] = '/my-path-for-log-files/my-site-%Y-%m.log';


Note: Ensure the path where the log files are saved has permissions for the 
website to read and write files!



AUTHOR/MAINTAINER
=================
Kendall Anderson <dailyphotography at gmail DOT com>
http://invisiblethreads.com


CHANGELOG
=========
v1.1, 2014-02-21
- removed Drupal dependency from class.WatchdogFile.php
- documentation revisions

v1.0, 2013-03-22
- initial development
