<?php

$entity = elgg_extract('entity', $vars);

echo '<div>';
echo '<label>' . elgg_echo('efv:settings:mime_remap') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[mime_remap]',
	'value' => $entity->mime_remap,
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
	)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('efv:settings:enable_ffmpeg') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[enable_ffmpeg]',
	'value' => $entity->enable_ffmpeg,
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
	)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('efv:settings:ffmpeg_path') . '</label>';
echo elgg_view('input/text', array(
	'name' => 'params[ffmpeg_path]',
	'value' => $entity->ffmpeg_path,
));
echo '</div>';

$config = array(
	'pdf' => array(
		'ext' => array('pdf'),
		'apps' => array('google'),
	),
	'msoffice' => array(
		'ext' => array('doc', 'dot', 'docx', 'dotx', 'ppt', 'pot', 'pps', 'pptx', 'potx', 'ppsx', 'xls', 'xlt', 'xlsx', 'xltx'),
		'apps' => array('msoffice', 'google'),
	),
	'media' => array(
		'ext' => array('mp4', 'mp3', 'avi', 'ogv', 'webm', 'ogg', 'anx', 'flv', 'mov', 'm4v', 'wav'),
		'apps' => array('projekktor', 'google', 'divx')
	),
	'text' => array(
		'ext' => array('txt'),
		'apps' => array('google', 'syntaxhighlighter')
	),
	'code' => array(
		'ext' => array('css', 'htm', 'html', 'php', 'js', 'xml'),
		'apps' => array('google', 'syntaxhighlighter')
	),
	'graphic' => array(
		'ext' => array('tif', 'tiff', 'ai', 'psd', 'dxf', 'svg', 'bmp'),
		'apps' => array('google'),
	),
	'postscript' => array(
		'ext' => array('eps', 'ps'),
		'apps' => array('google'),
	),
	'archive' => array(
		'ext' => array('zip', 'rar'),
		'apps' => array('google'),
	),
	'other' => array(
		'ext' => array('pages', 'otf', 'ttf', 'xps'),
		'apps' => array('google'),
	),
);

foreach ($config as $type => $options) {

	echo '<hr />';

	echo '<div>';
	echo '<h3>' . elgg_echo("efv:type:$type") . '</h3>';

	$app_options = array();
	foreach ($options['apps'] as $app) {
		$app_options[$app] = elgg_echo("efv:app:$app");
	}
	$app_options['none'] = elgg_echo('efv:app:none');

	foreach ($options['ext'] as $ext) {
		echo '<div>';
		echo "<label>*.$ext</label>";
		echo elgg_view('input/dropdown', array(
			'name' => "params[$ext]",
			'value' => $entity->$ext,
			'options_values' => $app_options
		));
		echo '</div>';
	}
	
	echo '</div>';
}






