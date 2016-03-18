define(function(require) {
	var videojs = require('videojs');
	videojs.options.flash.swf = elgg.get_simplecache_url('videojs/video-js.swf');
	return videojs;
});