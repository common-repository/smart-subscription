<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if (!current_user_can('edit_posts'))
{
    wp_die(__('You do not have sufficient permissions to manage plugins for this site.'));
}

global $wpdb;
$formtable = $wpdb->prefix . "ss_drf_forms_list";

if (isset($_POST['info_update']) and wp_verify_nonce( sanitize_text_field($_POST['crf_create_form_nonces']), 'create_form_ss_drf_subsc_new')) {
	
$content = 'Thank you for subscribing.';
$failcontent = 'Something went wrong. Please try later or contact the administrator by another method.';
	// Update options

	$ss_drf_isdwnlfile = sanitize_file_name($_POST["ss_drf_isdwnlfile"]);	
	$ss_drf_dwnlfile = sanitize_text_field($_POST["ss_drf_dwnlfile"]);	
	$ss_drf_isredirect = sanitize_text_field($_POST["ss_drf_isredirect"]);	
	$ss_drf_redirecturl = esc_url($_POST["ss_drf_redirecturl"]);	
	$success_message = $_POST["success_message"] ? wp_kses_post     ($_POST["success_message"]) : $content;	
	$fail_message =  $_POST["fail_message"] ? wp_kses_post($_POST["fail_message"]) : $failcontent;
	$ss_drf_frmname = wp_kses_post($_POST["ss_drf_frmname"]);		
	$ss_drf_form_above = wp_kses_post($_POST["form_above_content"]);
	$ss_drf_form_below = wp_kses_post($_POST["form_below_content"]);
	$button_text = wp_kses_post($_POST["button_text"]);
	

$wpdb->query( $wpdb->prepare(
	"INSERT INTO $formtable (form_name, is_download, file_path, is_redirect, redirect_path, success_message, failure_message, form_above, form_below, button_text) VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )",
	array(
		$ss_drf_frmname,	
		$ss_drf_isdwnlfile,
		$ss_drf_dwnlfile,
		$ss_drf_isredirect,
		$ss_drf_redirecturl,
		$success_message,
		$fail_message,
		$ss_drf_form_above,
		$ss_drf_form_below,										
		$button_text,										
	)
));
$last_insert_id = $wpdb->insert_id;

if($last_insert_id){
	// Updated message
	echo "<div class='updated fade'><p><strong>" . __('Options updated successfully.Please wait, it will be redirecting to Edit Page') . "</strong></p></div>";
?>
<script type="text/javascript">
window.location = "<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ss_drforms";
</script>  
<?php
}
} elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
?>
<div id="message" class="error notice notice-error is-dismissible"><p>Invalid nonce.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
  
  <?php          
}

?>

		<div class="wrapinner">
		
			<div class="ss_drf">
			<a class="add-new-h2 bkp" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ss_drforms"><span>Back</span></a>
				<script type="text/javascript">
				jQuery(document).ready(function($) {
	var custom_uploader;
    $('#upload_image_button').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload File',

            // mutiple: true if you want to upload multiple files at once

            multiple: false
        }).open()
        .on('select', function(e){

            // This will return the selected image from the Media Uploader, the result is an object

            var uploaded_image = image.state().get('selection').first();

            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image

            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            console.log(image_url);

            // Let's assign the url value to the input field

            $('#ss_drf_dwnlfile').val(image_url);
        });
    });

				});
				</script>
				
				<form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ss_drform-new">	
				<?php

if (function_exists('settings_fields')) {
	settings_fields('ss_drf_settings');
} ?>		
						<?php //echo get_option('ss_drf_isdwnlfile');
 ?>

<table>
  <tr>
    <td>Form Name :</td>
    <td><input type="text" class='colorwell' name="ss_drf_frmname" value="Untitled Form" size="100" /></td>
  </tr>
  <tr>
    <td><input type="checkbox" checked="checked" class='colorwell' name="ss_drf_isdwnlfile" value="yes"> Download File</td>
  </tr>
  <tr>
  <td><input id="upload_image_button" class="button-primary" name="_unique_name_button" type="text" value="Upload File" style="width: 107px;" /></td>
    <td>
    <?php
echo "<input type='text' size='100' ";
echo "class='colorwell' ";
echo "name='ss_drf_dwnlfile' ";
echo "id='ss_drf_dwnlfile' ";
echo "placeholder='Upload File' ";
echo "value='' />\n";
?>
    </td>
  </tr>
  <tr>
    <td colspan="3"><input type="checkbox" checked="checked" class='colorwell' name="ss_drf_isredirect" value="yes"> Redirect Page</td>
  </tr>
    <tr>
    <td>Redirect Page Url</td>
    <td colspan="2"><input type="text" size='100' class='colorwell' placeholder='Redirect Page URL' name="ss_drf_redirecturl" value=""></td>    
  </tr>
  <tr>
  <td colspan="3"><p style="margin: 20px 0px 0px 0px;font-weight: bold;">Success Message : </p></td>  
  </tr>
<tr>
  <td colspan="3">
<?php
$content = 'Thank you for subscribing.';
$id = 'successmsg';
$settings = array(
	'textarea_name' => "success_message",
	'media_buttons' => false,
	'textarea_rows' => '7',
	'wpautop' => true,
	'tinymce' => array( 
            'content_css' => plugin_dir_url( __FILE__ ) . 'editor-styles.css' 
       )
);
wp_editor($content, $id, $settings); ?>
  </td>
</tr>
  <tr>
  <td colspan="3"><p style="margin: 20px 0px 0px 0px;font-weight: bold;">Failure Message : </p></td>  
  </tr>
<tr>
  <td colspan="3">
	<?php
$failcontent = 'Something went wrong. Please try later or contact the administrator by another method.';
$failid = 'failmsg';
$failsettings = array(
	'textarea_name' => "fail_message",
	'media_buttons' => false,
	'textarea_rows' => '7',
	'wpautop' => true
);
wp_editor($failcontent, $failid, $failsettings); ?>
  </td>
</tr>

  <tr>
  <td colspan="3"><p style="margin: 20px 0px 0px 0px;font-weight: bold;">Form Above Content : </p></td>  
  </tr>
<tr>
  <td colspan="3">
	<?php
$form_above_content = '';
$frmabvid = 'frmabvcnt';
$frmabvsettings = array(
	'textarea_name' => "form_above_content",
	'media_buttons' => false,
	'textarea_rows' => '7',
	'wpautop' => true
);
wp_editor($form_above_content, $frmabvid, $frmabvsettings); ?>
  </td>
</tr>

  <tr>
  <td colspan="3"><p style="margin: 20px 0px 0px 0px;font-weight: bold;">Form Below Content : </p></td>  
  </tr>
<tr>
  <td colspan="3">
	<?php
$form_below_content = '';
$frmblwid = 'frmblwcnt';
$frmblwsettings = array(
	'textarea_name' => "form_below_content",
	'media_buttons' => false,
	'textarea_rows' => '7',
	'wpautop' => true
);
wp_editor($form_below_content, $frmblwid, $frmblwsettings); ?>
  </td>
</tr>
<tr>
    <td colspan="3"><p style="margin: 20px 0px 0px 0px;font-weight: bold;">Button Text : </p></td>
    </tr>
    <tr>
    <td colspan="2"><input type="text" class='colorwell' name="button_text" value="" size="100" /></td>
  </tr>
</table>																				
					<p class="submit">
						<?php  wp_nonce_field( 'create_form_ss_drf_subsc_new' ,'crf_create_form_nonces', false); ?>
						<input type='submit'  id="gobutton" class="button-primary" name='info_update' value='Add Form' />
					</p>					
				</form>
					
				
			</div><?php
 ?>
			
		</div>
