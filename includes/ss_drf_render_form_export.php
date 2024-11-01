<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!current_user_can('edit_posts'))
{
    wp_die(__('You do not have sufficient permissions to manage plugins for this site.'));
}

global $wpdb;
$formslist = $wpdb->prefix . "ss_drf_forms_list";
$formsubs = $wpdb->prefix . "ss_drf_form_subscribers";

?>

  <form  action="" name="drexportform" id="drexportform" class="drexport" method="Get">
    <fieldset class="drexport-inner">

      <p class="drexport-input">
        <label for="select" class="select">
          <select name="chsform" id="chsform" onchange="getval(this);">
          <option value="">Choose Form</option>
<?php

$query = "SELECT *  FROM $formslist";
$frmres = $wpdb->get_results($query);

if ($wpdb->num_rows)
	{
	foreach($frmres as $key => $row)
		{
		$form_name = $row->form_name;
		if ($form_name == '')
			{
			$form_name = 'Form ' . $form_id;
			}

		if (intval($_GET['form']) == $row->id)
			{
			echo '<option selected="selected" value="' . $row->id . '">' . $form_name . '</option>';
			}
		  else
			{
			echo '<option value="' . $row->id . '">' . $form_name . '</option>';
			}
		}
	}

?>
          </select>
        </label>
      </p>
		      
      <p class="drexport-check">
        <label for="" class="">Choose fields : </label><br>
        <label for="" class="check"><input type="checkbox" name="chsfield[]" value="form_id">Form ID</label>                
        <label for="" class="check"><input type="checkbox" name="chsfield[]" value="email_id">Email Address</label>
		  <label for="" class="check"><input type="checkbox" name="chsfield[]" value="download_count">Download Count</label>
      </p>      

      <p class="drexport-input">
        <label for="select" class="select">
          <select name="chsformat" id="chsformat" onchange="getformatval(this);">
          <option value="">Choose Export Format</option>
          <option value="csv">CSV</option>
          <option value="xl">XLS</option>
          </select>
        </label>
      </p>

      <p class="drexport-submit">
        <?php  wp_nonce_field( 'export_form_ss_drf','crf_export_nonces', false); ?>
        <input type="submit" name="submitexport" value="Export">
      </p>
<!--       <input hidden name="actionurl" id="actionurl" value="<?php echo admin_url("admin-ajax.php"); ?>';<?php echo plugin_dir_url( __FILE__ ).'/download_csv.php'; ?>" /> -->
       <input hidden name="actionurl" id="actionurl" value="<?php echo admin_url("admin-ajax.php"); ?>?action=export_subscriber_list" /> 
      <input hidden name="formid" id="formid" value="" />
      <input hidden name="formatname" id="formatname" value="" />
      
    </fieldset>
  </form>
    <p style="position: relative;    margin: 50px auto;    padding: 5px;    width: 720px;">
    <iframe id="iframedownloadurl"  src=""></iframe>
    </p>
  
<script type="text/javascript">
jQuery(document).ready(function(){
jQuery("#drexportform").on("submit", function(e) {
    e.preventDefault();
    var form = jQuery(this);
    var action = jQuery("#actionurl").val();
    var formValues = form.find('input[name!=actionurl]').serialize();
    var url = action + "&" + formValues;
    jQuery('#iframedownloadurl').attr('src',url);	
    jQuery('#drexportform')[0].reset();
	});
});
    function getval(sel) {
        //alert(sel.value);
       jQuery("#formid").val(sel.value); 
    }
    function getformatval(sel) {
        //alert(sel.value);
       jQuery("#formatname").val(sel.value); 
    }    
</script>  

