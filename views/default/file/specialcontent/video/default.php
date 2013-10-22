<?php

$full_view = elgg_extract('full_view', $vars, false);
if (!$full_view) {
	return;
}

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$mime = elgg_file_viewer_get_mime_type($entity);

$base_type = substr($mime, 0, strpos($mime, '/'));
if ($base_type !== 'video') {
	echo elgg_view("file/specialcontent/$base_type/default", $vars);
	return;
}

if (elgg_view_exists("file/specialcontent/$mime")) {
	echo elgg_view("file/specialcontent/$mime", $vars);
	return;
}

elgg_load_js('video-js');
elgg_load_js('elgg.video-js');
elgg_load_css('video-js');

$url = elgg_normalize_url("file/download/$entity->guid");

echo '<div class="elgg-col elgg-col-1of1 clearfloat">';
echo "<video id=\"$entity->title\" class=\"video-js vjs-default-skin\" controls width=\"100%\">";
echo "<source src=\"$url\" type=\"$mime\" />";
echo "</video>";
echo '</div>';
