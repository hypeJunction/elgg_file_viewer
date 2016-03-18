<?php

require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'elgg_file_viewer_init');

/**
 * Init
 * @return void
 */
function elgg_file_viewer_init() {

	// Syntax highlighting
	elgg_register_css('prism', elgg_get_simplecache_url('prism/themes/prism.css'));
	elgg_extend_view('prism/themes/prism.css', 'prism/plugins/line-numbers/prism-line-numbers.css');

	elgg_define_js('prism', [
		'src' => elgg_get_simplecache_url('prism/prism.js'),
		'exports' => 'Prism',
	]);
	elgg_define_js('prism-line-numbers', [
		'src' => elgg_get_simplecache_url('prism/plugins/line-numbers/prism-line-numbers.js'),
		'deps' => ['prism'],
	]);

	// Videojs
	elgg_register_css('videojs', elgg_get_simplecache_url('videojs/video-js.min.css'));

	elgg_define_js('videojs', [
		'src' => elgg_get_simplecache_url('videojs/video.min.js'),
	]);

	elgg_register_page_handler('projekktor', 'elgg_file_viewer_projekktor_video');

	elgg_register_event_handler('create', 'object', 'elgg_file_viewer_make_web_compatible');
	elgg_register_event_handler('update:after', 'object', 'elgg_file_viewer_make_web_compatible');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'elgg_file_view_set_video_icon_url');
}

/**
 * Get publicly accessible URL for the file
 *
 * @param ElggFile $file File entity
 * @return string|false
 */
function elgg_file_viewer_get_public_url($file) {

	if (!$file instanceof ElggFile) {
		return false;
	}

	return elgg_get_download_url($file, false, '+60 minutes');
}

/**
 * Get a URL to the alternative format of the video/audio file
 *
 * @param ElggFile $file   File entity
 * @param string   $format Video format (extension)
 * @return string
 */
function elgg_file_viewer_get_media_url($file, $format) {

	if (!$file instanceof ElggFile) {
		return '';
	}

	$info = pathinfo($file->getFilenameOnFilestore());
	$filename = $info['filename'];

	$output = new ElggFile();
	$output->owner_guid = $file->owner_guid;
	$output->setFilename("projekktor/$file->guid/$filename.$format");

//	if (!$output->exists() && elgg_get_plugin_setting('enable_ffmpeg', 'elgg_file_viewer')) {
//		$output = elgg_file_viewer_convert_file($file, $format);
//	}

	return elgg_get_download_url($output);
}

/**
 * Fix mime
 * 
 * @param ElggFile $file File entity
 * @return string
 */
function elgg_file_viewer_get_mime_type($file) {

	if (!$file instanceof ElggFile) {
		return 'application/otcet-stream';
	}

	return $file->detectMimeType();
}

/**
 * Serve a converted web compatible video
 * URL structure: projekktor/<guid>/<format>/
 *
 * @param array $page URL segments
 * @deprecated 2.0
 */
function elgg_file_viewer_projekktor_video($page) {

	$guid = elgg_extract(0, $page, null);
	$file = get_entity($guid);
	$format = elgg_extract(1, $page);

	$url = elgg_file_viewer_get_media_url($file, $format);
	if (!$url) {
		forward('', '404');
	}

	forward($url);
}

/**
 * Convert a video/audio file to a web compatible format
 * 
 * @param ElggFile $file   File entity
 * @param string   $format Format to convert to (extension)
 * @return ElggFile|false
 */
function elgg_file_viewer_convert_file($file, $format) {

	if (!$file instanceof ElggFile || !$format) {
		return false;
	}

	$ffmpeg_path = elgg_get_plugin_setting('ffmpeg_path', 'elgg_file_viewer');
	if (!$ffmpeg_path) {
		return false;
	}

	$info = pathinfo($file->getFilenameOnFilestore());
	$filename = $info['filename'];

	$output = new ElggFile();
	$output->owner_guid = $file->owner_guid;
	$output->setFilename("projekktor/$file->guid/$filename.$format");
	$output->open('write');
	$output->close();

	try {
		$FFmpeg = new FFmpeg($ffmpeg_path);

		if (!$file->icontime) {
			$icon = new ElggFile();
			$icon->owner_guid = $file->owner_guid;
			$icon->setFilename("projekktor/$file->guid/$filename.jpg");
			$FFmpeg->input($file->getFilenameOnFilestore())->thumb(0, 1)->output($icon->getFilenameOnFilestore())->ready();

			if ($icon->exists()) {
				$file->icontime = time();
				$file->ffmpeg_thumb = $icon->getFilename();

				$prefix = 'file/';
				$filestorename = $file->icontime . $filename . '.jpg';

				$thumbnail = get_resized_image_from_existing_file($icon->getFilenameOnFilestore(), 60, 60, true);
				if ($thumbnail) {
					$thumb = new ElggFile();
					$thumb->setMimeType($_FILES['upload']['type']);

					$thumb->setFilename($prefix . "thumb" . $filestorename);
					$thumb->open("write");
					$thumb->write($thumbnail);
					$thumb->close();

					$file->thumbnail = $prefix . "thumb" . $filestorename;
					unset($thumbnail);
				}

				$thumbsmall = get_resized_image_from_existing_file($icon->getFilenameOnFilestore(), 153, 153, true);
				if ($thumbsmall) {
					$thumb->setFilename($prefix . "smallthumb" . $filestorename);
					$thumb->open("write");
					$thumb->write($thumbsmall);
					$thumb->close();
					$file->smallthumb = $prefix . "smallthumb" . $filestorename;
					unset($thumbsmall);
				}

				$thumblarge = get_resized_image_from_existing_file($icon->getFilenameOnFilestore(), 600, 600, false);
				if ($thumblarge) {
					$thumb->setFilename($prefix . "largethumb" . $filestorename);
					$thumb->open("write");
					$thumb->write($thumblarge);
					$thumb->close();
					$file->largethumb = $prefix . "largethumb" . $filestorename;
					unset($thumblarge);
				}
			}
		}

		$FFmpeg->input($file->getFilenameOnFilestore())->output($output->getFilenameOnFilestore())->ready();
		elgg_log("Converting file $file->guid to $format: $FFmpeg->command", 'NOTICE');
	} catch (Exception $ex) {
		elgg_log($ex->getMessage(), 'ERROR');
		return false;
	}

	return $output;
}

/**
 * Create web compatible instances of audio/video files
 *
 * @param string   $event  "create"|"update:after"
 * @param string   $type   "object"
 * @param ElggFile $entity File entity
 * @return void
 */
function elgg_file_viewer_make_web_compatible($event, $type, $entity) {

	if (!$entity instanceof ElggFile) {
		return;
	}

	if (!elgg_get_plugin_setting('enable_ffmpeg', 'elgg_file_viewer')) {
		return;
	}

	if (!elgg_is_active_plugin('vroom')) {
		return;
	}

	$file_guids = (array) elgg_get_config('elgg_file_viewer_file_guids');
	$file_guids[] = $entity->guid;
	elgg_set_config('elgg_file_viewer_file_guids', $file_guids);

	elgg_register_event_handler('shutdown', 'system', 'elgg_file_viewer_vroom');
}

/**
 * Vroom callback
 * @return void
 */
function elgg_file_viewer_vroom() {

	$file_guids = (array) elgg_get_config('elgg_file_viewer_file_guids');
	$file_guids = array_unique($file_guids);

	foreach ($file_guids as $guid) {
		$entity = get_entity($guid);
		if (!$entity instanceof ElggFile) {
			continue;
		}

		$entity_mime = $entity->getMimeType();
		list($base_type, $ext) = explode('/', $entity_mime);

		$mimes = [];
		switch ($base_type) {
			case 'video' :
				$mimes = [
					'video/mp4',
					'video/webm',
					'video/ogv',
				];
				break;

			case 'audio' :
				$mimes = [
					'audio/mpeg',
					'audio/ogg',
					'video/wav',
				];
				break;
		}

		foreach ($mimes as $mime) {
			if ($mime == $entity_mime) {
				continue;
			}
			list(, $ext) = explode('/', $mime);
			$url = elgg_file_viewer_get_media_url($entity, $ext);
			if (!$url) {
				elgg_file_viewer_convert_file($entity, $ext);
			}
		}
	}
}

/**
 * Handle video file icon URLs
 * 
 * @param string $hook   "entity:icon:url"
 * @param string $type   "object"
 * @param string $return URL
 * @param array  $params Hook params
 * @return string
 */
function elgg_file_view_set_video_icon_url($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	if (!$entity instanceof ElggFile || !$entity->icontime || !$entity->ffmpeg_thumb) {
		return;
	}

	$size = elgg_extract('size', $params, 'medium');

	switch ($size) {
		case "small":
			$thumbfile = $entity->thumbnail;
			break;
		case "medium":
			$thumbfile = $entity->smallthumb;
			break;
		case "large":
		default:
			$thumbfile = $entity->largethumb;
			break;
	}

	$icon = new ElggFile();
	$icon->owner_guid = $entity->owner_guid;
	$icon->setFilename($thumbfile);

	$url = elgg_get_inline_url($icon);
	if ($url) {
		return $url;
	}
}
