<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!current_user_can('edit_posts'))
{
    wp_die(__('You do not have sufficient permissions to manage plugins for this site.'));
}
        global $wpdb;
        $formtable = $wpdb->prefix . "ss_drf_forms_list";
        

        if (isset($_POST['info_update']) and wp_verify_nonce( sanitize_text_field($_POST['crf_create_form_nonces']), 'create_form_ss_drf_subsc'.intval($_POST['formid'])) ) {

                $content = 'Thank you for subscribing.';
                $failcontent = 'Something went wrong. Please try later or contact the administrator by another method.';
	        // Update options
	        //'create_form_ss_drf_subsc'. $frmid
	        $frmid = intval($_POST["formid"]);
	        $ss_drf_isdwnlfile   = sanitize_file_name($_POST["ss_drf_isdwnlfile"]);
	        $ss_drf_dwnlfile = sanitize_text_field($_POST["ss_drf_dwnlfile"]);
	        $ss_drf_isredirect  = sanitize_text_field($_POST["ss_drf_isredirect"]);
	        $ss_drf_redirecturl = esc_url($_POST["ss_drf_redirecturl"]);
	        $success_message = $_POST["success_message"] ? wp_kses_post($_POST["success_message"]) : $content;	
	        $fail_message =  $_POST["fail_message"] ? wp_kses_post($_POST["fail_message"]) : $failcontent;	
	        $ss_drf_frmname  = wp_kses_post($_POST["ss_drf_frmname"]);	
	        $ss_drf_frmabv   = wp_kses_post($_POST["form_above_content"]);	
	        $ss_drf_frmblw   = wp_kses_post($_POST["form_below_content"]);
	        $button_text  = sanitize_text_field($_POST["button_text"]);	

                $wpdb->update( 
	                $formtable, 
	                array( 
		                'form_name' => $ss_drf_frmname,
		                'is_download' => $ss_drf_isdwnlfile,
		                'file_path' => $ss_drf_dwnlfile, 
		                'is_redirect' => $ss_drf_isredirect,
		                'redirect_path' => $ss_drf_redirecturl,
		                'success_message' => $success_message,
		                'failure_message' => $fail_message,
		                'form_above' => $ss_drf_frmabv,
		                'form_below' => $ss_drf_frmblw,					
		                'button_text' => $button_text,					
	                ), 
	                array( 'id' => $frmid ), 
	                array( 
		                '%s',
		                '%s',
		                '%s',
		                '%s',
		                '%s',
		                '%s',
		                '%s',		
		                '%s',	
		                '%s',	
		                '%s',	
	                ), 
	                array( '%d' ) 
                );
	        // Give an updated message
	        echo "<div class='updated fade'><p><strong>" . __('Options updated successfully') . "</strong></p></div>";
     } else {
               ?>
               
               <div id="message" class="error notice notice-error is-dismissible"><p>Security Issues.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
               <?php
     }   

     $query = "SELECT *  FROM $formtable where id=".intval($_GET['formid']);
     $frmres = $wpdb->get_results($query);
     if($wpdb->num_rows){
        foreach( $frmres as $key => $row){
        
                $curform_id = $row->id;
                $curform_is_down =  $row->is_download;
                $curform_file_path =  $row->file_path;
                $curform_is_redir =  $row->is_redirect;
                $curform_redir_path =  $row->redirect_path;
                $curform_successmsg =  $row->success_message;
                $curform_failmsg =  $row->failure_message;
                $curform_name =  $row->form_name;
                $curform_abv_cnt =  $row->form_above;
                $curform_blw_cnt =  $row->form_below;
                $curform_button_text =  $row->button_text;
                
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
    
jQuery("#toplevel_page_ss_drforms").addClass("wp-has-current-submenu").addClass("wp-menu-open");
jQuery("#toplevel_page_ss_drforms > a").addClass("wp-has-current-submenu").addClass("wp-menu-open");
document.title = "Edit Form " + document.title;
				});
</script>
				
				<form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ss_drform-edit&formid=<?php echo $curform_id; ?>">	
				<?php

if (function_exists('settings_fields')) {
	settings_fields('ss_drf_settings');
} ?>		
						<?php //echo get_option('ss_drf_isdwnlfile');
 ?>
					<?php

if ($curform_is_down == 'yes') {
	$bgimg = 'checked="checked"';
}
else {
	$bgimg = "";
} ?>
					<?php

if ($curform_is_redir == 'yes') {
	$bgred = 'checked="checked"';
}
else {
	$bgred = "";
} ?>
							<table  cellpadding="3">
  <tr>
    
    <td>Form Name :</td>
    <td>
    <input type="text" class='colorwell' name="ss_drf_frmname" value="<?php echo $curform_name; ?>" size="100" /></td>
  </tr>
	<tr>
    <td><input type="checkbox" class='colorwell' <?php
echo $bgimg; ?> name="ss_drf_isdwnlfile" value="yes"> Download File</td>
  </tr>
  <tr>
  <td><input id="upload_image_button" class="button-primary" name="_unique_name_button" type="text" value="Upload File" style="width: 107px;" /></td>
    <td colspan="2">
    <?php
echo "<input type='text' size='100' ";
echo "class='colorwell' ";
echo "name='ss_drf_dwnlfile' ";
echo "id='ss_drf_dwnlfile' ";
echo "value='" . $curform_file_path . "' />\n";
?>
    </td>
  </tr>
  <tr>
    <td colspan="3"><input type="checkbox" class='colorwell' <?php
echo $bgred; ?> name="ss_drf_isredirect" value="yes"> Redirect Page</td>
  </tr>
    <tr>
    <td>Redirect Page Url</td>
    <td colspan="2"><input type="text" size='100' class='colorwell' name="ss_drf_redirecturl" value="<?php
echo $curform_redir_path; ?>"></td>    
  </tr>
  <tr>
  <td colspan="3"><p style="margin: 20px 0px 0px 0px;font-weight: bold;">Success Message : </p></td>  
  </tr>
<tr>
  <td colspan="3">
	<?php
$content = $curform_successmsg;
$id = 'successmsg';
$settings = array(
	'textarea_name' => "success_message",
	'media_buttons' => false,
	'textarea_rows' => '5',
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
$failcontent = $curform_failmsg;
$failid = 'failmsg';
$failsettings = array(
	'textarea_name' => "fail_message",
	'media_buttons' => false,
	'textarea_rows' => '5',
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
$form_above_content = $curform_abv_cnt;
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
$form_below_content = $curform_blw_cnt;
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
    <td colspan="2"><input type="text" class='colorwell' name="button_text" value="<?php echo $curform_button_text; ?>" size="100" /></td>
  </tr>
</table>																				
					<p class="submit">
				        <?php  wp_nonce_field( 'create_form_ss_drf_subsc'.$curform_id ,'crf_create_form_nonces', false); ?>
						<input type="hidden" name="formid" value="<?php echo $curform_id; ?>" />
						<input type='submit'  id="gobutton" class="button-primary" name='info_update' value='Update Form' />
					</p>					
				</form>
					
				
			</div><?php
 ?>
			
		</div>
<?php } else { 
echo "<div class='updated fade'><p><strong>" . __('You attempted to edit an item that doesn’t exist. Perhaps it was deleted?') . "</strong></p></div>";
 } ?>		
