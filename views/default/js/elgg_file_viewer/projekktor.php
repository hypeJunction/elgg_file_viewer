//<script>

	elgg.provide('elgg.projekktor');

	elgg.projekktor.init = function() {
		var swfplayer = elgg.get_site_url() + 'mod/elgg_file_viewer/vendors/projekktor-1.2.38r332/jarisplayer.swf';		
		var playerconfig = {
			platformPriority: ['flash','native'],
			playerFlashMP4: swfplayer,
			playerFlashMP3: swfplayer
		}

		projekktor('video', playerconfig);
		projekktor('audio', playerconfig);
	}

	elgg.register_hook_handler('init', 'system', elgg.projekktor.init);