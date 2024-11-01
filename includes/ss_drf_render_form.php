<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!current_user_can('edit_posts'))
{
    wp_die(__('You do not have sufficient permissions to manage plugins for this site.'));
}

global $wpdb;
$formtable = $wpdb->prefix . "ss_drf_forms_list";

if (isset($_GET['bulk_action']) && wp_verify_nonce( $_GET['render_form_nonce'] , 'render_form_action' )){
     
     $dr_form_ids = $_GET['ss_drfrm'];	
     
     if(sanitize_text_field($_GET['action'])=="delete"){
     
        foreach($dr_form_ids as $dr_id) {
                    $wpdb->query( $wpdb->prepare("DELETE FROM $formtable WHERE id = %d", intval( $dr_id ) ) );
        }
        
        echo "<div class='updated fade'><p><strong>" . __('Forms deleted successfully.') . "</strong></p></div>"; 
        	
     }
}

$js = 0;

if(isset($_GET['s'])){
        $query = "SELECT *  FROM $formtable where form_name LIKE  '%" . sanitize_text_field($_GET['s']) . "%'";	
}else {
        $query = "SELECT *  FROM $formtable";
}

$frmres = $wpdb->get_results($query);
$total_count = $wpdb->num_rows;

?>
<form method="get" action="">
	<input type="hidden" name="page" value="ss_drforms">
	<p class="search-box">
	<label class="screen-reader-text" for="ss_drform-search-input">Search Forms:</label>
	<input type="search" id="ss_drform-search-input" name="s" value="<?php echo sanitize_text_field($_GET['s']); ?>">
	<input type="submit" name="" id="ss_drform-search-submit" class="button" value="Search Forms">
	</p>
	<?php wp_nonce_field('render_form_action', 'render_form_nonce'); ?>
	<?php wp_get_referer(); ?>
<div class="tablenav top">

		<div class="alignleft actions bulkactions">
			<select name="action">
<option value="-1" selected="selected">Bulk Actions</option>
	<option value="delete">Delete</option>
</select>
<input type="submit" name="bulk_action" id="doaction" class="button action" value="Apply">
		</div>
<div class="tablenav-pages one-page"><?php echo '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_count ), number_format_i18n( $total_count ) ) . '</span>'; ?></div>
		<br class="clear">
	</div>
<script src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/smart-subscription/includes/clipboard.min.js"></script>	

<table class="wp-list-table widefat fixed" >
<thead><tr><th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="ss_drf-select-all-1">Select All</label><input id="ss_drf-select-all-1" type="checkbox"></th><th>Title</th><th>Shortcode</th><th></th></tr></thead>
<tfoot><tr><th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="ss_drf-select-all-2">Select All</label><input id="ss_drf-select-all-2" type="checkbox"></th><th>Title</th><th>Shortcode</th><th></th></tr></tfoot>
<tbody>
<?php
if($wpdb->num_rows){
   foreach( $frmres as $key => $row) {
   
        $form_id = $row->id;
        $form_is_down =  $row->is_download;
        $form_file_path =  $row->file_path;
        $form_is_redir =  $row->is_redirect;
        $form_redir_path =  $row->redirect_path;
        $form_successmsg =  $row->success_message;
        $form_failmsg =  $row->failure_message;
        $form_name =  $row->form_name;
        
        if($form_name==''){
                $form_name = 'Form '.$form_id;
        }
        
        if($js % 2 ==0){ 
                $trcls = 'class="alternate"';
        } else {
                $trcls = '';
        }
        ?>
        <tr <?php echo $trcls; ?>>
        <th scope="row" class="check-column"><input type="checkbox" name="ss_drfrm[]" value="<?php echo $form_id ?>"></th>
        <td class="title column-title"><strong><a class="" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ss_drform-edit&formid=<?php echo $form_id; ?>" title="<?php echo $form_name; ?>"><?php echo $form_name; ?></a></strong> 
        <div class="row-actions"><span class="edit"><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ss_drform-edit&formid=<?php echo $form_id; ?>">Edit</a></span> | <span class="edit"><a href="#" data-formid="<?php echo $form_id; ?>" class="drduplicate">Duplicate</a></span></div></td>
        <td class="shortcode column-shortcode">
              <p class="drexport-input">
        <input type="text" onfocus="this.select();" readonly="readonly" value='[smart-subscription-form id="<?php echo $form_id ?>" title="<?php echo $form_name; ?>"]' class="shortcode-in-list-table wp-ui-text-highlight code" id="shortcodefrm<?php echo $form_id ?>">
		        </p>
        </td>
        <td><a href="#" id="copy-button<?php echo $form_id ?>" class="copy-clip" data-clipboard-action="copy" data-clipboard-target="#shortcodefrm<?php echo $form_id ?>">Copy to Clipboard</a></td>
        </tr>
        <script type="text/javascript">	
            var clipboard = new Clipboard('#copy-button<?php echo $form_id ?>');

            clipboard.on('success', function(e) {
                console.log(e);
            });

            clipboard.on('error', function(e) {
                console.log(e);
            });    		
	        </script>
        <?php
        $js++; 
   }
} else { ?>
        <tr><td colspan="3">No forms created</td></tr>
<?php 
}
?>

</tbody>
</table>
</form>
