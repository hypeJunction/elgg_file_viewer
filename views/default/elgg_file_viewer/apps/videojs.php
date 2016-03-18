<?php
$entity = elgg_extract('entity', $vars);

if (!$entity instanceof ElggFile) {
	return;
}

$attr = array(
	'class' => 'video-js',
	'width' => 640,
	'id' => "video-player-$entity->guid",
	'title' => $entity->getDisplayName(),
	'controls' => true,
	'preload' => 'metadata',
	'autoplay' => false,
);

$entity_mime = elgg_file_viewer_get_mime_type($entity);
list($base_type, $ext) = explode('/', $entity_mime);

switch ($base_type) {

	default :
	case 'video' :
		$tag = 'video';
		$mimes = array_unique([
			$entity_mime,
			'video/mp4',
			'video/webm',
			'video/ogv',
		]);
		$attr['poster'] = ($entity->icontime) ? $entity->getIconURL('master') : elgg_get_simplecache_url('elgg_file_viewer/video.jpg');
		break;

	case 'audio' :
		$tag = 'audio';
		$mimes = array_unique([
			$entity_mime,
			'audio/mpeg',
			'audio/ogg',
			'video/wav',
		]);
		$attr['poster'] = ($entity->icontime) ? $entity->getIconURL('master') : elgg_get_simplecache_url('elgg_file_viewer/audio.jpg');
		break;
}

$sources = '';
foreach ($mimes as $mime) {
	if ($mime == $entity_mime) {
		$url = elgg_get_download_url($entity);
	} else {
		list(, $ext) = explode('/', $mime);
		$url = elgg_file_viewer_get_media_url($entity, $ext);
	}
	if ($url) {
		$sources .= elgg_format_element('source', [
			'src' => $url,
			'type' => $mime,
		]);
	}
}

elgg_load_css('videojs');
?>
<div class="elgg-col elgg-col-1of1 clearfix">
	<?php
	echo elgg_format_element($tag, $attr, $sources);
	?>
</div>
<script>
	require(['elgg_file_viewer/apps/videojs'], function (videojs) {
		videojs('<?php echo "video-player-$entity->guid" ?>');
	});
</script>