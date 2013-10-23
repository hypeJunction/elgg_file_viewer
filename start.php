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
			), 'Access a non-public file from a remote location', 'GET', false, false);



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

	if ($file->access_id == ACCESS_PUBLIC || !elgg_is_logged_in()) {
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