<?php

$guid = get_input('guid');
$file = get_entity($guid);

if (!$file) {
	return;
}

$mime = elgg_file_viewer_get_mime_type($file);

$filename = $file->originalfilename;

header("Pragma: public");
header("Content-type: $mime");
header("Content-Disposition: attachment; filename=\"$filename\"");

ob_clean();
flush();
readfile($file->getFilenameOnFilestore());