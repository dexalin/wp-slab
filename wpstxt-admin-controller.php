<?php
/**
* @package WP slabText
*/

$ndata = $checked = array();

if($_POST['wpstxt_action'] == __('Update','wp-slabText')){
		
	$ndata = array(
		'enable'	=> $_POST['enable'],
		'load_css'	=> $_POST['load_css'],
		'font_family'	=> sanitize_text_field($_POST['font_family']),
		'load_webfont'	=> $_POST['load_webfont'],
		'webf_apikey'	=> sanitize_text_field($_POST['webf_apikey']),
		'webf_family'	=> sanitize_text_field($_POST['webf_family']),
		'webf_variants'	=> sanitize_text_field($_POST['webf_variants']),
		'webf_subsets'	=> sanitize_text_field($_POST['webf_subsets']),
		'webf_other_family'	=> sanitize_text_field($_POST['webf_other_family']),
		'webf_other_variants'	=> sanitize_text_field($_POST['webf_other_variants']),
		'webf_other_subsets'	=> sanitize_text_field($_POST['webf_other_subsets']),
		'parents_class'	=> sanitize_html_class($_POST['parents_class'],'entry-title'),
		'which'		=> $_POST['which']
	);
	
	// Save, update or delete the data.
	if( $ndata == array() || ( isset($_POST['delete']) && $_POST['delete'] == 'yes') ){
		
		delete_option( 'wpstxt_settings' );
	
	} else {
		
		if( get_option( 'wpstxt_settings' ) == false ){
			add_option( 'wpstxt_settings' , $ndata );
		} else {
			update_option( 'wpstxt_settings' , $ndata );
		}
	}
}

// Set form values that reflects actual data in DB.
$sdata = wpstxt_get_all_options();

if($sdata){
	foreach($sdata as $key => $val){
		if($key == 'which'){
			foreach($val as $k => $v){
				$checked['which_'.$v] = 'checked="checked"';
			}
		} elseif($key == 'parents_class'){		
			$value['parents_class'] = $val;	
		} elseif($key == 'webf_apikey'){		
			$value['webf_apikey'] = $val;
		} elseif($key == 'font_family'){		
			$value['font_family'] = $val;	
		} elseif($key == 'webf_family'){		
			$selected['webf_family'] = $val;
		} elseif($key == 'webf_variants'){		
			$selected['webf_variants'] = $val;
		} elseif($key == 'webf_subsets'){		
			$selected['webf_subsets'] = $val;	
		} elseif($key == 'webf_other_family'){		
			$value['webf_other_family'] = $val;
		} elseif($key == 'webf_other_variants'){		
			$value['webf_other_variants'] = $val;
		} elseif($key == 'webf_other_subsets'){		
			$value['webf_other_subsets'] = $val;	
		} else {
			if($val == 'yes'){
				$checked[$key] = 'checked="checked"';
			}
		}
	}
}
?>
<script type="text/javascript">

jQuery(document).ready(function($){
	
	if( $("#load_webfont").is( ":checked" ) == true ){
		$("#font_family").val("").attr( "disabled" , true );
		$("#font_family_temp").attr( "disabled" , false );
		$("#google_webf").removeAttr( "style" );
		$("#webf_apikey").attr( "disabled" , false );
		$("#webf_apikey_temp").attr( "disabled" , true );
		if('<?php echo $value['webf_apikey']; ?>' != '' ){
			ajax_get_fontlist_from_google();
		}
		if('<?php echo $selected['webf_family']; ?>' == 'other' ){
			$('#webf_others').removeAttr('style');
		}else{
			$('#webf_others').attr('style','display:none');
		}
	} else {
		$("#font_family").attr( "disabled" , false );
		$("#font_family_temp").attr( "disabled" , true );
		$("#google_webf").attr( "style" , "display:none" );
		$("#webf_apikey").attr( "disabled" , true );
		$("#webf_apikey_temp").attr( "disabled" , false );
	}
	
	$("#load_webfont").click(function(){
		if( $("#load_webfont").is( ":checked" ) == true ){
			var font_family_temp = $("#font_family").val();
			$("#font_family_temp")
				.attr( "disabled" , false )
				.val(font_family_temp);
			$("#font_family")
				.val("")
				.attr( "disabled" , true );
			$("#google_webf").removeAttr( "style" );
			if('<?php echo $selected['webf_family']; ?>' == 'other'||$("#webf_family").val() =='other'){
				$('#webf_others').removeAttr('style');
			}else{
				$('#webf_others').attr('style','display:none');
			}
			var webf_apikey_temp = $("#webf_apikey_temp").val();
			$("#webf_apikey")
				.attr( "disabled" , false )
				.val(webf_apikey_temp);
			$("#webf_apikey_temp")
				.val("")
				.attr( "disabled" , true );
			if('<?php echo $value['webf_apikey']; ?>' != '' ){
				ajax_get_fontlist_from_google();
			}
		} else {
			var font_family_temp = $("#font_family_temp").val();
			$("#font_family")
				.attr( "disabled" , false )
				.val(font_family_temp);
			$("#font_family_temp")
				.val("")
				.attr( "disabled" , true );
			$("#font_family_temp").val("");
			$("#google_webf").attr( "style" , "display:none" );
			var webf_apikey_temp = $("#webf_apikey").val();
			$("#webf_apikey_temp")
				.attr( "disabled" , false )
				.val(webf_apikey_temp);
			$("#webf_apikey")
				.val("")
				.attr( "disabled" , true );
		}
	});
});

<?php
/**
* Get the most popular web font list by Google Web Fonts API.
*/
?>
function ajax_get_fontlist_from_google(){
	
	// If the element already exists, doing nothing.
	if(jQuery("#webf_family").length)
		return false;
	
	var div_id = jQuery('#webf_styles');
	
	var apiUrl = [];
	apiUrl.push('https://www.googleapis.com/webfonts/v1/webfonts');
	apiUrl.push('?sort=popularity');
	apiUrl.push('&key=<?php echo esc_html($value['webf_apikey']); ?>');
	var json_url = apiUrl.join('');
	
	div_id.append('<span class="loading">Loading...</span>');
	
	jQuery.getJSON(json_url,function(json){
		if(json){
			
			var items = json['items'];
			var sel_fam = '<?php _e('Popular fonts','wp-slabText'); ?> :<br/>';
			sel_fam += '<select id="webf_family" name="webf_family" onChange="create_subitems_select()">';
			
			jQuery.each(items,function(cnt){
				
				var selected = '';
				if(this.family == '<?php echo $selected['webf_family']; ?>'){
					selected = 'selected="selected"';
				}
				
				sel_fam += '<option value="'+ this.family +'" variants="'+this.variants+'" subsets="'+this.subsets+'" '+selected+'>'+this.family+'</option>';
				
				cnt++;
				if(cnt === 30){
					if('other' == '<?php echo $selected['webf_family']; ?>'){
						selected = 'selected="selected"';
					}
					sel_fam += '<option value="other"'+selected+'><?php echo _e("Other","wp-slabText"); ?>...</option>';
					return false;
				}
			});
			
			sel_fam += '</select>';
			div_id.append(sel_fam);
			
			// Create submenus
			create_subitems_select();
			
			div_id.children('.loading').remove();
		}else{
			alert('test');
			if(div_id.children().is('.loading')){
				div_id.children('.loading').remove();
				div_id.append('<span style="color:red">ERROR_01</span>');
			}
		}
	});
	
	div_id.ajaxComplete(function(){
		if(div_id.children().is('.loading')){
			div_id.children('.loading').remove();
			div_id.append('<span style="color:red">ERROR_02</span>');
		}
	});
}

<?php
/**
* Create a submenu list that depends on the value selected by user on mainmenu.
*/
?>
function create_subitems_select(){
	
	var div_id = jQuery('#webf_styles');
	
	// Delete previous objects before create new object.
	jQuery('#webf_variants').remove();
	jQuery('#webf_subsets').remove();
	
	
	var sel_value = jQuery('#webf_family option:selected').attr('value');
	
	// If 'other' is selected, create text fields.
	if(sel_value == 'other'){
		
		// If the element already exists, show them up.
		if( 0 < jQuery("#webf_others").length){
			jQuery('#webf_others').removeAttr('style');
		}
	
	// If one of Google Web Font is selected, create submenu.
	}else{
		
		if( 0 < jQuery("#webf_others").length){
			jQuery('#webf_others').attr('style','display:none');
		}
		
		// Create new Variants object.
		var variants = jQuery('#webf_family option:selected').attr('variants');
		var vari = jQuery(variants.split(','));
		var sel_vali = '<select id="webf_variants" name="webf_variants">';		
		
		jQuery.each(vari,function(){
			
			var selected = '';
			if(this == '<?php echo $selected['webf_variants']; ?>'){
				selected = 'selected="selected"';
			}
			
			sel_vali += '<option value="'+this+'" '+selected+'>'+this+'</option>';
		});
		sel_vali += '</select>';
		div_id.append(sel_vali);
		
		// Create new Subsets object.
		var subsets = jQuery('#webf_family option:selected').attr('subsets');
		var subs = jQuery(subsets.split(','));
		var sel_subs = '<select id="webf_subsets" name="webf_subsets">';		
		
		jQuery.each(subs,function(){
			
			var selected = '';
			if(this == '<?php echo $selected['webf_subsets']; ?>'){
				selected = 'selected="selected"';
			}
			
			sel_subs += '<option value="'+this+'" '+selected+'>'+this+'</option>';
		});
		sel_subs += '</select>';
		div_id.append(sel_subs);
		//div_id.append('</div>');
	}
}
</script>

<?php
/**
* Styles for Admin.
*/
?>
<style type="text/css">
#google_webf,
#webf_styles,
#webf_others {
	margin: 10px 0;
}
</style>