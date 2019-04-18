<?php

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof ElggFile) {
	return;
}

$url = elgg_get_download_url($entity);
$iframe_url = elgg_normalize_url("mod/elgg_file_viewer/providers/viewerjs-0.5.8/ViewerJS/index.html#$url");

$attr = elgg_format_attributes(array(
	'src' => $iframe_url,
	'name' => $entity->title,
	'height' => 780,
	'width' => "100%",
	'seamless' => true,
		));

echo '<div class="elgg-col elgg-col-1of1 clearfloat">';
echo "<iframe $attr></iframe>";
echo '</div>';