<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

add_action('init', 'ss_drf_register_shortcodes');
function ss_drf_register_shortcodes()
{
    add_shortcode('smart-subscription-form', 'ss_drf_Shortcode');
add_filter( 'widget_text', 'shortcode_unautop');
add_filter( 'widget_text', 'do_shortcode');
}
wp_register_style('ss_drf-front-style', plugin_dir_url(__FILE__) . '/ss_drfstyle.css');
wp_enqueue_style('ss_drf-front-style');

function ss_drf_js_variables()
{
?>
      <script type="text/javascript">
        var ss_drfajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        var ss_drfdownurl = '<?php echo plugin_dir_url(__FILE__) . "/download_file.php"; ?>';
      </script>
      
<?php
}
add_action('wp_head', 'ss_drf_js_variables');

add_action('wp_footer', 'ss_drf_footerjs');
function ss_drf_footerjs(){
?>
<script type="text/javascript">
var $j = jQuery;
$j( document ).ready(function() {

var count = $j(".ss_drform").length;
if(count > 1)
{
$j(".ss_drform").each(function(i){

$j(this).addClass( "actionurl"+(i+1) );

//var actionurl = $j(this).attr("action");

//var action = actionurl+(i+1);
//$j(this).attr("action", action);
});
}
});
</script>
<script type="text/javascript" >

function validateEmail($email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( $email );
}
var $j = jQuery;
$j( document ).ready(function() {

    $j(".ss_drf_button").on("click",function(e){   e.preventDefault();
	var cur_form = $j(this).closest('form');
        cur_form.find(".ss_drfloader").show();
        var emailaddress = cur_form.find(".subscribe-input").val();                       
                if( !validateEmail(emailaddress) || emailaddress=="") {
                    cur_form.find(".subscribe-input").css( "border", "1px solid red" ); 
                    cur_form.find(".ss_drfloader").hide(); 
                    cur_form.find(".error").html("Enter Valid Email");                  
                }else{ 
                    cur_form.find(".subscribe-input").val('');
                    cur_form.find(".error").hide(); 
                cur_form.find(".subscribe-input").css( "border-color", "#999 #aaa #aaa" );
                var data = { 
        "action": "my_ss_drf_action",
        "email": emailaddress,
        "id": cur_form.find(".ss_drf_id").val(),
        "_wpnonce": cur_form.find("input[name^='crf_nonces']" ).val()
            };
      $j.ajax({
                type: "POST",
                url: ss_drfajaxurl,
                data: data,
                dataType: "json",
                success: function (response) {
            cur_form.find(".ss_drfrmres").html(response.message);
           cur_form.find(".ss_drfloader").hide();
            if(response.status=="success"){
                    cur_form.find(".subscribe-input").val("");
                    if(response.is_down=="yes"){
				$j( "#secretIFrame" ).remove();                    		
                    		$j("body").append( "<iframe id=\"secretIFrame\" src=\"\" style=\"display:none; visibility:hidden;\"></iframe>" );
                    		$j("#secretIFrame").attr("src",response.nonce_url);


                        //window.open(ss_drfdownurl+"?file="+response.file_path, "_blank");
                        }
                    if(response.is_redir=="yes"){                    
                        setTimeout(function(){ window.location = response.redir_path; }, 5000);                        
                        }                        
                }
            }
         });           
        }
        });
    });
</script>
<?php
}

add_action('wp_ajax_ss_drf_get_download', 'ss_drf_get_download_callback');
add_action('wp_ajax_nopriv_ss_drf_get_download', 'ss_drf_get_download_callback');

function ss_drf_get_download_callback(){

    global $wpdb;
    $ss_drf_id    = base64_decode(sanitize_text_field($_GET['ss_drf_token']));
    
    if (!isset($_GET['ss_drf_dwd_nonce']) || !wp_verify_nonce($_GET['ss_drf_dwd_nonce'], 'download_file'.$ss_drf_id )) {
           echo "Token Missing";
           die();
    } 
    
    $formlist = $wpdb->prefix . "ss_drf_forms_list";
    $query    = "SELECT *  FROM $formlist where id=" . $ss_drf_id;
    $frmres   = $wpdb->get_results($query);
            
  
    if ($wpdb->num_rows) {

        foreach ($frmres as $key => $row) {
                $upload_dir          = wp_upload_dir();
                $curform_file_path   = $row->file_path;
                $curfrmmod_file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $curform_file_path);
                $file = $curfrmmod_file_path; //Get the file from URL variable
                $file_array = explode('/', $file); //Try to seperate the folders and filename from the path
                $file_array_count = count($file_array); //Count the result
                $filename = $file_array[$file_array_count-1]; //Trace the filename
                $file_path = $curfrmmod_file_path; //Set the file path w.r.t the download.php... It may be different for u
                if(file_exists($file_path)) {
                    header("Content-disposition: attachment; filename={$filename}"); //Tell the filename to the browser
                    header('Content-type: application/octet-stream'); //Stream as a binary file! So it would force browser to download
                    readfile($file_path); //Read and stream the file
                }
                else {
                    echo "Sorry, the file does not exist!";
                }
                die();
        }
    }
     die();
    

       
}

add_action('wp_ajax_my_ss_drf_action', 'my_ss_drf_action_callback');
add_action('wp_ajax_nopriv_my_ss_drf_action', 'my_ss_drf_action_callback');

function my_ss_drf_action_callback()
{

        
    global $wpdb; // this is how you get access to the database
    
    $ss_drf_id    = intval($_POST['id']);
    $ss_drf_email = sanitize_email($_POST['email']);
    
    
    
    $formlist = $wpdb->prefix . "ss_drf_forms_list";
    $query    = "SELECT *  FROM $formlist where id=" . $ss_drf_id;
    
    $frmres   = $wpdb->get_results($query);
    if ($wpdb->num_rows) {
        foreach ($frmres as $key => $row) {
            $curform_id          = $row->id;
            $curform_is_down     = $row->is_download;
            $curform_file_path   = $row->file_path;
            $curform_is_redir    = $row->is_redirect;
            $curform_redir_path  = $row->redirect_path;
            $curform_successmsg  = $row->success_message;
            $curform_failmsg     = $row->failure_message;
            $curform_name        = $row->form_name;
            $upload_dir          = wp_upload_dir();
            $curfrmmod_file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $curform_file_path);
        }
    }
    
    if(!wp_verify_nonce( sanitize_text_field($_POST['_wpnonce']), 'create-ss_drf_subsc'.$ss_drf_id)) {
                $ss_drfresponse = array(
                    "status" => 'fail',
                    'message' => $curform_failmsg
            );
            die(json_encode($ss_drfresponse));
    }
    
    $formtable = $wpdb->prefix . "ss_drf_form_subscribers";
    $query_subs     = "SELECT *  FROM $formtable where email_id='" . $ss_drf_email . "' and form_id=".$ss_drf_id;
    $frmres    = $wpdb->get_results($query_subs);
    if ($curform_is_down == "yes") {			  
        if ($wpdb->num_rows) {				
            foreach ($frmres as $key => $row) {
                $total_down = $row->download_count + 1;
                $last_insert_id = $row->id;
            }
            $wpdb->update($formtable, array(
                'form_id' => $ss_drf_id,
                'email_id' => $ss_drf_email,
                'download_count' => $total_down
            ), array(
                'email_id' => $ss_drf_email
            ), array(
                '%d',
                '%s',
                '%d'
            ), array(
                '%s'
            ));
            
        } else {
            $wpdb->query($wpdb->prepare("INSERT INTO $formtable (form_id, email_id, download_count) VALUES ( %d, %s, %d)", array(
                $ss_drf_id,
                $ss_drf_email,
                1
            )));
            $last_insert_id = $wpdb->insert_id;
        }
    } else {
        if ($wpdb->num_rows) {
            foreach ($frmres as $key => $row) {
                $last_insert_id = $row->id;
            }
            $wpdb->update($formtable, array(
                'form_id' => $ss_drf_id,
                'email_id' => $ss_drf_email
            ), array(
                'email_id' => $ss_drf_email
            ), array(
                '%d',
                '%s'
            ), array(
                '%s'
            ));
        } else {
            $wpdb->query($wpdb->prepare("INSERT INTO $formtable (form_id, email_id) VALUES ( %d, %s)", array(
                $ss_drf_id,
                $ss_drf_email
            )));
            $last_insert_id = $wpdb->insert_id;
        }
        
    }

$nonce_url = wp_nonce_url(admin_url('admin-ajax.php?'), 'download_file'.$ss_drf_id, 'ss_drf_dwd_nonce' ).'&action=ss_drf_get_download&ss_drf_token='. base64_encode($ss_drf_id);

    if ($last_insert_id) {
        $ss_drfresponse = array(
            "status" => 'success',
            'is_down' => $curform_is_down,
           // 'file_path' => $curfrmmod_file_path,
            'is_redir' => $curform_is_redir,
            'redir_path' => $curform_redir_path,
            'message' => $curform_successmsg,
            'form_name' => $curform_name,
	    'nonce_url' => $nonce_url
            
        );
    } else {
        $ss_drfresponse = array(
            "status" => 'fail',
            'message' => $curform_failmsg
        );
    }
    
    die(json_encode($ss_drfresponse));
}

function ss_drf_Shortcode($params = array())
{
    // default parameters
    extract(shortcode_atts(array(
        'title' => 'Form 1',
        'id' => '1'
    ), $params));
global $wpdb;
$formlist = $wpdb->prefix . "ss_drf_forms_list";
    $query    = "SELECT *  FROM $formlist where id=" . $id;    
    $frmres   = $wpdb->get_results($query);
    if ($wpdb->num_rows) {
        foreach ($frmres as $key => $row) {
            $curform_name        = $row->form_name;
				$dupform_above =  stripslashes($row->form_above);
				$dupform_below =  $row->form_below;
				$dupform_button_text =  $row->button_text;
        }
    } 
    
    $ss_drfhtml = '<p style="display:none;">Title = ' . $title . ' : Id: == ' . $id . '</p>';

$ss_drfhtml .= '<section class="drsubscribe">
    <div class="subscribe-pitch frmtop">
      <h3>' . $title . '</h3>
      <p>' . $dupform_above . '<p>
    </div>
    <form action="#" method="post" class="subscribe-form ss_drform ss_drform' . $id . '" id="ss_drform' . $id . '">
      <input type="email" name="email" id="ss_drfemail' . $id . '" class="subscribe-input" placeholder="Your email address..." autofocus>
      <span style="color:red; font-size: 12px;" class="error error' . $id . '"></span><br>
      <button type="button" name="submit" id="ss_drfsubbut' . $id . '" class="subscribe-submit test ss_drf_button">'.$dupform_button_text.'</button>
      <input type="hidden" class="ss_drf_id" id="ss_drfrm' . $id . '" value="' . $id . '" />        
    <img src="' . plugin_dir_url(__FILE__) . 'ss_drfloader.gif" alt="loader" class="ss_drfload ss_drfloader' . $id . '" style="">
	<p class="ss_drfrmres ss_drfrmres' . $id . '" style="color: green;"></p>';
$ss_drfhtml .= wp_nonce_field( 'create-ss_drf_subsc'.$id ,'crf_nonces'.$id, false);
$ss_drfhtml .= '</form> 
<div class="subscribe-pitch frmbot">
      <p>' . $dupform_below . '<p>
    </div>
  </section>';
    return $ss_drfhtml;
    $ss_drfhtml .= '<script type="text/javascript" >
function validateEmail($email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( $email );
}
var $j = jQuery;
$j( document ).ready(function() {

    $j("#ss_drfsubbut' . $id . '").on("click",function(){
        $j(".ss_drfloader' . $id . '").show();
        var emailaddress = $j("#ss_drfemail' . $id . '").val();                       
                if( !validateEmail(emailaddress) || emailaddress=="") {
                    $j("#ss_drfemail' . $id . '").css( "border", "1px solid red" ); 
                    $j(".ss_drfloader' . $id . '").hide(); 
                    $j(".error' . $id . '").html("Enter Valid Email");                  
                }else{ 
                    $j(".error' . $id . '").hide(); 
                $j("#ss_drfemail' . $id . '").css( "border-color", "#999 #aaa #aaa" );
                var data = {
        "action": "my_ss_drf_action",
        "email": $j("#ss_drfemail' . $id . '").val(),
        "id": $j("#ss_drfrm' . $id . '").val(),
        "_wpnonce": $j("#crf_nonces' . $id . '").val()
            };
      $j.ajax({
                type: "POST",
                url: ss_drfajaxurl,
                data: data,
                dataType: "json",
                success: function (response) {
            $j(".ss_drfrmres' . $id . '").html(response.message);
            $j(".ss_drfloader' . $id . '").hide();
            if(response.status=="success"){
                    $j("#ss_drfemail' . $id . '").val("");
                    if(response.is_down=="yes"){
								$j( "#secretIFrame" ).remove();                    		
                    		$j("body").append( "<iframe id=\"secretIFrame\" src=\"\" style=\"display:none; visibility:hidden;\"></iframe>" );
                    		$j("#secretIFrame").attr("src","'.wp_nonce_url(admin_url("admin-ajax.php?"), 'download_file'.$id, "ss_drf_dwd_nonce" ).'&action=ss_drf_get_download&ss_drf_token='. base64_encode($id).'");
                        //window.open(ss_drfdownurl+"?file="+response.file_path, "_blank");
                        }
                    if(response.is_redir=="yes"){                    
                        setTimeout(function(){ window.location = response.redir_path; }, 5000);                        
                        }                        
                }
            }
         });           
        }
        });
    });
</script>';
    
    return $ss_drfhtml;
}

add_action('wp_ajax_my_ss_drf_duplicate', 'my_ss_drf_duplicate');
add_action('wp_ajax_nopriv_my_ss_drf_duplicate', 'my_ss_drf_duplicate');

function my_ss_drf_duplicate(){
global $wpdb;
$ss_drformid = intval($_POST['formid']);
$dup_msg = "<div class='updated fade'><p><strong>" . __('Something went wrong.Please try again.') . "</strong></p></div>";
$ss_drfresponse = array("status" => 'fail','message' => $dup_msg); 
$formtable = $wpdb->prefix . "ss_drf_forms_list";
$query = "SELECT *  FROM $formtable where id='$ss_drformid'";
$frmres = $wpdb->get_results($query);
if($wpdb->num_rows){
foreach( $frmres as $key => $row) {
$dupform_is_down =  $row->is_download;
$dupform_file_path =  $row->file_path;
$dupform_is_redir =  $row->is_redirect;
$dupform_redir_path =  $row->redirect_path;
$dupform_successmsg =  $row->success_message;
$dupform_failmsg =  $row->failure_message;
$dupform_name =  $row->form_name;
$dupform_above =  $row->form_above;
$dupform_below =  $row->form_below;	
}
$wpdb->query( $wpdb->prepare(
	"INSERT INTO $formtable (form_name, is_download, file_path, is_redirect, redirect_path, success_message, failure_message, form_above, form_below) VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s )",
	array(
		$dupform_name,	
		$dupform_is_down,
		$dupform_file_path,
		$dupform_is_redir,
		$dupform_redir_path,
		$dupform_successmsg,
		$dupform_failmsg,
		$dupform_above,
		$dupform_below										
	)
));
$last_insert_id = $wpdb->insert_id;

if($last_insert_id){
$dup_msg = "<div class='updated fade'><p><strong>" . __($dupform_name.' Form duplicated successfully.') . "</strong></p></div>";
$ss_drfresponse = array(
            "status" => 'success',
            'message' => $dup_msg
        ); 		
}else {
$dup_msg = "<div class='updated fade'><p><strong>" . __($dupform_name.' Form not duplicated.Please try again.') . "</strong></p></div>";
$ss_drfresponse = array(
            "status" => 'fail',
            'message' => $dup_msg
        ); 		
}

}
die(json_encode($ss_drfresponse));
}

?>
