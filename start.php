<?php

define('EFV_MIME_REMAP', elgg_get_plugin_setting('mime_remap', 'elgg_file_viewer'));

/**
 * Elgg Viewer
 */
elgg_register_event_handler('init', 'system', 'elgg_file_viewer_init');

function elgg_file_viewer_init() {

	// Registering a new viewtype for output buffer
	elgg_register_viewtype('ob');
	elgg_register_viewtype_fallback('ob');

	// Exposing a function for remote access to non-public files
	expose_function('efv.download', 'elgg_file_viewer_download', array(
		'guid' => array(
			'type' => 'int',
			'required' => true
		)
			), 'Access a non-public file from a remote location', 'GET', false, true);

	// Projekktor for Video/Audio support
	elgg_register_js('projekktor', '/mod/elgg_file_viewer/vendors/projekktor-1.2.38r332/projekktor-1.2.38r332.min.js');
	elgg_register_simplecache_view('js/elgg_file_viewer/projekktor');
	elgg_register_js('elgg.projekktor', elgg_get_simplecache_url('js', 'elgg_file_viewer/projekktor'), 'footer');

	elgg_register_css('projekktor', '/mod/elgg_file_viewer/vendors/projekktor-1.2.38r332/theme/maccaco/projekktor.style.css');

	// Syntax highlighter / text support
	elgg_register_js('syntaxhighlighter', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shCore.js', 'footer');
	elgg_register_js('syntaxhighlighter.css', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushCss.js', 'footer');
	elgg_register_js('syntaxhighlighter.xml', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushXml.js', 'footer');
	elgg_register_js('syntaxhighlighter.plain', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushPlain.js', 'footer');
	elgg_register_js('syntaxhighlighter.js', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushJScript.js', 'footer');
	elgg_register_js('syntaxhighlighter.php', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushPhp.js', 'footer');
	elgg_register_simplecache_view('js/elgg_file_viewer/syntaxhighlighter');
	elgg_register_js('elgg.syntaxhighlighter', elgg_get_simplecache_url('js', 'elgg_file_viewer/syntaxhighlighter'), 'footer');

	elgg_register_css('syntaxhighlighter.core', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/styles/shCore.css');
	elgg_register_css('syntaxhighlighter.theme', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/styles/shThemeDefault.css');

	// FFmpeg class
	elgg_register_class('FFmpeg', elgg_get_plugins_path() . 'elgg_file_viewer/vendors/ffmpeg/src/ffmpeg.class.php');
	elgg_register_page_handler('projekktor', 'elgg_file_viewer_projekktor_video');
}

/**
 * Make file accessible via a rest handler
 *
 * @param int $guid GUID of the file
 */
function elgg_file_viewer_download($guid) {

	$file = get_entity($guid);

	if (!elgg_instanceof($file, 'object', 'file')) {
		return array('error' => elgg_echo('file:downloadfailed'));
	}

	$exportable_values = $file->getExportableValues();

	$export = new stdClass();

	foreach ($exportable_values as $v) {
		$export->$v = $file->$v;
	}

	$export->url = $file->getURL();

	return array($export);
}

/**
 * Get publicly accessible URL for the file
 *
 * @param ElggFile $file
 * @return string
 */
function elgg_file_viewer_get_public_url($file) {

	if (!elgg_instanceof($file, 'object', 'file')) {
		return '';
	}

	if (!elgg_is_logged_in()) {
		return $file->getURL();
	}

	$user = elgg_get_logged_in_user_entity();
	$token = create_user_token($user->username);

	$base_url = elgg_normalize_url("services/api/rest/ob");
	$params = array(
		'method' => 'efv.download',
		'guid' => $file->getGUID(),
		'auth_token' => $token
	);

	return elgg_http_add_url_query_elements($base_url, $params);
}

/**
 * Get a URL to the alternative format of the video/audio file
 * @param ElggFile $file
 * @param string $format
 */
function elgg_file_viewer_get_media_url($file, $format) {

	if (!elgg_instanceof($file, 'object', 'file')) {
		return '';
	}

	if (!elgg_is_logged_in()) {
		return $file->getURL();
	}

	return elgg_normalize_url("projekktor/$file->guid/$format/media.$format");
}

/**
 * Fix mime
 * 
 * @param ElggFile $file
 * @return boolean|string
 */
function elgg_file_viewer_get_mime_type($file) {

	if (!elgg_instanceof($file, 'object', 'file')) {
		return false;
	}

	$mime = $file->getMimeType();

	if (!$mime) {
		$mime = "application/octet-stream";
	}

	if (EFV_MIME_REMAP != 'yes') {
		return $mime;
	}

	$info = pathinfo($file->getFilenameOnFilestore());

	$extension = $info['extension'];

	if ($mime == "application/zip") {
		switch ($extension) {
			case 'docx':
				$mime = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
				break;
			case 'xlsx':
				$mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
				break;
			case 'xltx' :
				$mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.template";
				break;
			case 'pptx':
				$mime = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
				break;
			case 'potx' :
				$mime = "application/vnd.openxmlformats-officedocument.presentationml.template";
				break;
			case 'ppsx' :
				$mime = "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
				break;
			case 'dotx' :
				$mime = "application/vnd.openxmlformats-officedocument.wordprocessingml.template";
				break;
		}
	}

	if ($mime == "application/vnd.ms-office") {
		switch ($extension) {
			case "ppt" :
			case "pot" :
			case 'pps' :
				$mime = "application/vnd.ms-powerpoint";
				break;
			case 'doc' :
			case 'dot' :
				$mime = 'application/msword';
				break;
			case 'xls' :
			case 'xlt' :
			case 'xla' :
				$mime = "application/vnd.ms-excel";
				break;
		}
	}

	return $mime;
}

/**
 * Serve a converted web compatible video
 * URL structure: projekktor/<guid>/<format>/
 *
 * @param array $page Page segments array
 */
function elgg_file_viewer_projekktor_video($page) {

	$enable_ffmpeg = elgg_get_plugin_setting('enable_ffmpeg', 'elgg_file_viewer');
	if ($enable_ffmpeg != 'yes') {
		return false;
	}

	$guid = elgg_extract(0, $page, null);
	$file = get_entity($guid);

	if (!elgg_instanceof($file, 'object', 'file')) {
		return false;
	}

	$info = pathinfo($file->getFilenameOnFilestore());
	$filename = $info['filename'];

	$format = elgg_extract(1, $page);

	$output = new ElggFile();
	$output->owner_guid = $file->owner_guid;
	$output->setFilename("projekktor/$file->guid/$filename.$format");

	if (!$output->size()) {
		try {
			$filestorename = $output->getFilenameOnFilestore();

			$output->open('write');
			$output->close();

			$ffmpeg_path = elgg_get_plugin_setting('ffmpeg_path', 'elgg_file_viewer');

			$FFmpeg = new FFmpeg($ffmpeg_path);
			$FFmpeg->input($file->getFilenameOnFilestore())->output($filestorename)->ready();

			elgg_log("Converting file $file->guid to $format: $FFmpeg->command", 'NOTICE');
		} catch (Exception $e) {
			elgg_log($e->getMessage(), 'ERROR');
		}
	}

	$mime = elgg_file_viewer_get_mime_type($file);
	$base_type = substr($mime, 0, strpos($mime, '/'));

	header("Pragma: public");
	header("Content-type: $base_type/$format");
	header("Content-Disposition: attachment; filename=\"$filename.$format\"");

	ob_clean();
	flush();
	readfile($output->getFilenameOnFilestore());
}