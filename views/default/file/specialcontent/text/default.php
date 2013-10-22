<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

elgg_load_js('syntaxhighlighter');
elgg_load_js('elgg.syntaxhighlighter');
elgg_load_css('syntaxhighlighter.core');
elgg_load_css('syntaxhighlighter.theme');

$mime = elgg_file_viewer_get_mime_type($entity);

$base_type = substr($mime, 0, strpos($mime, '/'));
if ($base_type !== 'text' && $mime != 'application/javascript') {
	echo elgg_view("file/specialcontent/$base_type/default", $vars);
	return;
}

if ($mime != 'application/javascript' && elgg_view_exists("file/specialcontent/$mime")) {
	echo elgg_view("file/specialcontent/$mime", $vars);
	return;
}

switch ($mime) {
	default :
		elgg_load_js('syntaxhighlighter.plain');
		$brush = 'plain';
		break;

	case 'application/javascript' :
		elgg_load_js('syntaxhighlighter.js');
		$brush = 'js';
		break;

	case 'text/css' :
		elgg_load_js('syntaxhighlighter.css');
		$brush = 'css';
		break;

	case 'text/html' :
	case 'text/xml' :
		elgg_load_js('syntaxhighlighter.xml');
		$brush = 'xml';
		break;

}
$url = elgg_normalize_url("file/download/$entity->guid");
$contents = htmlentities(file_get_contents($url));


echo '<div class="elgg-col elgg-col-1of1 clearfloat">';
echo "<pre class=\"brush: $brush\">";
echo $contents;
echo '</pre>';
echo '</div>';
