<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$mime = elgg_file_viewer_get_mime_type($entity);

$url = elgg_normalize_url("file/download/$entity->guid/$entity->originalfilename");

echo '<div class="elgg-col elgg-col-1of1 clearfloat">';

switch ($mime) {
	case 'video/mp4' :
	case 'video/webm' :
	case 'video/ogg' :
	case 'audio/ogg' :
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
		break;

	default:
		echo <<<__HTML
<object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="640" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
    <param name="src" value="$url"/>
	<param name="autoplay" value="false">
    <embed type="$mime" src="$url" width="640" pluginspage="http://go.divx.com/plugin/download/"></embed>
</object>
__HTML;
		break;
}

echo '</div>';
