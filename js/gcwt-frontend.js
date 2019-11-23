window.addEventListener('load', function() {

	Vue.prototype.moment = moment

	var example_table = {
		"1030":{"1":false, "2":false, "3":false, "4":false, "5":false, "6":false ,"7":false},
		"1200":{"1":false, "2":false, "3":false, "4":false, "5":false, "6":false ,"7":false},
		"1400":{"1":false, "2":false, "3":false, "4":false, "5":false, "6":false ,"7":false},
		"1530":{"1":false, "2":false, "3":false, "4":false, "5":false, "6":false ,"7":false},
		"1800":{"1":false, "2":false, "3":false, "4":false, "5":false, "6":false ,"7":false}
	}

	moment.locale(window.global_settings_data.language)

	Vue.component('timetable', {
		template: "#gcwt_timetable_template",
		data: function() {
			return {
				loading: true,
				highlighted: {time: '', color: ''},
				table: example_table,
				colors: {},
				cycle: this.timetableAttrs['cycle'] == 'true',
				type: this.timetableAttrs['type'],
				compact: this.timetableAttrs['compact'],
				view_opt: {
					start: (this.timetableAttrs['start'] != null) ? moment(this.timetableAttrs['start']): moment().hours(0).minutes(0).seconds(0).day(this.timetableAttrs['week_start']),
					cal: this.timetableAttrs['urls'][0]['name']
				},
				// week_start: moment().hours(0).minutes(0).seconds(0).day(this.timetableAttrs['week_start']),
				weekdays: this.timetableAttrs['weekdays'],
				time_format: this.timetableAttrs['time_format'],
				table_style: this.timetableAttrs['style'],
				weekday_name: this.timetableAttrs['weekday_name'],
				caption: this.timetableAttrs['caption_text'],
				urls: this.timetableAttrs['urls']
			}
		},
		props: {
			timetableAttrs: Object
		},
		created: function() {
			this.doFetch({start: this.view_opt.start.format(), cal: this.view_opt.cal})
		},
		filters: {
			format_time: function(val) {
				return moment('19700101T' + val).format(global_settings_data['time_format'])
			},
			getTimeWithWeek: function(data) {
				var t = []
				for (time in data) {
					t.push({time: time, week: data[time]})
				}

				t.sort(function (a, b) {
				  return moment('19700101T' + a.time) - moment('19700101T' + b.time)
				})

				return t
			},
			getWeek: function(data, weekdays) {
				var t = []
				weekdays.forEach(function(wday) {
					t.push(data[wday])
				}, this)

				return t
			},
		},
		methods: {
			highlight: function(starttime) {
				if (starttime == this.highlighted.time) {
					return this.highlighted.color
				}
			},
			onEventHover: function(cell) {
				if (cell.rowspan) {
					this.highlighted = {
						time: cell.starttime, 
						color: this.colors[cell.summary] 
					}
					this.caption = '<span>' + this.$options.filters.format_time(cell.starttime) + ' - ' +
											   this.$options.filters.format_time(cell.endtime) + '</span> ' +
											   cell.summary + '. ' + (cell.description ? cell.description : '')
				}
			},
			onEventUnhover: function() {
				this.highlighted = {time: null, color: null}
				this.caption = this.timetableAttrs['caption_text']
			},
			dateRange: function() {
				var start_date = moment(this.view_opt.start)
				var end_date = moment(this.view_opt.start).add(1, 'w')
				var format = 'D'
				if (start_date.month() != end_date.month()) {
					format += ' MMM'
				}
				if (start_date.year() != end_date.year()) {
					format += ' YYYY'
				}

				return start_date.format(format) + ' - ' + end_date.format('D MMM YYYY')
			},
			doFetch: function(data){
				this.loading = true
				data.url = this.urls.filter((url) => { 
					return url.name == data.cal
				})[0]['url']
				fetch(window.ajaxurl + '?action=fetch_table_data', {
		    	method: 'POST',
					body: JSON.stringify(data),
					headers: {'Content-Type': 'application/json'},
				}).then(function(response) { return response.json() })
		    	.then(function(json) {
		    		this.loading = false
		    		this.colors = json.colors
		    		this.view_opt.cal = data.cal
		    		this.table = this.$root.get_table(json.events, this.weekdays, this.type, this.compact)
		    	}.bind(this))
			},
			previous: function(){
				this.view_opt.start = this.view_opt.start.subtract(1,'w')
				this.doFetch({start: this.view_opt.start.format(), cal: this.view_opt.cal})
			},
			next: function(){
				this.view_opt.start = this.view_opt.start.add(1,'w')
				this.doFetch({start: this.view_opt.start.format(), cal: this.view_opt.cal})
			}
		}
	})

	document.addEventListener('vueMounted', function() {
		// alert('yes')
	})

	var timetables = document.querySelectorAll('timetable.instance')
	var hook

	do {
		hook = timetables[0].parentNode
	} while ( hook.querySelectorAll('timetable.instance').length < timetables.length)

	var app = new Vue({
		el: hook,
		mounted: function() {
			var evt = new Event("vueMounted", {"bubbles":true, "cancelable":false});
			document.dispatchEvent(evt);
		},
		methods: {
			get_table: function(json, weekdays, type, isCompact) {
				// Get event start times
				var start_times = []
				for (key in json) {
					var start_time = json[key].dtstart.split('T')[1]
					if (start_times.includes(start_time)) {}
					else {
						start_times.push(start_time)
					}
				}

				if (isCompact == 'no') {

					// Get end times
					var end_times = []
					for (key in json) {
						var end_time = json[key].dtend.split('T')[1]
						if (end_times.includes(end_time)) {}
						else {
							end_times.push(end_time)
						}
					}

					// Redundantly sort the times for this test
					start_times.sort(function (a, b) {
					  return moment('19700101T' + a) - moment('19700101T' + b)
					})

					end_times.sort(function (a, b) {
					  return moment('19700101T' + a) - moment('19700101T' + b)
					})

					// Test start times for different gaps
					var consistent_start_times = []
					;[300, 240, 180, 150, 120, 90, 60, 45, 30, 15, 10, 5].some(function(gap) {
						var time, i = 0
						consistent_start_times = []

						// Generate all the times with this particular gap from first start time to last end time
						while( (time = moment('19700101T' + start_times[0]).add(gap*i, 'm')).isBefore(moment('19700101T' + end_times[end_times.length - 1])) ) {
							i++
							consistent_start_times.push(time.format('HHmmss'))
						}

						// Continue testing gaps until every event is covered then get out of the loop
						return start_times.every(function(start_time) {
							return consistent_start_times.includes(start_time)
						}, this)

					}, this)

					start_times = consistent_start_times

				}

				// Create empty matrix table
				var temp_table = {}
				start_times.forEach(function(start_time) {
					temp_table[start_time] = {}
				  weekdays.forEach(function(week_day){
				  	temp_table[start_time][week_day] = false
				  }, this)
				}, this)

				for (key in json){
					var event = json[key]
					var event_span = 1
					var event_weekday = moment(event.dtstart).isoWeekday()

					// Fill the rest of event into temp_table + calc eventspan-1
					for (var i = 0; i < start_times.length; i++) {
						if (start_times[i] > event.dtstart.split('T')[1] && start_times[i] < event.dtend.split('T')[1]) {
							temp_table[start_times[i]][event_weekday] = {rowspan: 0}
							event_span++
						}
					}

					// Fill the start cell with event info
					temp_table[event.dtstart.split('T')[1]][event_weekday] = {
						rowspan: event_span, 
						summary: event.summary,
						description: event.description,
						starttime: event.dtstart.split('T')[1],
						endtime: event.dtend.split('T')[1]
					}
				}

				// Delete empty rows
				start_times.forEach(function(start_time) {
					row_is_not_empty = weekdays.some(function(week_day){
				  	return temp_table[start_time][week_day] != false
				  }, this)

				  if (row_is_not_empty) {}
		  		else {
		  			delete temp_table[start_time]
			  	}

				}, this)

				return temp_table
			}
		}
	})

})