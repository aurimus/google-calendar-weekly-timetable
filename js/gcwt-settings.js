window.onload = function() {
	Vue.prototype.moment = moment

	var data_el = document.querySelector('[data-settings-atts]')
	var settings_data  = JSON.parse(data_el.dataset.settingsAtts)
	var settings_urls  = JSON.parse(data_el.dataset.urls)
	var settings_langs  = JSON.parse(data_el.dataset.langs)
	var trial_start  = JSON.parse(data_el.dataset.trial_start)

	Vue.component('weekdays', {
		data() {
			return { 
				dragWday: 0,
				weekdays: this.value
			}
		},
		props: {
			value: Array,
			lang: String,
			wd_name: String,
		},
		watch: {
			value: function(newWeekdays) {
				this.weekdays = newWeekdays
			}
		}
	})

	var vue_settings = new Vue({
		el: "#vue_settings_app",
		components: {
			'slider-picker': VueColor.Slider
		},
		data: function() {
			return {
				events: {},
				error: '',
				codeConfirmed: trial_start == 0 ? true : false,
				// text_color: document.getElementById('text_color').value,
				locale: settings_data.language,
				langs: settings_langs,
				weekdays: settings_data.weekdays.slice(0),
				settings: settings_data,
				urls: settings_urls
			}
		},
		methods: {
			fetchInfo: function(target) {
		    target.readOnly = true
		    var target_url = target.value
		    return fetch(window.ajaxurl + '?action=get_info', {
		    	method: 'POST',
		    	body: JSON.stringify({url: target_url}),
		    	headers: {'Content-Type': 'application/json'},
		    }).then(function(response){ return response.json() })
		    	.then(function(json) {
	    	  	target.readOnly = false
		    	  target.value = ''
		    	  if (json.error) {
		    	  	this.error = json.error
		    	  	return
		    	  } else { this.error = ''}
    	      this.urls.push({name: json.title, url: target_url})
    	    }.bind(this) ); 
			},
			updateSettings: function() {
				var data = this.settings
		    return fetch(window.ajaxurl + '?action=update_settings', {
		    	method: 'POST',
		    	body: JSON.stringify(data),
		    	headers: {'Content-Type': 'application/json'},
		    }).then(function(response){ return response.json() })
		    	.then(function(json) {
		    	  if (json.error) {
		    	  	this.weekdays = settings_data.weekdays.slice(0)
		    	  	return
		    	  }
    	    }.bind(this) ); 
			},
			onUrlPaste: function(evt){
				window.setTimeout(() => {
			    this.fetchInfo(evt.target)
				});
			},
			onUrlDelete: function(prev_url){
				this.urls.splice(this.urls.findIndex((el) => { return el == prev_url }), 1)
				fetch(window.ajaxurl + '?action=update_urls', {
		    	method: 'POST',
		    	body: JSON.stringify(this.urls),
		    	headers: {'Content-Type': 'application/json'},
		    }).then(function(response){
		    	if (response.ok) {
		    	  return response.json() 
		    	} else {
		    		return {error: response.status}
		    	}
		    }).then(function(json) {
	    	  if (json.error) {
	    	  	this.urls.push({name: prev_url.name, url: prev_url.url})
	    	  	alert('Something went wrong, try again')
	    	  	return
	    	  }
  	    }.bind(this) ); 
			},
			confirmCode: function(evt){
				var patreoncode = evt.clipboardData.getData('Text')
				fetch(window.ajaxurl + '?action=confirm_code', {
		    	method: 'POST',
		    	body: JSON.stringify(patreoncode),
		    	headers: {'Content-Type': 'application/json'},
		    }).then(function(response){
		    	if (response.ok) {
		    	  return response.json() 
		    	} else {
		    		return {error: response.status}
		    	}
		    }).then(function(json) {
	    	  if (json.error) {
	    	  	alert('Something went wrong, try again or contact me if it persist 4urimas@gmail.com')
	    	  	return
	    	  } else {
	    	  	this.codeConfirmed = true
	    	  	alert('Thank You so much!')
	    	  }
  	    }.bind(this) );
			}
		},
		mounted: function() {
			// var url_input = document.querySelector('#url')
			// if (url_input.value.length > 0) {
			// 	this.onUrlUpdate()
			// }
		}
	})


}