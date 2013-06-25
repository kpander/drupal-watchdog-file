README.txt
==========
WATCHDOG FILE is a module that records all watchdog() events to a log file on
your server rather than using the database. The goals are to reduce database
load in general, and to provide an easier way of keeping track of your watchdog
activity.

This module is intended to replace the Watchdog module.


WHY USE THIS MODULE?
====================

HOW IT WORKS
============
- Drupal-independent class: WatchdogFile()
- meaning, this can be reused elsewhere

INSTALLATION
============

CONFIGURATION
=============
After installing the module, go to /admin/config/system/watchdog-file to change
the module settings.


AUTHOR/MAINTAINER
=================
Kendall Anderson <dailyphotography at gmail DOT com>
http://invisiblethreads.com


CHANGELOG
=========
v1.0, 2013-03-22
- initial development


TODO
====
