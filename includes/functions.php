<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

        
        function ss_drf_plugin_run_install ($table_name, $table_version, $sql){
        
		   global $wpdb;
		   $wp_table_name = $wpdb->prefix . $table_name;
		   
		   if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name){
		   
				$sql_create_table = "CREATE TABLE " . $wp_table_name . " ( " . $sql . " ) ;";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql_create_table);
 
			//create option for table version
				$option_name = $table_name.'_tbl_version';
				$newvalue = $table_version;
				  if ( get_option($option_name) ) {
					    update_option($option_name, $newvalue);
					  } else {
					    $deprecated=' ';
					    $autoload='no';
					    add_option($option_name, $newvalue, $deprecated, $autoload);
				  }
			//create option for table name
				$option_name = $table_name.'_tbl';
				$newvalue = $wp_table_name;
				  if ( get_option($option_name) ) {
					    update_option($option_name, $newvalue);
					  } else {
					    $deprecated=' ';
					    $autoload='no';
					    add_option($option_name, $newvalue, $deprecated, $autoload);
				  }
		}
 
                // Code here with new database upgrade info/table Must change version number to work.
                $installed_ver = get_option( $table_name.'_tbl_version' );
                
                if( $installed_ver != $table_version ) {
                
		  $sql_create_table = "CREATE TABLE " . $wp_table_name . " ( " . $sql . " ) ;";
                  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                  dbDelta($sql_create_table);
                  update_option( $table_name.'_tbl_version', $table_version );
                  
                }
                
                add_role(
	                'staff'
	                , 'Staff'
	                , array(
		                'read'         => true,  // True allows this capability
		                'edit_posts'   => true,
		                'delete_posts' => false, // Use false to explicitly deny
	                )
                );
                
                add_role(
	                'client'
	                , 'Client'
	                , array(
		                'read'         => true,  // True allows this capability
		                'edit_posts'   => true,
		                'delete_posts' => false, // Use false to explicitly deny
	                )
                );
        }
        
        
    function export_subscriber_list(){

			$nonce = $_REQUEST['crf_export_nonces'];
			if ( ! wp_verify_nonce( $nonce, 'export_form_ss_drf' ) || !current_user_can('edit_posts') ) {
					die( 'Security check' );
			} else {
        		//if(isset($_GET['crf_export_nonces'])){
                global $wpdb;
                $formslist = $wpdb->prefix . "ss_drf_forms_list";
                $formsubs = $wpdb->prefix . "ss_drf_form_subscribers";
                $dat = date("Y_m_d");
                $t = time();
                $form_id = intval($_GET['formid']);
                $format_name = sanitize_text_field($_GET['formatname']);
                $flds = implode(',',$_GET['chsfield']);
                $results = $wpdb->get_results("SELECT $flds FROM $formsubs where form_id='$form_id'", ARRAY_A );
                if($format_name=="csv"){
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=subs_'.$dat.'_'.$t.'.csv');

                // create a file pointer connected to the output stream
                $output = fopen('php://output', 'w');
                $csv_fields=array();
                foreach($_GET['chsfield'] as $chsfld){
                $csv_fields[] = sanitize_text_field($chsfld);	
                }
                // output the column headings
                fputcsv($output, $csv_fields);

                foreach($results as $row) {
                    fputcsv($output, $row);
                }
                }

                if($format_name=="xl"){
                header("Content-type: application/vnd-ms-excel");
                header('Content-Disposition: attachment; filename=subs_'.$dat.'_'.$t.'.xls');
                ?>
                <table  cellspacing="0" cellpadding="0">
                    <tr>
                <?php 
                foreach($_GET['chsfield'] as $chsfld){
                        echo '<th>'.esc_html($chsfld).'</th>';	
                }
                ?>    
	                </tr>
                <?php foreach($results as $row) {
		                echo '<tr>';
		                foreach($_GET['chsfield'] as $chsfld){	
			                echo '<th>'. esc_html($row[$chsfld]) .'</th>';	
		                }    	
		                echo '</tr>';
                    //fputcsv($output, $row);
                }

                ?>

                </table>	
                <?php	
                }
       // } 
      }
                 exit();
    }
        
        add_action('wp_ajax_export_subscriber_list', 'export_subscriber_list');
        add_action('wp_ajax_nopriv_export_subscriber_list', 'export_subscriber_list'); 

        
        function add_list_options() {        			
        			global $wpdb;
					$formslist = $wpdb->prefix . "ss_drf_forms_list";
					$query = "SELECT *  FROM $formslist";
					$frmres = $wpdb->get_results($query);
					$html = '';
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
								$html .= '<option selected="selected" value="' . $row->id . '">' . $form_name . '</option>';
								}
							  else
								{
								$html .= '<option value="' . $row->id . '">' . $form_name . '</option>';
								}
							}
						}
					return $html;					
        }
?>
