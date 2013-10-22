<?php

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


	// Video support
	elgg_register_js('video-js', '/mod/elgg_file_viewer/vendors/video-js/video.js', 'footer');
	elgg_register_simplecache_view('js/elgg_file_viewer/video');
	elgg_register_js('elgg.video-js', elgg_get_simplecache_url('js', 'elgg_file_viewer/video'), 'footer');

	elgg_register_css('video-js', '/mod/elgg_file_viewer/vendors/video-sj/video-js.min.css');

	// Audio support
	elgg_register_js('audiojs', '/mod/elgg_file_viewer/vendors/audiojs/audio.min.js', 'footer');
	elgg_register_simplecache_view('js/elgg_file_viewer/audio');
	elgg_register_js('elgg.audiojs', elgg_get_simplecache_url('js', 'elgg_file_viewer/audio'), 'footer');

	// Syntax highlighter / text support
	elgg_register_js('syntaxhighlighter', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shCore.js', 'footer');
	elgg_register_js('syntaxhighlighter.css', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushCss.js', 'footer');
	elgg_register_js('syntaxhighlighter.xml', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushXml.js', 'footer');
	elgg_register_js('syntaxhighlighter.plain', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushPlain.js', 'footer');
	elgg_register_js('syntaxhighlighter.js', '/mod/elgg_file_viewer/vendors/syntaxhighlighter_3.0.83/scripts/shBrushJScript.js', 'footer');
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

	$mime_types_map = array(
		'323' => 'text/h323',
		'acx' => 'application/internet-property-stream',
		'ai' => 'application/postscript',
		'aif' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'asf' => 'video/x-ms-asf',
		'asr' => 'video/x-ms-asf',
		'asx' => 'video/x-ms-asf',
		'au' => 'audio/basic',
		'avi' => 'video/x-msvideo',
		'axs' => 'application/olescript',
		'bas' => 'text/plain',
		'bcpio' => 'application/x-bcpio',
		'bin' => 'application/octet-stream',
		'bmp' => 'image/bmp',
		'c' => 'text/plain',
		'cat' => 'application/vnd.ms-pkiseccat',
		'cdf' => 'application/x-cdf',
		'cer' => 'application/x-x509-ca-cert',
		'class' => 'application/octet-stream',
		'clp' => 'application/x-msclip',
		'cmx' => 'image/x-cmx',
		'cod' => 'image/cis-cod',
		'cpio' => 'application/x-cpio',
		'crd' => 'application/x-mscardfile',
		'crl' => 'application/pkix-crl',
		'crt' => 'application/x-x509-ca-cert',
		'csh' => 'application/x-csh',
		'css' => 'text/css',
		'dcr' => 'application/x-director',
		'der' => 'application/x-x509-ca-cert',
		'dir' => 'application/x-director',
		'dll' => 'application/x-msdownload',
		'dms' => 'application/octet-stream',
		'doc' => 'application/msword',
		'dot' => 'application/msword',
		'dvi' => 'application/x-dvi',
		'dxr' => 'application/x-director',
		'eps' => 'application/postscript',
		'etx' => 'text/x-setext',
		'evy' => 'application/envoy',
		'exe' => 'application/octet-stream',
		'fif' => 'application/fractals',
		'flr' => 'x-world/x-vrml',
		'gif' => 'image/gif',
		'gtar' => 'application/x-gtar',
		'gz' => 'application/x-gzip',
		'h' => 'text/plain',
		'hdf' => 'application/x-hdf',
		'hlp' => 'application/winhlp',
		'hqx' => 'application/mac-binhex40',
		'hta' => 'application/hta',
		'htc' => 'text/x-component',
		'htm' => 'text/html',
		'html' => 'text/html',
		'htt' => 'text/webviewhtml',
		'ico' => 'image/x-icon',
		'ief' => 'image/ief',
		'iii' => 'application/x-iphone',
		'ins' => 'application/x-internet-signup',
		'isp' => 'application/x-internet-signup',
		'jfif' => 'image/pipeg',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'js' => 'application/x-javascript',
		'latex' => 'application/x-latex',
		'lha' => 'application/octet-stream',
		'lsf' => 'video/x-la-asf',
		'lsx' => 'video/x-la-asf',
		'lzh' => 'application/octet-stream',
		'm13' => 'application/x-msmediaview',
		'm14' => 'application/x-msmediaview',
		'm3u' => 'audio/x-mpegurl',
		'man' => 'application/x-troff-man',
		'mdb' => 'application/x-msaccess',
		'me' => 'application/x-troff-me',
		'mht' => 'message/rfc822',
		'mhtml' => 'message/rfc822',
		'mid' => 'audio/mid',
		'mny' => 'application/x-msmoney',
		'mov' => 'video/quicktime',
		'movie' => 'video/x-sgi-movie',
		'mp2' => 'video/mpeg',
		'mp3' => 'audio/mpeg',
		'mpa' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpp' => 'application/vnd.ms-project',
		'mpv2' => 'video/mpeg',
		'ms' => 'application/x-troff-ms',
		'mvb' => 'application/x-msmediaview',
		'nws' => 'message/rfc822',
		'oda' => 'application/oda',
		'p10' => 'application/pkcs10',
		'p12' => 'application/x-pkcs12',
		'p7b' => 'application/x-pkcs7-certificates',
		'p7c' => 'application/x-pkcs7-mime',
		'p7m' => 'application/x-pkcs7-mime',
		'p7r' => 'application/x-pkcs7-certreqresp',
		'p7s' => 'application/x-pkcs7-signature',
		'pbm' => 'image/x-portable-bitmap',
		'pdf' => 'application/pdf',
		'pfx' => 'application/x-pkcs12',
		'pgm' => 'image/x-portable-graymap',
		'pko' => 'application/ynd.ms-pkipko',
		'pma' => 'application/x-perfmon',
		'pmc' => 'application/x-perfmon',
		'pml' => 'application/x-perfmon',
		'pmr' => 'application/x-perfmon',
		'pmw' => 'application/x-perfmon',
		'pnm' => 'image/x-portable-anymap',
		'pot' => 'application/vnd.ms-powerpoint',
		'ppm' => 'image/x-portable-pixmap',
		'pps' => 'application/vnd.ms-powerpoint',
		'ppt' => 'application/vnd.ms-powerpoint',
		'prf' => 'application/pics-rules',
		'ps' => 'application/postscript',
		'pub' => 'application/x-mspublisher',
		'qt' => 'video/quicktime',
		'ra' => 'audio/x-pn-realaudio',
		'ram' => 'audio/x-pn-realaudio',
		'ras' => 'image/x-cmu-raster',
		'rgb' => 'image/x-rgb',
		'rmi' => 'audio/mid',
		'roff' => 'application/x-troff',
		'rtf' => 'application/rtf',
		'rtx' => 'text/richtext',
		'scd' => 'application/x-msschedule',
		'sct' => 'text/scriptlet',
		'setpay' => 'application/set-payment-initiation',
		'setreg' => 'application/set-registration-initiation',
		'sh' => 'application/x-sh',
		'shar' => 'application/x-shar',
		'sit' => 'application/x-stuffit',
		'snd' => 'audio/basic',
		'spc' => 'application/x-pkcs7-certificates',
		'spl' => 'application/futuresplash',
		'src' => 'application/x-wais-source',
		'sst' => 'application/vnd.ms-pkicertstore',
		'stl' => 'application/vnd.ms-pkistl',
		'stm' => 'text/html',
		'svg' => 'image/svg+xml',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc' => 'application/x-sv4crc',
		't' => 'application/x-troff',
		'tar' => 'application/x-tar',
		'tcl' => 'application/x-tcl',
		'tex' => 'application/x-tex',
		'texi' => 'application/x-texinfo',
		'texinfo' => 'application/x-texinfo',
		'tgz' => 'application/x-compressed',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',
		'tr' => 'application/x-troff',
		'trm' => 'application/x-msterminal',
		'tsv' => 'text/tab-separated-values',
		'txt' => 'text/plain',
		'uls' => 'text/iuls',
		'ustar' => 'application/x-ustar',
		'vcf' => 'text/x-vcard',
		'vrml' => 'x-world/x-vrml',
		'wav' => 'audio/x-wav',
		'wcm' => 'application/vnd.ms-works',
		'wdb' => 'application/vnd.ms-works',
		'wks' => 'application/vnd.ms-works',
		'wmf' => 'application/x-msmetafile',
		'wps' => 'application/vnd.ms-works',
		'wri' => 'application/x-mswrite',
		'wrl' => 'x-world/x-vrml',
		'wrz' => 'x-world/x-vrml',
		'xaf' => 'x-world/x-vrml',
		'xbm' => 'image/x-xbitmap',
		'xla' => 'application/vnd.ms-excel',
		'xlc' => 'application/vnd.ms-excel',
		'xlm' => 'application/vnd.ms-excel',
		'xls' => 'application/vnd.ms-excel',
		'xlt' => 'application/vnd.ms-excel',
		'xlw' => 'application/vnd.ms-excel',
		'xof' => 'x-world/x-vrml',
		'xpm' => 'image/x-xpixmap',
		'xwd' => 'image/x-xwindowdump',
		'z' => 'application/x-compress',
		'zip' => 'application/zip',
	);

	$info = pathinfo($file->getFilenameOnFilestore());

	$extension = $info['extension'];

	if (array_key_exists($extension, $mime_types_map)) {
		if ($mime != $mime_types_map[$extension] && substr($mime_types_map[$extension], 0, strpos($mime_types_map[$extension], '/')) !== 'application') {
			$mime = $mime_types_map[$extension];
		}
	}

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
			case 'ppa' :

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

	if (!$mime) {
		$mime = "application/octet-stream";
	}

	return $mime;
}