=== iCalendar timetable BETA ===
Contributors: Aurimas Kubeldzis
Donate link: www.googletimetable.com
Tags:  google calendar, timetable, schedule, weekly, google, calendar, event, events, 
Requires at least: 2.9.2
Tested up to: 5.2.4
Stable tag: 3.0.0

Simple, lightweight weekly timetable / schedule for training, personal, school and other kind of weekly schedules. Anyone can easily manage right from google calendar or Outlook 365.

== Description ==

Google Calendar week displayed automatically as weekly timetable / schedule in a post or page. If you want to embed Google Calendar week then you will like this plugin because it is much more neat and vertically concise in comparison.

= Features =

* Very easy to add Google Calendar, a simple url copy paste.
* Easy to adapt style to your website.
* You can set colors for each event / schedule entry
* Conserves vertical space compared to usual Google Calendar embedded weekly view.
* All language choices that include time format
* You can choose Sunday / Saturday as start of week
* Website visitor can browse weeks with next / previous buttons
* Some style choices for table right in the settings, otherwise easy to style with CSS
* Description of hovered event in caption
* Custom caption without a link

You can also use Outlook as a source for your events!

[youtube https://www.youtube.com/watch?v=Tm8k51vp_iY]

Support: by [email](4urimas@gmail.com)

Support:

* [Plugin Homepage Comments](www.googletimetable/)
* [E-mail](4urimas@gmail.com)
* [Forum](https://wordpress.org/support/plugin/google-calendar-weekly-timetable)

== Installation ==

Please send me any suggestions (bugs/features). Use comments on wordpress plugin, homepage or my email.

Only 3 steps:

1. Create Google Calendar (or use existing one). Put some events and make them repeat forever. Go to Calendar Settings -> My calendars -> Click three dots beside your calendar -> Settings and Sharing -> Access permissions -> Click on "Make available to Public". Then go to last section where it says Integrate calendar and copy the link "Public address in iCal format"
2. Go back to your wordpress admin panel. Go to Settings -> Google Calendar Weekly Timetable and PASTE the link from previous step to `Google Calendar ICS` field. If everything ok - your calendar name and description should appear in green. Click Save.
3. Now go to the post/page that you want to insert the table to. Add shortcode `[timetable]` anywhere you like. Use `.gcwt` table class to modify the styling.

Whenever you have plans for changes - change it for that date or dates in your Google Calendar. In other words the timetable will always display events for upcomming week and correctly reflect your calendar.

== Screenshots ==

1. Google Calendar Weekly Timetable

== Changelog ==

= 1.0.7 =
* Added cache to not speed up loading
* Added color in CSS
* Bug fixes (1.0.0 -> 1.0.7)
* Added ad for v2

= 1.0.0 =
* Now works with .ics calendar
* Complete rewrite of the plugin
* Features: many have been removed as it complicated the plugin too much lol

= 0.3.2 =
* Fix: Any language support - week day names defaults to WP language (set by define ('WPLANG', '');)
* Fix: "Start time" string removed from upper-left cell
* Bug fix: some problems with events not being displayed if there are too many of them

= 0.3.1 =
* Fix: Now it cache_duration works together with refresh_one shortcode options.
* Bug fix: The language code wasn't working correctly for title parsing in different languages. Also added documentation
* Changes to readme.txt.

= 0.3 =
* New feature: Event titles in multiple languages
* New feature: Now can specify time format instead of just switch between 12 and 24 hour clocks. However, it is picked up from wordpress by default so use this only if it doesn't work.
* New feature: User can now set table caption.
* New feature: Can specify that one feed would be parsed at every refresh of table. Use refresh_one=1 in shortcode.
* Bug fix: Fix for php4, where the plugin won't install
* Change: automatically pick 7 days according to the day the week starts on (before it would start today so on the week days that are already passed this week table would show next weeks schedule, while showing this weeks shedule in future days. You can get that back by adding start="today")

= 0.2.1 =
* New feature: Timezone, time format and week starts on settings are set automatically from wordpress by default 
* New feature: Shortcode option to set cache duration for a table (overrides individual settings) 
* New feature: Comments and manual on how to edit CSS
* New feature: If no arguments are passed with id=”" then all the feeds (if any) are automatically included 
* New feature: New shortcode [timetable]

= 0.2 =
* New feature: Start day of week can be chosen (Monday or Sunday). Use start_sunday=1 in shortcode and make sure to specify Sunday as a start date with start="previous Sunday"
* New feature/fix: Events can now occupy more blocks down if the times match and it doesn't go over other event. Use rowspan=0 shortcode option to disable.
* New feature: Can now specify exactly what 7 days to pick according to strtotime() php function. Using start="previous Monday" (ex.) in shortcode will render current week from Monday to Sunday
* Added "start time" to table left top cell. Indicating that you have start times on the left column.
* Bug fix: Adaptation to some recent google changes concerning https and http

= 0.1.2 =
* Bug fix: php warnings in case of no events in the feed
* Bug fix: Incompatable with Google Calendar Events and other plugins that use SimplePie(GCalendar). This is major code change, most function names changed, also database names.
* Bug fix: Background color and some other CSS options don't show up
* Bug fix: Table goes over widgets (oversized)
* Bug fix: Gets more events than it's supposed to (futher than a week)

= 0.1.1 =
* Changes to readme.txt.

= 0.1 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
This is complete rewrite of the plugin, if you are using old version and it's working for you - don't upgrade yet because I removed some features that you might be using

== Frequently Asked Questions ==

Please visit the [plugin homepage](www.googletimetable.com) and leave a comment for help, or [contact me](4urimas@gmail.com) directly.