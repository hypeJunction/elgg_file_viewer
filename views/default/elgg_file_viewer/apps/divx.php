<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'object', 'file')) {
	return;
}

$mime = elgg_file_viewer_get_mime_type($entity);

$url = elgg_normalize_url("file/download/$entity->guid/$entity->originalfilename");

echo '<div class="elgg-col elgg-col-1of1 clearfloat">';

echo <<<__HTML
<object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="640" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
    <param name="src" value="$url"/>
	<param name="autoplay" value="false">
    <embed type="$mime" src="$url" width="640" pluginspage="http://go.divx.com/plugin/download/"></embed>
</object>
__HTML;

echo '</div>';
