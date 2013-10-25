<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$mime = elgg_file_viewer_get_mime_type($entity);

$base_type = substr($mime, 0, strpos($mime, '/'));
if ($base_type !== 'text') {
	echo elgg_view("file/specialcontent/$base_type/default", $vars);
	return;
}

if (elgg_view_exists("file/specialcontent/$mime")) {
	echo elgg_view("file/specialcontent/$mime", $vars);
	return;
}

$info = pathinfo($entity->getFilenameOnFilestore());
$extension = $info['extension'];

$app = elgg_get_plugin_setting($extension, 'elgg_file_viewer');
if (!$app || $app == 'none') {
	return;
}

echo elgg_view("elgg_file_viewer/apps/$app", $vars);
