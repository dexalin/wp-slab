<?php
/**
* @package WP slabText
*/
?>
<div class="wrap">
	
	<?php screen_icon('options-general'); ?>
	<h2><?php _e('WP slabText','wp-slabText'); ?></h2>
	<h3><?php _e('Settings','wp-slabText'); ?></h3>
	
	<form action="" method="post">
		<table class="form-table">
			<tr>
				<th><label for="enable"><?php _e('Activate "slabText"','wp-slabText'); ?></label></th>
				<td><input type="checkbox" name="enable" value="yes" <?php echo $checked['enable']; ?> /></td>
			</tr>
			<tr>
				<th><label for="load_css"><?php _e('Load default CSS file','wp-slabText'); ?></label></th>
				<td><input type="checkbox" name="load_css" value="yes" <?php echo $checked['load_css']; ?> />
					<p><?php _e('If you don\'t check, you need to add appropriate styles that is described <a href="http://www.frequency-decoder.com/demo/slabText/" title="slabText" target="_blank">here</a> in your theme CSS file. Otherwise "slabText" doesn\'t  work!','wp-slabText'); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="font"><?php _e('Font family','wp-slabText'); ?></label></th>
				<td><input type="text" id="font_family" name="font_family" value="<?php echo esc_html($value['font_family']); ?>" size="30" />
				<input type="hidden" id="font_family_temp" name="font_family" value="<?php echo esc_html($value['font_family']); ?>" disabled="disabled" /><br/>
					<label for="load_webfont"><input type="checkbox" id="load_webfont" name="load_webfont" value="yes" <?php echo $checked['load_webfont']; ?> /> <?php _e('Use Google Web Fonts','wp-slabText'); ?></label>
					<!-- <p>( <?php _e('If you want to use Google Web Fonts, check this option.','wp-slabText'); ?> )</p> -->
					<!-- Google Web Fonts -->
					<div id="google_webf">
						<?php _e('API Key','wp-slabText'); ?> :<br/>
						<input type="text" id="webf_apikey" name="webf_apikey" value="<?php echo esc_html($value['webf_apikey']); ?>" size="40" /> <a href="https://code.google.com/apis/console" title="Google APIs console" target="_blank" ><?php _e('Get your key','wp-slabText'); ?> &raquo;</a>
						<input type="hidden" id="webf_apikey_temp" name="webf_apikey" value="<?php echo esc_html($value['webf_apikey']); ?>" disabled="disabled" />
						<div id="webf_styles">
						</div>
						<div id="webf_others">
							<input type="text" id="webf_other_family" name="webf_other_family" value="<?php echo esc_html($value['webf_other_family']); ?>" size="20" />
							<input type="text" id="webf_other_variants" name="webf_other_variants" value="<?php echo esc_html($value['webf_other_variants']); ?>" size="20" />
							<input type="text" id="webf_other_subsets" name="webf_other_subsets" value="<?php echo esc_html($value['webf_other_subsets']); ?>" size="20" />
							&nbsp;<a href="http://www.google.com/webfonts" title="Google Web Fonts" target="_blank">Full font list &raquo;</a>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th><label for="parents_class"><?php _e('Classname of parent element','wp-slabText'); ?></label></th>
				<td><input type="text" name="parents_class" value="<?php echo esc_html($value['parents_class']); ?>" size="30" /></td>
			</tr>
			<tr>
				<th><label for="which[]"><?php _e('Target pages','wp-slabText'); ?></label></th>
				<td>
					<input type="checkbox" name="which[]" value="front" <?php echo $checked['which_front']; ?> /> <?php _e('Front page','wp-slabText'); ?><br />
					<input type="checkbox" name="which[]" value="archives" <?php echo $checked['which_archives']; ?> /> <?php _e('Archives page','wp-slabText'); ?><br />
					<input type="checkbox" name="which[]" value="search" <?php echo $checked['which_search']; ?> /> <?php _e('Search page','wp-slabText'); ?><br />
					<input type="checkbox" name="which[]" value="contents" <?php echo $checked['which_contents']; ?> /> <?php _e('Contents page','wp-slabText'); ?><br />
				</td>
			</tr>
			<tr>
				<th><label for="delete"><?php _e('Delete settings','wp-slabText'); ?></label></th>
				<td>
					<input type="checkbox" name="delete" value="yes" />
					<p><?php _e('Delete all settings data from your database. Check this option if you will totally stop use this plugin.','wp-slabText'); ?></p>
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="wpstxt_action" value="<?php _e('Update','wp-slabText'); ?>" class="button-primary" /></p>
	</form>
</div>