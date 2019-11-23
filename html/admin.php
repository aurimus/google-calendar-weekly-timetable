<?php 
    $settings = get_option(GENERAL_SETTINGS);
    $urls = get_option('gcwt-urls');
    $trial_start = get_option('gcwt-trial_start');

    $languages = ["en", "af", "ar-dz", "ar-kw", "ar-ly", "ar-ma", "ar-sa", "ar-tn", "ar", "az", "be", "bg", "bm", "bn", "bo", "br", "bs", "ca", "cs", "cv", "cy", "da", "de-at", "de-ch", "de", "dv", "el", "en-au", "en-ca", "en-gb", "en-ie", "en-il", "en-nz", "eo", "es-do", "es-us", "es", "et", "eu", "fa", "fi", "fo", "fr-ca", "fr-ch", "fr", "fy", "gd", "gl", "gom-latn", "gu", "he", "hi", "hr", "hu", "hy-am", "id", "is", "it", "ja", "jv", "ka", "kk", "km", "kn", "ko", "ky", "lb", "lo", "lt", "lv", "me", "mi", "mk", "ml", "mn", "mr", "ms-my", "ms", "mt", "my", "nb", "ne", "nl-be", "nl", "nn", "pa-in", "pl", "pt-br", "pt", "ro", "ru", "sd", "se", "si", "sk", "sl", "sq", "sr-cyrl", "sr", "ss", "sv", "sw", "ta", "te", "tet", "tg", "th", "tl-ph", "tlh", "tr", "tzl", "tzm-latn", "tzm", "ug-cn", "uk", "ur", "uz-latn", "uz", "vi", "x-pseudo", "yo", "zh-cn", "zh-hk", "zh-tw"];
    add_thickbox();
?>

<div v-cloak class="wrap gcwt-adminpage" id="vue_settings_app" data-settings-atts="<?= esc_attr(json_encode($settings)) ?>" data-urls="<?= esc_attr(json_encode($urls)) ?>" data-trial_start="<?= esc_attr(json_encode($trial_start)) ?>" data-langs="<?= esc_attr(json_encode($languages)) ?>">

	<h2><?php _e('iCalendar Timetable BETA', GCWT_TEXT_DOMAIN); ?></h2>
	<span v-if="!codeConfirmed">You can use this for 3 months (<?= intval(90 - (time() - $trial_start)/86400) ?> days left) then <a href="">support me on patreon</a> to continue using it. Plugin code from Patreon: <input type="text" name="code" @paste="confirmCode"></span>

	<h3><?php _e('General Options', GCWT_TEXT_DOMAIN); ?></h3>

	<script type="text/javascript">
		window.addEventListener('load', function() {
			var rows = document.querySelectorAll('.form-table tr')
			rows.forEach( (row) => {
				row.addEventListener('mouseenter', e => {
					row.querySelector('.tip').style.display = 'block'
				} )
				row.addEventListener('mouseleave', e => {
					row.querySelector('.tip').style.display = 'none'
				} )
			})
		})
	</script>
	
	<table class="form-table">
        <tr>
			<th scope="row"><?php _e('Google Calendar ICS', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<input placeholder="Paste your .ics URL from the web (e.g. Google Calendar) here" id="url" type="text" size="100" @paste="onUrlPaste"/>
				<div v-if="error">
					Error: <span class="error">{{ error }}</span>
				</div>
				<div v-for="(url, index) in urls">
					<span>{{ url.name }}</span>
					<a class="url-delete" @click.prevent="onUrlDelete(url)"><?php _e('Delete', GCWT_TEXT_DOMAIN); ?></a>
				</div>
				<div class="tip">You need to paste your ics calendar url (not necesarry Google). If you are using Google Calendar then after creating calendar click three dots next to it then Settings and sharing, go to bottom where it says 'Public address in ical format' and copy that link. You also need to make the calendar public by clicking Make available to public in Access permissions section</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Timetable type', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[type]" v-model="settings.type" @change="updateSettings" disabled>
					<option value="normal">times, weekdays, events</option>
					<option value="inverse">weekdays, times, events</option>
					<option value="eventsleft">events, weekdays, times</option>
					<option value="eventstop">weekdays, events, times</option>
				</select>
				<div class="tip">Type of your timetable (left, top, content) - normal, inverse, events on the left, events on the top. SUPPORT ME ON PATREON TO IMPLEMENT THIS</div>
			</td>
		</tr>
        <tr v-if="settings.type == 'normal' || settings.type == 'inverse'">
			<th scope="row"><?php _e('Compact', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[compact]" v-model="settings.compact" @change="updateSettings">
					<option value="yes">Yes</option>
					<option value="no">No</option>
				</select>
				<div class="tip">Should the timetable be compacted if possible. A compacted table will only display the times that has events, while not compacted table will include times in between. If visual accuraccy is important to you, select No</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Language', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[language]" v-model="settings.language" @change="updateSettings">
					<option v-for="lang in langs">{{lang}}</option>
				</select>
				<div class="tip">Language for your week day names and month names.</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Weekday names', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[weekday_name]" v-model="settings.weekday_name" @change="updateSettings">
					<option value="dd" >{{moment().locale(this.settings.language).format('dd')}}</option>
					<option value="ddd" >{{moment().locale(this.settings.language).format('ddd')}}</option>
					<option value="dddd" >{{moment().locale(this.settings.language).format('dddd')}}</option>
				</select>
				<div class="tip">The format for week day names. For smaller table choose shorthands.</div>
			</td>
		</tr>
        <tr class="weekdays">
			<th scope="row"><?php _e('Weekdays', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<weekdays inline-template :initial-weekdays="weekdays" :lang="settings.language" :wd_name="settings.weekday_name" @input="updateSettings" v-model="settings.weekdays">
					<div>
						<span class="separator" @drop.prevent="weekdays.unshift(dragWday); $emit('input', weekdays)" @dragenter.prevent @dragover.prevent> | </span>
						<span v-for="(wday, index) in weekdays" draggable="true" @dragstart="$event.dataTransfer.setData('text/plain', null); dragWday = weekdays.splice(index, 1).pop(); $emit('input', weekdays)">
							{{moment().isoWeekday(wday).locale(lang).format(wd_name)}}
						</span>
						<span class="separator" @drop.prevent="weekdays.push(dragWday); $emit('input', weekdays)" @dragenter.prevent @dragover.prevent> | </span>
						<a class="wd_reset" @click="weekdays = [1, 2, 3, 4, 5, 6, 7]; $emit('input', weekdays)">Reset</a>
				    </div>
				</weekdays>
				<div class="tip">Which days of the week to display and in what order. Drag and drop days to the start or end of days or off the side to remove</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Show date range', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[cycle]" @change="updateSettings" v-model="settings.cycle">
					<option value="true">Yes</option>
					<option value="false">No</option>
				</select>
				<div class="tip">Whether to show date above the table with next and preivous buttons that allows user to browse.</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Time format', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[time_format]" @change="updateSettings" v-model="settings.time_format">
					<option value="HH:mm"> 24 hours - HH:mm</option>
					<option value="H:mm"> 24 hours - H:mm</option>
					<option value="hh:mma"> 12 hours - hh:mma</option>
					<option value="hh:mm a"> 12 hours - hh:mm a</option>
					<option value="hh:mmA"> 12 hours - hh:mmA</option>
					<option value="hh:mm A"> 12 hours - hh:mm A</option>
					<option value="h:mma"> 12 hours - h:mma</option>
					<option value="h:mm a"> 12 hours - h:mm a</option>
					<option value="h:mmA"> 12 hours - h:mmA</option>
					<option value="h:mm A"> 12 hours - h:mm A</option>
				</select>
				<div class="tip">One 'h' means no leading zeroes. 'a' means lower case pm/am whereas 'A' is in capital.</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Text color', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<input type="color" v-model="settings.style.color" @change="updateSettings" name="gcwt_general[style][color]"></input>
				<div class="tip">Usually you would either want black or white or gray, depending on the colors of your events.</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Table layout', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[style][table-layout]" v-model="settings.style['table-layout']" @change="updateSettings">
					<option value="auto" >Flexible</option>
					<option value="fixed" >Fixed</option>
				</select>
				<div class="tip">Fixed layout would mean every column of the same width no matter the content. Flexible will stretch column widths depending on event titles.</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Font size', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[style][font-size]" v-model="settings.style['font-size']" @change="updateSettings">
					<option value="10px" >Extra small</option>
					<option value="12px" >Very small</option>
					<option value="14px" >Small</option>
					<option value="16px" >Medium</option>
					<option value="18px" >Large</option>
					<option value="20px" >Very large</option>
					<option value="22px" >Extra large</option>
				</select>
				<div class="tip">Smaller font size also allows your table to be smaller overall.</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Caption text', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<input type="text" size="50" name="gcwt_general[caption_text]" v-model="settings.caption_text" @change="updateSettings"></input>
				<div class="tip">Table caption that will be displayed unless the user is hovering one of your events, in that case the event info will be displayed</div>
			</td>
		</tr>
        <tr>
			<th scope="row"><?php _e('Cache duration', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<select name="gcwt_general[cache_duration]" v-model="settings.cache_duration" @change="updateSettings">
					<option value="1" >Developer</option>
					<option value="60" >1 min</option>
					<option value="300" >5 min</option>
					<option value="900" >15 min</option>
					<option value="1800" >30 min</option>
					<option value="3600" >1 hour</option>
					<option value="18000" >5 hours</option>
					<option value="86400" >1 day</option>
				</select>
				<div class="tip">How long to keep downloaded data in server before renewing. With cache users experience shorter load times while browsing the timetable. Choose Developer temporarily if you are just testing - this means the events will reflect your calendar imediatly.</div>
			</td>
		</tr>
	</table>
	
</div>