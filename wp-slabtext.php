<?php
/*
Plugin Name: TNW slabText for WP
Description: Allows for true fluid type with headlines. Uses jQuery plugin slabText created by Brian McAllister.
Version: 1.0
Author: -A, The Next Wave
Author URI: https://thenextwave.biz
License: Creative Commons Attribution 4.0 International License
*/

/*
"slabText" - a jQuery plugin that is included in this plugin,
was created by Brian McAllister.
http://freqdec.github.io/slabText/
*/

/*********************************************
* Load language file.
**********************************************/
function wpstxt_load_lang(){
	// Load a language file that match for the site's setting.
	load_plugin_textdomain('wp-slabText',false,basename( dirname( __FILE__ ) ) . '/languages');
}
add_action('plugins_loaded','wpstxt_load_lang');

/*********************************************
* Setup the settings pages.
**********************************************/
// Create plugin's management page.
function wpstxt_admin(){
	add_submenu_page('options-general.php','slabText','slabText','administrator',__FILE__ ,'wpstxt_load_settings');
}
add_action('admin_menu','wpstxt_admin');

function wpstxt_load_settings(){
	require_once('wpstxt-admin-controller.php');
	require_once('wpstxt-admin-viewer.php');
}

/*********************************************
* Decide pages the JS/CSS files should be loaded.
**********************************************/
function wpstxt_load_cond(){
	$enable = null;
	$which = array();

	// Get settings data.
	$enable = wpstxt_get_option('enable');

	if($enable == "yes"){

		// Get settings data.
		$which = wpstxt_get_option('which');

		if(in_array('contents',$which)){
			if(is_singular()||is_page()||is_single()){
				wpstxt_load_js();
			}
		}
		if(in_array('front',$which)){
			if(is_front_page()||is_home()){
				wpstxt_load_js();
			}
		}
		if(in_array('archives',$which)){
			if(is_archive()){
				wpstxt_load_js();
			}
		}
		if(in_array('search',$which)){
			if(is_search()){
				wpstxt_load_js();
			}
		}
	}
}
add_action('wp_enqueue_scripts','wpstxt_load_cond');

/*********************************************
* Load the JS/CSS files in head section.
**********************************************/
function wpstxt_load_js(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('slabtext',plugins_url('/slabText/js/jquery.slabtext.min.js', __FILE__),array('jquery'));

	$load_css = wpstxt_get_option('load_css');
	if($load_css == "yes"){
		wp_register_style('wpstxt', plugins_url('/slabText/css/slabtext.css', __FILE__));
		wp_enqueue_style('wpstxt');
	}

	$load_webfont = wpstxt_get_option('load_webfont');
	if($load_webfont == "yes"){

		$webf_family = wpstxt_get_option('webf_family');

		if($webf_family == 'other'){

			$webf_family	= wpstxt_get_option('webf_other_family');
			if(empty($webf_family)){
				$webf_family = 'Open Sans';
			}
			$webf_variants	= wpstxt_get_option('webf_other_variants');
			$webf_subsets	= wpstxt_get_option('webf_other_subsets');

		}elseif($webf_family == ''){

			$webf_family = 'Open Sans';

		}else{

			$webf_variants	= wpstxt_get_option('webf_variants');
			$webf_subsets	= wpstxt_get_option('webf_subsets');
		}

		// Replace spaces to '+' for the API.
		$webf_family = preg_replace('/\s+/','+',$webf_family);

		if(!empty($webf_variants)){
			$webf_variants = ':'.$webf_variants;
		}

		if(!empty($webf_subsets)){
			$webf_subsets = '&subset='.$webf_subsets;
		}

		echo '<link rel="stylesheet" id="wpstxt_webf-css" href="http://fonts.googleapis.com/css?family='.esc_html($webf_family.$webf_variants.$webf_subsets).'" type="text/css" media="all" />';
		// Need to avoid the urlencode_deep function :-(
		//wp_register_style('wpstxt_webf','http://fonts.googleapis.com/css?family='.$webf_family.$webf_variants.$webf_subsets);
		//wp_enqueue_style('wpstxt_webf');
	}
}

/*********************************************
* Get the all settings data of 'WP slabText'.
**********************************************/
function wpstxt_get_all_options(){
	$wpstxt_values = null;

	// Get the newest data for the Viewer/Editor.
	$c_data = get_option('wpstxt_settings');

	if($c_data != false){
		foreach($c_data as $key => $value){
			if(is_array($value)){
				foreach($value as $k => $v){
						$wpstxt_values[$key][$k] = $v;
				}
			}else{
				$wpstxt_values[$key] = $value;
			}
		}
	}
	return $wpstxt_values;
}

/*********************************************
* Get a single settings data of 'WP slabText' by key.
**********************************************/
function wpstxt_get_option($tkey = null){
	$wpstxt_value = null;

	$c_data = get_option('wpstxt_settings');

	if($c_data != false){
		foreach($c_data as $key => $value){
			if($key == $tkey){
				if(is_array($value)){
					foreach($value as $k => $v){
						$wpstxt_value[] = $v;
					}
				}else{
					$wpstxt_value = $value;
				}
			}
		}
	}
	return $wpstxt_value;
}

/*********************************************
* Outputs the code for executing 'WP slabText'.
**********************************************/
function wpstxt_call(){
	$cond_flag = false;

	$enable = wpstxt_get_option('enable');
	$which = wpstxt_get_option('which');
	if($enable =='yes' && (
		(in_array('front',$which) && (is_front_page()||is_home()))
		||
		(in_array('archives',$which) && is_archive())
		||
		(in_array('search',$which) && is_search())
		||
		(in_array('contents',$which) && (is_singular()||is_page()||is_single()))
	)){
		$cond_flag = true;
	}

	if($cond_flag == true){

		$parents_class = wpstxt_get_option('parents_class');

		$webf_script  = "\n<script type='text/javascript'>";
		$webf_script .= "jQuery(document).ready(function($){";
		$webf_script .= "$('.".esc_html($parents_class)."').slabText();";
		// If the plugin is deactivated in the post, detach all slabText staffs in the post.
		$webf_script .= "$('.no-wpstxt')";
		$webf_script .= 	".find('.slabtextdone').removeClass('slabtextdone')";
		$webf_script .= 	".find('.slabtext').removeClass('slabtext').removeAttr('style');";
		$webf_script .= "});";
		$webf_script .= "</script>\n";

		echo $webf_script;

		$webf_vari_char = $webf_vari_digi = array();

		// Use Google Web Fonts.
		$load_webfont = wpstxt_get_option('load_webfont');
		if($load_webfont == "yes"){

			$font_family = wpstxt_get_option('webf_family');

			if($font_family == 'other'){
				$font_family	= wpstxt_get_option('webf_other_family');
				if(empty($font_family)){
					$font_family = 'Open Sans';
				}
				$font_variants	= wpstxt_get_option('webf_other_variants');
			}elseif(empty($font_family)){
				$font_family = 'Open Sans';
			}else{
				$font_variants	= wpstxt_get_option('webf_variants');
			}

			// Split 'Variants' into font-weight and font-style. ex) '700italic' => '700','italic'.
			if(!empty($font_variants)){
				$webf_vari_char = preg_split('/[0-9]+/',$font_variants,2);
				$webf_vari_digi = preg_split('/[a-z]+/',$font_variants,2);
			}

		}else{

			$font_family = wpstxt_get_option('font_family');

		}

		// Print CSS style in <head> tag of header template.
		$font_style  = '<style type="text/css">.wpstxt .'.esc_html($parents_class).'{';
		$font_style .= 'font-family:"'.esc_html($font_family).'";';
		foreach($webf_vari_digi as $vari_d){
			if($vari_d != ''){
				$font_style .= 'font-weight:'.esc_html($vari_d).';';
			}
		}
		foreach($webf_vari_char as $vari_c){
			if($vari_c != ''){
				$font_style .= 'font-style:'.esc_html($vari_c).';';
			}
		}
		$font_style .= '}</style>';

		echo $font_style;
	}
}
add_action('wp_head','wpstxt_call',999);

/*********************************************
* Insert span tags to each of pieces that has divided with double-pipe in the title.
**********************************************/
function wpstxt_get_segmented($title = null){
	global $post;
	$cond_flag = false;
	$rtxt = null;

	$use_wpstxt = get_post_meta($post->ID,'use_wpstxt',true);
	if($use_wpstxt == 'yes'){

		$which = wpstxt_get_option('which');
		if(
			(in_array('front',$which) && (is_front_page()||is_home()))
			||
			(in_array('archives',$which) && is_archive())
			||
			(in_array('search',$which) && is_search())
			||
			(in_array('contents',$which) && (is_singular()||is_page()||is_single()))
			&&
			in_the_loop()
		){
			$cond_flag = true;
		}
	}

	if($cond_flag == true){

		$divide_dpipe = get_post_meta($post->ID,'divide_dpipe',true);

		if($divide_dpipe == 'yes'){
			// Divide the title with double-pipe.
			$sep_title = preg_split("/\|\|/",$title);

			// Wrap each of pieces with <span> tag, and also puts unique class for advanced styling.
			$cnt = 1;
			foreach($sep_title as $st){
				$rtxt .= '<span class="slabtext wpstxt-'.$cnt.'">';
				$rtxt .= $st;
				$rtxt .= '</span>';
				$cnt++;
			}
		}else{
			// Remove double-pipe from posts which create sidebars from within the loop when divide_dpipe is not in use. (for example, in page builders)
			$rtxt  = preg_replace('/\|\|/','',$title);
		}


	}else{
		// Remove double-pipe from the post's title when the plugin is deactivated.
		$rtxt  = preg_replace('/\|\|/','',$title);
	}

	return $rtxt;
}
add_filter('the_title','wpstxt_get_segmented');

/*********************************************
* Put 'no-wpstxt' class in the parent tag of the post's title if the plugin is disabled.
**********************************************/
function wpstxt_get_disabled($classes = array()){
	global $post;
	$use_wpstxt = null;

	$use_wpstxt = get_post_meta($post->ID,'use_wpstxt',true);

	if($use_wpstxt == 'yes'){
		$class = 'wpstxt';
	}else{
		$class = 'no-wpstxt';
	}
	$classes[] = $class;

	return $classes;
}
add_filter('post_class','wpstxt_get_disabled');

/*********************************************
* Delete the handle characters from the title tag that is used outside of the loop.
**********************************************/
function wpstxt_delete_chars($title = null){

	$rtxt = null;

	// Delete double-pipe from title.
	$rtxt = preg_replace('/\|\|/','',$title);

	return $rtxt;
}
add_filter('single_post_title','wpstxt_delete_chars');

/*********************************************
* Add plugins option to the editor in the admin.
**********************************************/
// Set the box.
function wpstxt_add_option(){
	add_meta_box('slabText',__('slabText','wp-slabText'),'wpstxt_option_form','post','side');
	add_meta_box('slabText',__('slabText','wp-slabText'),'wpstxt_option_form','page','side');
	add_meta_box('slabText',__('slabText','wp-slabText'),'wpstxt_option_form','project','side');

}
add_action('admin_menu', 'wpstxt_add_option');

// Create the form contents.
function wpstxt_option_form($post){
	$checked = array();

	$use_wpstxt = get_post_meta($post->ID,'use_wpstxt',true);
	$divide_dpipe = get_post_meta($post->ID,'divide_dpipe',true);
	if($use_wpstxt == 'yes'){
		$checked['use_wpstxt'] = 'checked="checked"';
	}
	if($divide_dpipe == 'yes'){
		$checked['divide_dpipe'] = 'checked="checked"';
	}

	$wpstxt_form  = '<input type="checkbox" name="use_wpstxt" value="yes" size="25" ' .$checked['use_wpstxt'].'/>&nbsp;';
	$wpstxt_form .= '<label for="use_wpstxt">'.__("Activate",'wp-slabText').'</label><br/>';
	$wpstxt_form .= '<input type="checkbox" name="divide_dpipe" value="yes" size="25" ' .$checked['divide_dpipe'].'/>&nbsp;';
	$wpstxt_form .= '<label for="divide_dpipe">'.__("Use ||(double-pipe) for dividing the title",'wp-slabText').'</label>';
	$wpstxt_form .= '<input type="hidden" name="wpstxt_nonce" id="wpstxt_nonce" value="'.wp_create_nonce( plugin_basename(__FILE__)).'"/>';

	echo $wpstxt_form;
}

// Save,update or delete the data.
function wpstxt_save_postdata($post_ID){

	if(!wp_verify_nonce($_POST['wpstxt_nonce'],plugin_basename(__FILE__)))
		return $post_ID;

	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_ID;

	if('page' == $_POST['post_type']){
    	if(!current_user_can('edit_page',$post_ID))
	    	return $post_ID;
	}else{
	    if(!current_user_can('edit_post',$post_ID))
	    	return $post_ID;
	}

	$pmdata = array();

	$pmdata['use_wpstxt'] = $_POST['use_wpstxt'];
	$pmdata['divide_dpipe'] = $_POST['divide_dpipe'];

	foreach($pmdata as $k => $v){
		if(isset($k) && $v == 'yes'){
			if(get_post_meta($post_ID,$k,true) == false){
				add_post_meta($post_ID,$k,$v,true);
			}else{
				update_post_meta($post_ID,$k,$v);
			}
		}else{
			delete_post_meta($post_ID,$k);
		}
	}

}
add_action('save_post','wpstxt_save_postdata');
