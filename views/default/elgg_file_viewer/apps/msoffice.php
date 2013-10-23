<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$url = urlencode(elgg_file_viewer_get_public_url($entity));
$iframe_url = "http://view.officeapps.live.com/op/view.aspx?src=$url";

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