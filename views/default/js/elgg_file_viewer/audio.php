//<script>

	elgg.provide('elgg.audio');

	elgg.audio.init = function() {
		audiojs.events.ready(function() {
			var as = audiojs.createAll();
		});
	}

	elgg.register_hook_handler('init', 'system', elgg.video.init);