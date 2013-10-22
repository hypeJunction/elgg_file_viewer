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
if ($base_type !== 'application') {
	echo elgg_view("file/specialcontent/$base_type/default", $vars);
	return;
}

if (elgg_view_exists("file/specialcontent/$mime")) {
	echo elgg_view("file/specialcontent/$mime", $vars);
	return;
}

$url = urlencode(elgg_file_viewer_get_public_url($entity));

switch ($mime) {

	case 'application/msword' :
	case 'application/msexcel' :
	case 'application/mspowerpoint' :
	case 'application/mswrite' :
	case 'application/x-msword' :
	case 'application/x-excel' :
	case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' :
	case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template' :
	case 'application/vnd.ms-excel' :
	case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' :
	case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template' :
	case 'application/vnd.ms-powerpoint' :
	case 'application/vnd.openxmlformats-officedocument.presentationml.presentation' :
	case 'application/vnd.openxmlformats-officedocument.presentationml.template' :
	case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' :
		$iframe_url = "http://view.officeapps.live.com/op/view.aspx?src=$url";
		break;

	default :
		$iframe_url = "https://docs.google.com/viewer?url=$url&embedded=true&overridemobile=true";
		break;
}

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