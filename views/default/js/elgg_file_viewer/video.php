//<script>

	elgg.provide('elgg.video');

	elgg.video.init = function() {
		videojs.options.flash.swf = elgg.get_site_url() + "mod/elgg_file_viewer/vendors/video-js/video-js.swf";
	}

	elgg.register_hook_handler('init', 'system', elgg.video.init);