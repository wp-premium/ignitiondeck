<div class="wrap">
	<div class="extension_header">
		<h1><?php _e('IgnitionDeck Extensions', 'idf'); ?></h1>
	</div>
	<?php
	foreach ($data as $item) {
		$title = $item->title;
		$desc = $item->short_desc;
		$link = $item->link;
		$doclink = $item->doclink;
		$thumbnail = $item->thumbnail;
		$basename = $item->basename;
		$installed = false;
		$active = false;
		$text = __('Get Plugin', 'idf');
		$plugin_path = dirname(IDF_PATH).'/'.$basename.'/'.$basename.'.php';
		if (file_exists($plugin_path)) {
			$installed = true;
			$text = __('Activate', 'idf');
			$link = '';//admin_url('/plugins.php/?idf_activate_extension='.$basename);
			if (is_plugin_active($basename.'/'.$basename.'.php')) {
				$active = true;
				$text = __('Installed', 'idf');
			}
		}
		?>
		<div class="extension">
			<div class="extension-image" style="background-image: url(<?php echo $thumbnail; ?>);"></div>
			<p class="extension-desc"><?php echo $desc; ?></p>
			<div class="extension-link">
				<button class="button <?php echo (!$active && !$installed ? 'button-primary' : 'active-installed'); ?>" <?php echo (!empty($link) ? 'onclick="location.href=\''.html_entity_decode($link).'\'"' : ''); ?> <?php echo ($active ? 'disabled="disabled"' : ''); ?> data-extension="<?php echo $basename; ?>"><?php echo $text; ?></button>
				<?php if (!empty($doclink)) { ?>
					<button class="button" onclick="window.open('<?php echo $doclink; ?>')"><?php _e('Docs', 'idf'); ?></button>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>