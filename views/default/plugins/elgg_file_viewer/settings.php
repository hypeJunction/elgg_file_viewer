<?php

$entity = elgg_extract('entity', $vars);

echo '<div>';
echo '<label>' . elgg_echo('efv:settings:enable_ffmpeg') . '</label>';
echo elgg_view('input/dropdown', [
	'name' => 'params[enable_ffmpeg]',
	'value' => $entity->enable_ffmpeg,
	'options_values' => [
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
	]
]);
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('efv:settings:ffmpeg_path') . '</label>';
echo elgg_view('input/text', [
	'name' => 'params[ffmpeg_path]',
	'value' => $entity->ffmpeg_path,
]);
echo '</div>';

$config = [
	'pdf' => [
		'ext' => ['pdf'],
		'apps' => ['google', 'viewerjs'],
	],
	'msoffice' => [
		'ext' => [
			'doc',
			'dot',
			'docx',
			'dotx',
			'ppt',
			'pot',
			'pps',
			'pptx',
			'potx',
			'ppsx',
			'xls',
			'xlt',
			'xlsx',
			'xltx'
		],
		'apps' => ['msoffice', 'google'],
	],
	'odf' => [
		'ext' => ['odt', 'ods', 'odp', 'odg', 'odf', 'fodt', 'fods', 'fodp', 'fodg'],
		'apps' => ['viewerjs'],
	],
	'media' => [
		'ext' => ['mp4', 'mp3', 'avi', 'ogv', 'webm', 'ogg', 'anx', 'flv', 'mov', 'm4v', 'wav'],
		'apps' => ['projekktor', 'google']
	],
	'text' => [
		'ext' => ['txt'],
		'apps' => ['google', 'syntaxhighlighter']
	],
	'code' => [
		'ext' => ['css', 'htm', 'html', 'php', 'js', 'xml'],
		'apps' => ['google', 'syntaxhighlighter']
	],
	'graphic' => [
		'ext' => ['tif', 'tiff', 'ai', 'psd', 'dxf', 'svg', 'bmp'],
		'apps' => ['google'],
	],
	'postscript' => [
		'ext' => ['eps', 'ps'],
		'apps' => ['google'],
	],
	'archive' => [
		'ext' => ['zip', 'rar'],
		'apps' => ['google'],
	],
	'other' => [
		'ext' => ['pages', 'otf', 'ttf', 'xps'],
		'apps' => ['google'],
	],
];

foreach ($config as $type => $options) {

	echo '<hr />';

	echo '<div>';
	echo '<h3>' . elgg_echo("efv:type:$type") . '</h3>';

	$app_options = [];
	foreach ($options['apps'] as $app) {
		$app_options[$app] = elgg_echo("efv:app:$app");
	}
	$app_options['none'] = elgg_echo('efv:app:none');

	foreach ($options['ext'] as $ext) {
		echo '<div>';
		echo "<label>*.$ext</label>";
		echo elgg_view('input/dropdown', [
			'name' => "params[$ext]",
			'value' => $entity->$ext,
			'options_values' => $app_options
		]);
		echo '</div>';
	}

	echo '</div>';
}






