<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$mime = elgg_file_viewer_get_mime_type($entity);

$base_type = substr($mime, 0, strpos($mime, '/'));
if ($base_type !== 'image') {
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
if ($app && $app != 'none') {
	echo elgg_view("elgg_file_viewer/apps/$app", $vars);
	return;
}

$image_url = elgg_format_url($entity->getIconURL('large'));
$download_url = elgg_get_site_url() . "file/download/{$entity->getGUID()}";

echo <<<HTML
<div class="file-photo">
	<a href="$download_url"><img class="elgg-photo" src="$image_url" /></a>
</div>
HTML;

