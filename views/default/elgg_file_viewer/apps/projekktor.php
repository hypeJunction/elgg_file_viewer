<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$ffmpeg = elgg_get_plugin_setting('enable_ffmpeg', 'elgg_file_viewer');

$mime = elgg_file_viewer_get_mime_type($entity);

$source = elgg_normalize_url("file/download/$entity->guid/$entity->originalfilename");

echo '<div class="elgg-col elgg-col-1of1 clearfloat">';

elgg_load_js('projekktor');
elgg_load_js('elgg.projekktor');
elgg_load_css('projekktor');
$attr = elgg_format_attributes(array(
	'class' => 'projekktor',
	'width' => 640,
	'height' => 480,
	'id' => "projekktor-$entity->guid",
	'poster' => elgg_normalize_url('mod/elgg_file_viewer/media/intro.png'),
	'title' => $entity->title
		));

$base_type = substr($mime, 0, strpos($mime, '/'));

switch ($base_type) {

	default :
	case 'video' :

		if ($ffmpeg) {
			$mp4 = elgg_file_viewer_get_media_url($entity, 'mp4');
			$webm = elgg_file_viewer_get_media_url($entity, 'webm');
			$ogv = elgg_file_viewer_get_media_url($entity, 'ogv');

			echo <<<HTML
		<video $attr>
			<source src="$mp4" type="video/mp4" />
			<source src="$webm" type="video/webm" />
			<source src="$ogv" type="video/ogg" />
		</video>
HTML;
		} else {
			echo "<video src=\"$source\" $attr></video>";
		}
		break;

	case 'audio' :
		if ($ffmpeg) {
			$mpeg = elgg_file_viewer_get_media_url($entity, 'mpeg');
			$wav = elgg_file_viewer_get_media_url($entity, 'wav');
			$ogg = elgg_file_viewer_get_media_url($entity, 'ogg');

			echo <<<HTML
		<audio $attr>
			<source src="$ogg" type="audio/ogg" />
			<source src="$mpeg" type="audio/mpeg" />
			<source src="$wav" type="audio/wav" />
		</audio>
HTML;
		} else {
			echo "<audio src=\"$source\" $attr></audio>";
		}
		break;
}


echo '</div>';
