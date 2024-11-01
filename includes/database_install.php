<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function ss_drf_plugin_data_tables_install () {
$table_version = SS_DRF_PLUGIN_VERSION; //Call the plugin version.
//Install the first table
$table_name = "ss_drf_forms_list";
$sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,
	form_name VARCHAR(255) NOT NULL,	
	is_download VARCHAR(10) NOT NULL,
	file_path VARCHAR(255) NOT NULL,
	is_redirect VARCHAR(10) NOT NULL,
	redirect_path VARCHAR(255) NOT NULL,	
	success_message longtext NOT NULL,
	failure_message longtext NOT NULL,
	form_above longtext NOT NULL,
	form_below longtext NOT NULL,	
	button_text VARCHAR(255) NOT NULL,	
	PRIMARY KEY  (id)";
ss_drf_plugin_run_install  ($table_name, $table_version, $sql);

$table_name = "ss_drf_form_subscribers";
$sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,
	form_id mediumint(9) NOT NULL,
	email_id VARCHAR(255) NOT NULL,
	download_count mediumint(9) NOT NULL,
	PRIMARY KEY  (id)";
ss_drf_plugin_run_install  ($table_name, $table_version, $sql);

//Install the second table
/* $table_name = "projects";
$sql = "id mediumint(9) NOT NULL AUTO_INCREMENT,
	   project_name VARCHAR(255) DEFAULT NULL,
	   Company VARCHAR(255) DEFAULT NULL,
	   Client VARCHAR (255) DEFAULT NULL,
	  PRIMARY KEY  (id)";
ss_drf_plugin_run_install  ($table_name, $table_version, $sql);
*/
}
?>
