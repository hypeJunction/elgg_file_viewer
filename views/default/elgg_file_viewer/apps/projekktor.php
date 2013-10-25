<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$mime = elgg_file_viewer_get_mime_type($entity);

$url = elgg_normalize_url("file/download/$entity->guid/$entity->originalfilename");

echo '<div class="elgg-col elgg-col-1of1 clearfloat">';

elgg_load_js('projekktor');
elgg_load_js('elgg.projekktor');
elgg_load_css('projekktor');
$attr = elgg_format_attributes(array(
	'class' => 'projekktor',
	'src' => $url,
	'width' => 640,
	'height' => 480,
	'id' => "projekktor-$entity->guid",
	'poster' => elgg_normalize_url('mod/elgg_file_viewer/media/intro.png'),
	'title' => $entity->title
		));
$tag = 'video';
$base_type = substr($mime, 0, strpos($mime, '/'));
if ($base_type == 'audio') {
	$tag = 'audio';
}
echo "<$tag $attr></$tag>";

echo '</div>';
