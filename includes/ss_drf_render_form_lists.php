<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
if (!current_user_can('edit_posts'))
{
    wp_die(__('You do not have sufficient permissions to manage plugins for this site.'));
}
?>
		<select class="frmdrpdwn" onchange="getval(this);">
			<option>Please select a form:</option>
<?php
			echo add_list_options();
global $wpdb;
 /* $formslist = $wpdb->prefix . "ss_drf_forms_list";
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
	} */

?>
		</select>
<?php
$params = $_GET;

if (isset($_GET['form']) && check_admin_referer('ss_drf-form_subscribers_lists'))
	{	
	$formtable = $wpdb->prefix . "ss_drf_form_subscribers where form_id=" . intval($_GET['form']);
	$js = 0;
	$start = 0;
	$limit = 10;
	if (isset($_GET['pg']))
		{
		$page = intval($_GET['pg']);
		$start = ($page - 1) * $limit;
		}
	  else
		{
		$page = 1;
		}

	$query = "SELECT *  FROM $formtable LIMIT $start, $limit";
	$frmres = $wpdb->get_results($query);
?>
<div class="datagrid"><table>
<thead><tr><th>Email ID</th><th>Download</th></tr></thead>
<?php
	if ($wpdb->num_rows)
		{ ?>
<tfoot><tr><td colspan="2">
<div id="paging">
<ul>
<?php
		$query2 = "SELECT *  FROM $formtable";
		$frmres2 = $wpdb->get_results($query2);
		$rows = count($frmres2);
		$total = ceil($rows / $limit);
		if ($page > 1)
			{
			$params['pg'] = ($page - 1);
			$paramString = http_build_query($params);
			echo "<li><a href='" . get_option('siteurl') . "/wp-admin/admin.php?" . $paramString . "'><span>Previous</span></a></li>";
			}

		for ($i = 1; $i <= $total; $i++)
			{
			if ($i == $page)
				{
				echo "<li class='current'><a href='#' class='active'><span>" . $i . "</span></a></li>";
				}
			  else
				{
				$params['pg'] = $i;
				$paramString = http_build_query($params);
				echo "<li><a href='" . get_option('siteurl') . "/wp-admin/admin.php?" . $paramString . "'><span>" . $i . "</span></a></li>";
				}
			}

		if ($page != $total)
			{
			$params['pg'] = ($page + 1);
			$paramString = http_build_query($params);
			echo "<li><a href='" . get_option('siteurl') . "/wp-admin/admin.php?" . $paramString . "'><span>Next</span></a></li>";
			}

?>
</ul>
</div>
</td></tr></tfoot>
<tbody>
<?php
		if ($wpdb->num_rows)
			{
			foreach($frmres as $key => $row)
				{
				$ss_drf_id = $row->form_id;
				$ss_drf_email = $row->email_id;
				$total_down = $row->download_count;
				echo '<tr ';
				if ($js % 2 != 0)
					{
					echo 'class="alt"';
					}

				echo '><td>' . $ss_drf_email . '</td><td>' . $total_down . '</td></tr>';
				$js++;
				}
			}

?>
</tbody>
<?php
		}
	  else
		{ ?>
<tfoot><tr><td colspan="2"></td></tr></tfoot>
<tbody><tr><td colspan="2" style="text-align: center;font-weight: bold;font-size: 14px;">No Subscribers List</td></tr></tbody>
<?php
		} ?>
</table>
</div>
<?php
	} ?>
<input type="hidden" id="pgurl" value="<?php echo wp_nonce_url(get_option('siteurl').'/wp-admin/admin.php?page=drlists', 'ss_drf-form_subscribers_lists'); ?>" />
<script type="text/javascript">
    function getval(sel) {

       // alert(sel.value);
       // alert(jQuery("#pgurl").val());

       window.location = jQuery("#pgurl").val() + '&form=' + sel.value; 
    }
</script>
