# Google Calendar Weekly Timetable

This is a development version of iCalendar timetable plugin for Wordpress

## How to run it

Here's two ways to do it (first delete current plugin and the last step is to activate the plugin in your WP)

### Using SSH

1. Log in to the server and navigate to your WordPress plugins folder
2. Type `git clone https://github.com/aurimus/google-calendar-weekly-timetable.git`
3. `cd google-calendar-weekly-timetable`
3. Follow [these instructions](https://www.ionos.com/community/hosting/php/install-and-use-php-composer-on-ubuntu-1604/) (or equivalent) to install composer (if you don't have it) and run `composer install`

### Using FTP

1. Click _Clone or download_ and click on _Download ZIP_
2. Open FTP and upload the folder inside the .zip to your plugins directory.
3. Download [this folder](https://www.googletimetable.com/dependencies.zip), unzip and upload to inside of the plugin

### Using WordPress only

1. Click _Clone or download_ and click on _Download ZIP_ and unzip it!
2. Open inside of the plugin folder (where all the different files are) and put the inside of [this folder](https://www.googletimetable.com/dependencies.zip) inside of it.
3. Now zip it all back to a .zip
4. Upload to WP as a new plugin.

## How to contribute

Simply open an issue with feature request or a bug
