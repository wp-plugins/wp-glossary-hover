(function ($) {
	$(document).ready(function() {
		var jquery_ui_tooltip_options = {
			hide: false,
			show: false,
			track: wp_glossary_hover_tooltip_settings['tooltip_general_track']
		};

		if (wp_glossary_hover_tooltip_settings['tooltip_hide_enabled']) {
			jquery_ui_tooltip_options.hide = {
				effect: wp_glossary_hover_tooltip_settings['tooltip_hide_effect'],
				delay: wp_glossary_hover_tooltip_settings['tooltip_hide_delay'],
				duration: wp_glossary_hover_tooltip_settings['tooltip_hide_duration'],
				easing: wp_glossary_hover_tooltip_settings['tooltip_hide_easing']
			};
		}

		if (wp_glossary_hover_tooltip_settings['tooltip_show_enabled']) {
			jquery_ui_tooltip_options.show = {
				effect: wp_glossary_hover_tooltip_settings['tooltip_show_effect'],
				delay: wp_glossary_hover_tooltip_settings['tooltip_show_delay'],
				duration: wp_glossary_hover_tooltip_settings['tooltip_show_duration'],
				easing: wp_glossary_hover_tooltip_settings['tooltip_show_easing']
			};
		};

		$('.wpgh-tooltip').tooltip(jquery_ui_tooltip_options);
	});

}(jQuery));