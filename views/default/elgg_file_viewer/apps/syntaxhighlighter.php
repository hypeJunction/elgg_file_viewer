<?php
$entity = elgg_extract('entity', $vars);

if (!$entity instanceof ElggFile) {
	return;
}

$info = pathinfo($entity->getFilenameOnFilestore());
$extension = $info['extension'];

elgg_load_css('prism');
elgg_require_js('elgg_file_viewer/apps/syntaxhighlighter');

switch ($extension) {
	default :
		$class = "language-non";
		break;

	case 'html':
	case 'htm':
	case 'xml' :
		$class = "language-markup";
		break;

	case 'js' :
		$class = "language-javascript";
		break;

	case 'css' :
		$class = "language-css";
		break;

	case 'php' :
		$class = "language-php";
		break;
}

$entity->open('read');
$contents = $entity->grabFile();
$entity->close();

//$contents = htmlentities($contents);
?>

<div class="elgg-col elgg-col-1of1 clearfix">
	<pre>
		<?php
		echo elgg_format_element('code', ['class' => implode(' ', [$class, 'line-numbers'])], htmlentities($contents));
		?>
	</pre>
</div>