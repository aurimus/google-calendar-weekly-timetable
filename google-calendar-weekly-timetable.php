<?php
/*
Plugin Name: iCalendar Timetable BETA
Plugin URI: http://www.googletimetable.com
Description: Simple to setup, easy to manage weekly timetable / schedule for training, personal, school and other kind of weekly schedules. Anyone can manage right from google calendar or Outlook 365.
Version: 3.0.0
Author: aurimus
Author URI: http://www.googletimetable.com
Domain Path: /languages
License: GPL2

---

Copyright 2010 Aurimas Kubeldzis (email: 4urimas@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// ini_set('display_errors', TRUE); 

define('GCWT_PLUGIN_NAME', str_replace('.php', '', basename(__FILE__)));
define('GCWT_TEXT_DOMAIN', 'google-calendar-weekly-timetable');
define('GENERAL_SETTINGS', 'gcwt_general');

require_once 'vendor/autoload.php';

use ICal\ICal;

if(!class_exists('Google_Calendar_Weekly_Timetable')){
	class Google_Calendar_Weekly_Timetable{
		//PHP 4 constructor
		function Google_Calendar_Weekly_Timetable(){
			$this->__construct();
		}

		private $shortcode_name = 'timetable';

		//PHP 5 constructor
		function __construct(){
			add_action('send_headers', array($this, 'add_headers'));
			add_action('activate_google-calendar-weekly-timetable/google-calendar-weekly-timetable.php', array($this, 'activate_plugin'));
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
			add_action('init', array($this, 'init_plugin'));
			add_action('admin_menu', array($this, 'setup_admin'));
			add_action('admin_init', array($this, 'init_admin'));
			// SCRIPTS AND STYLES
			add_action('wp_enqueue_scripts', array($this, 'add_frontend_scripts'));
			add_action('admin_enqueue_scripts', array($this, 'add_admin_scripts'));
			add_action('wp_print_scripts', array($this, 'add_templates'));
			// AJAX admin
			add_action('wp_ajax_confirm_code', array($this, 'confirm_code'));
			add_action('wp_ajax_get_info', array($this, 'get_info'));
			add_action('wp_ajax_update_settings', array($this, 'update_settings'));
			add_action('wp_ajax_set_colors', array($this, 'set_colors'));
			add_action('wp_ajax_update_urls', array($this, 'update_urls'));
			// AJAX frontend
		    add_action('wp_ajax_nopriv_fetch_table_data', array($this, 'fetch_table_data'));
		    add_action('wp_ajax_fetch_table_data', array($this, 'fetch_table_data'));
		    // SHORTCODE
			add_shortcode($this->shortcode_name, array($this, 'shortcode_handler'));
		}

		function add_headers() {
			// header('Content-Type: text/html; charset=utf-8');
		}

		function activate_plugin(){
			$settings = get_option(GENERAL_SETTINGS);

			$defaults = array(
				'cycle' => true,
				'week_start' => get_site_option('start_of_week'),
				'type' => 'normal',
				'compact' => 'yes',
				'weekdays' => array([]),
				'time_format' => 'HH:mm',
				'style' => array('color' => '#444444', 'table-layout' => 'fixed', 'font-size' => '12px'),
				'caption_text' => 'Hover over event to show more info',
				'cache_duration' => 1,
				'language' => 'en',
				'weekday_name' => 'ddd'
			);

			switch ($defaults['week_start']) {
				case '1': $defaults['weekdays'] = [1, 2, 3, 4, 5, 6, 7]; break;
				case '0': $defaults['weekdays'] = [7, 1, 2, 3, 4, 5, 6]; break;
				case '6': $defaults['weekdays'] = [6, 7, 1, 2, 3, 4, 5]; break;
			}

		    $settings = shortcode_atts($defaults, $settings);
		    update_option(GENERAL_SETTINGS, $settings);

			add_option('gcwt-colors', array());
			add_option('gcwt-urls', array());
		}

		function init_plugin(){
			//Load text domain for i18n
			load_plugin_textdomain(GCWT_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages/');
			if(get_option('timezone_string') != '') date_default_timezone_set(get_option('timezone_string'));

			wp_register_script('momentjs', plugins_url('/node_modules/moment/min/moment-with-locales.js', __FILE__) );
			wp_register_script('vue', plugins_url('/node_modules/vue/dist/vue.js', __FILE__), ['momentjs'], '2.5.22' );
			wp_register_script('vue-color', plugins_url('/node_modules/vue-color/dist/vue-color.min.js', __FILE__), ['vue'], '2.7.0');

		}

		//Adds 'Settings' link to main WordPress Plugins page
		function add_settings_link($links){
			array_unshift($links, '<a href="options-general.php?page=google-calendar-weekly-timetable.php">' . __('Settings', GCWT_TEXT_DOMAIN) . '</a>');
			return $links;
		}

		//Setup admin settings page
		function setup_admin(){
			if(function_exists('add_options_page')) add_options_page('iCalendar Timetable', 'iCalendar Timetable', 'manage_options', basename(__FILE__), array($this, 'admin_page'));
		}

		//Prints admin settings page
		function admin_page(){
			require_once 'html/admin.php';
		}

		//Initialize admin stuff
		function init_admin(){
			register_setting('gcwt_general', 'gcwt_general', array($this, 'validate_general_options'));
		}

		
		//Validate submitted general options
		function validate_general_options($input){
			// exit(json_encode($input));
			// $settings = get_option(GENERAL_SETTINGS);
			// $settings['time_format'] = $input['time_format'];
			// $settings['style'] = $input['style'];
			// $settings['cycle'] = filter_var($input['cycle'], FILTER_VALIDATE_BOOLEAN);
			// $settings['caption_text'] = $input['caption_text'];
			// $settings['cache_duration'] = intval($input['cache_duration']);
			// $settings['language'] = $input['language'];
			// $settings['weekday_name'] = $input['weekday_name'];

			return $input;
		}

		//Adds the required admin page scripts and styles
		function add_admin_scripts($hook){
			if($hook != 'settings_page_' . GCWT_TEXT_DOMAIN) {
			    return;
			}

			$settings = get_option(GENERAL_SETTINGS);

			wp_enqueue_script('momentjs');
			wp_enqueue_script('vue');
			wp_enqueue_script('vue-color');
			wp_enqueue_style( 'settings_css', plugins_url('/css/gcwt-adminstyle.css', __FILE__) );
			wp_enqueue_script('settings_script', plugins_url('/js/gcwt-settings.js', __FILE__), ['vue', 'momentjs']);
			wp_add_inline_script('settings_script', 'window.locale = "' . $settings['language'] . '"');
			wp_add_inline_script('settings_script', 'window.cycle = "' . $settings['cycle'] . '"');
		}

		//Adds the required frontend scripts and styles
		function add_frontend_scripts(){
			global $post;
			$settings = get_option(GENERAL_SETTINGS);
			// Only enqueue scripts if we're displaying a post that contains the shortcode
			if( has_shortcode( $post->post_content, $this->shortcode_name ) ) {
				wp_enqueue_script('momentjs');
				wp_enqueue_script('vue');
				wp_enqueue_style('gcwt_frontend_style', plugins_url('/css/gcwt-style.css', __FILE__) );
				wp_enqueue_script('gcwt_frontend_script', plugins_url('/js/gcwt-frontend.js', __FILE__) );
				wp_add_inline_script('gcwt_frontend_script', 'window.ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"', 'before');
				wp_add_inline_script('gcwt_frontend_script', 'window.global_settings_data = ' . json_encode($settings), 'before');

				$current_user = wp_get_current_user();
				if (user_can( $current_user, 'administrator' )) {
					wp_enqueue_script('gcwt_frontend_colors', plugins_url('/js/gcwt-colors.js', __FILE__) );
				}
			}
		}

		//Adds the required frontend scripts and styles
		function add_templates(){
			?>
				<script type="text/x-template" id="gcwt_timetable_template">
					<?php include 'html/timetable.html'; ?>
				</script>
			<?php
		}


		//Handles the shortcode stuff
		function shortcode_handler($atts, $wrapped_content, $thetag){
			$settings = get_option(GENERAL_SETTINGS);
			$urls = get_option('gcwt-urls');

			//Check that any feeds have been added
			if(count($urls) > 0){

				extract(shortcode_atts(array(
					'time_format' => $settings['time_format'],
					'week_start' => $settings['week_start'],
					'type' => $settings['type'],
					'compact' => $settings['compact'],
					'timezone' => get_option('timezone_string'),
					'start' => null,
					// 'corner_text' => $settings['corner_text'],
					'caption_text' => $settings['caption_text'],
					'weekday_name' => $settings['weekday_name'],
					'weekdays' => $settings['weekdays']
				), $atts));
				
				//Did user request time format? if so then set it, otherwise use wordpress settings
				if ($time_format == null) $time_format = get_option('time_format');

				$atts = esc_attr( json_encode( [
			        'time_format' => $time_format,
			        'weekdays' => $weekdays,
					'type' => $type,
					'compact' => $compact,
			        'timezone' => $timezone,
			        'cycle' => $settings['cycle'],
			        'start' => $start,
			        'week_start' => $week_start,
			        'style' => $settings['style'],
			        'caption_text' => $caption_text,
			        'weekday_name' => $weekday_name,
			        'urls' => get_option('gcwt-urls')
			    ]));

				return "<timetable class='gcwt instance' :timetable-attrs='$atts'></timetable>";
				 
	
			} else {
				return __('From shortcode handler: No feed has been added yet. You can add a feed in the Google Calendar Weekly Timetable settings.', GCWT_TEXT_DOMAIN);
			}
		}


		// **** API **** //

		function fetch_table_data(){
			$settings = get_option(GENERAL_SETTINGS);
			$view_opt = json_decode(file_get_contents("php://input"), true);

			if ( false === ( $events_arr = get_transient($view_opt['start'] . $view_opt['url']) ) ) {

				try {
					$ical = new ICal();
					$ical->initUrl($view_opt['url']);
				} catch (Exception $e) {
				    exit( json_encode(array('error' => $e->getMessage())) );
				}

				$events = $ical->eventsFromRange(
					$view_opt['start'], 
					$view_opt['start'] . " + 1 week"
				);

				// Collect events info / fix timezone bug
				$events_arr = [];
				foreach ($events as $event) {
					$event = (array) $event;
					$temp_event = array();
					if (substr($event['dtstart'], -1) == 'Z') {
						$temp_event['dtstart'] = $event['dtstart_tz'];
						$temp_event['dtend'] = $event['dtend_tz'];
					} else {
						$temp_event['dtstart'] = $event['dtstart'];
						$temp_event['dtend'] = $event['dtend'];
					}

					$temp_event['summary'] = $event['summary'];
					$temp_event['description'] = $event['description'];
					$temp_event['location'] = $event['location'];

					$events_arr[] = $temp_event;
				}

				set_transient($view_opt['start'] . $view_opt['url'], $events_arr, $settings['cache_duration']);
			}

			exit(json_encode(array(
				'events' => $events_arr,
				'colors' => get_option('gcwt_colors')
			)));
		}



		function get_info(){
			$url = json_decode(file_get_contents("php://input"), true)['url'];

			try {
				$ical = new ICal(null, array('skipRecurrence' => true));
				$ical->initUrl($url);
				$info = $ical->cal['VCALENDAR'];
			} catch (Exception $e) {
			    exit( json_encode(array('error' => $e->getMessage())) );
			}

			$cal_urls = get_option('gcwt-urls');
			$cal_urls[] = array('name' => $info['X-WR-CALNAME'], 'url' => $url); // Need to check if already added here!
			update_option('gcwt-urls', $cal_urls);

			$db_colors = get_option('gcwt_colors');
			$names = array();
			foreach ($ical->cal['VEVENT'] as $event) {
				if ($names[$event['SUMMARY']]) {}
				else {
					if ($db_colors[$event['SUMMARY']]) {
						$color = $db_colors[$event['SUMMARY']];
					} else {
						$color = '#FFFFFF';
					}
					$names[$event['SUMMARY']] = array('bcolor' => array('hex' => $color));
				}
			}

			exit( json_encode(
				array(
					'title' => $info['X-WR-CALNAME'],
					'description' => $info['X-WR-CALDESC'],
					'events' => $names
				)
			));
		}


		function update_urls(){
			try {
				$new_urls = json_decode(file_get_contents("php://input"), true);
				$update = update_option('gcwt-urls', $new_urls);
			} catch (Exception $e) {
			    exit( json_encode(array('error' => $e->getMessage())) );
			}

			exit(json_encode(['status' => $update]));
		}

		function update_settings(){
			try {
				$settings = get_option(GENERAL_SETTINGS);
				$new_settings = json_decode(file_get_contents("php://input"), true);
				$all_settings = shortcode_atts($settings, $new_settings);
				$update = update_option(GENERAL_SETTINGS, $all_settings);
			} catch (Exception $e) {
			    exit( json_encode(array('error' => $e->getMessage())) );
			}

			// exit(json_encode(['settings' => $settings, 'new_settings' => get_option(GENERAL_SETTINGS)]));

			exit(json_encode(['status' => $update]));
		}


		function set_colors(){
			$db_colors = get_option('gcwt_colors');
			$user_colors = json_decode(file_get_contents("php://input"), true)['event_colors'];

			foreach ($user_colors as $event) {
				$db_colors[$event['name']] = $event['color'];
			}

			update_option('gcwt_colors', $db_colors);

			exit(json_encode(['status' => 'ok']));
		}
	}
}


$gce = new Google_Calendar_Weekly_Timetable();
?>