<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$info = pathinfo($entity->getFilenameOnFilestore());
$extension = $info['extension'];

elgg_load_js('syntaxhighlighter');
elgg_load_js('elgg.syntaxhighlighter');
elgg_load_css('syntaxhighlighter.core');
elgg_load_css('syntaxhighlighter.theme');

switch ($extension) {
	default :
		elgg_load_js('syntaxhighlighter.plain');
		$brush = 'plain';
		break;

	case 'js' :
		elgg_load_js('syntaxhighlighter.js');
		$brush = 'js';
		break;

	case 'css' :
		elgg_load_js('syntaxhighlighter.css');
		$brush = 'css';
		break;

	case 'htm' :
	case 'html' :
	case 'xml' :
		elgg_load_js('syntaxhighlighter.xml');
		$brush = 'xml';
		break;

	case 'php' :
		elgg_load_js('syntaxhighlighter.php');
		$brush = 'php';
		break;

}

$url = elgg_normalize_url("file/download/$entity->guid");
$contents = htmlentities(file_get_contents($url));

echo '<div class="elgg-col elgg-col-1of1 clearfloat">';
echo "<pre class=\"brush: $brush\">";
echo $contents;
echo '</pre>';
echo '</div>';