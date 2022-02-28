<?php
/**
 * Plugin Name: Custom Checklist From
 * Description: Handling Checklist - Create Checkbox List in the Backend with Description & Displaying those data to Front End Posts or Pages & User will react with these Checklists & stored to the Database - Developed By rapsum97.
 * Version: 1.0.1
 * Author: rapsum97
 * Author URI: https://www.fiverr.com/rapsum97
**/

/* Include Files */ 
function add_stylesheet() {
	wp_enqueue_script('jquery');
    wp_register_script("jqueryJS", plugins_url("/assets/js/jquery.min.js", __FILE__), array("jquery"));
    wp_enqueue_script("jqueryJS");
    wp_register_script("PopperJS", plugins_url("/assets/js/popper.min.js", __FILE__), array("jquery"));
    wp_enqueue_script("PopperJS");
    wp_register_script("bootstrapJS", plugins_url("/assets/js/bootstrap.min.js", __FILE__), array("jquery"));
    wp_enqueue_script("bootstrapJS");
    wp_register_script("dataTableJS", plugins_url("/assets/js/dataTables.bootstrap4.min.js", __FILE__), array("jquery"));
    wp_enqueue_script("dataTableJS");
    wp_register_script("customJS", plugins_url("/assets/js/script.js", __FILE__), array("jquery"));
    wp_enqueue_script("customJS");
    wp_register_script("tableDataTableJS", plugins_url("/assets/js/jquery.dataTables.min.js", __FILE__), array("jquery"));
    wp_enqueue_script("tableDataTableJS");
    wp_register_script("tableJS", plugins_url("/assets/js/dataTables.bootstrap4.min.js", __FILE__), array("jquery"));
    wp_enqueue_script("tableJS");
    wp_register_style("customCSS", plugins_url("/assets/css/customStyle.css", __FILE__));
    wp_enqueue_style("customCSS");   
    wp_register_style("datatableCSS", plugins_url("/assets/css/dataTables.bootstrap4.min.css", __FILE__));
    wp_enqueue_style("datatableCSS");
    wp_register_style("bootstrapCSS", plugins_url("/assets/css/bootstrap.min.css", __FILE__));
    wp_enqueue_style("bootstrapCSS");
	wp_enqueue_style("FontAwesomeCSS", "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css", false);
}
add_action("wp_enqueue_scripts", "add_stylesheet");
add_action("admin_print_styles", "add_stylesheet");

// Create Table
register_activation_hook( __FILE__, 'checkBoxListTable');
function checkBoxListTable() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'custom_checkbox_list';
	$sql = "CREATE TABLE `$table_name` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`label` varchar(220) NOT NULL,
		`description` text DEFAULT NULL,
		`created` datetime NOT NULL,
		PRIMARY KEY(id)
	) ENGINE = MyISAM DEFAULT CHARSET = latin1;";

	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	$table_name2 = $wpdb->prefix . 'custom_checkbox_user_selected';
	$sql2 = "CREATE TABLE `$table_name2` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`uid` int(20) NOT NULL,
		`checkbox` text DEFAULT NULL,
		PRIMARY KEY(id)
	) ENGINE = MyISAM DEFAULT CHARSET = latin1;";

	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name2'") != $table_name2) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql2);
	}
}

/* Plugin Menu and Submenu */
add_action('admin_menu', 'CheckboxListPlugin');
function CheckboxListPlugin() {
	add_menu_page('Checkbox Lists', 'Checkbox Lists', 'manage_options' ,__FILE__, 'checkboxListPage', 'dashicons-wordpress');
}
function checkboxListPage() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'custom_checkbox_list';
	$table_name2 = $wpdb->prefix . 'custom_checkbox_user_selected';
	$time = date('Y-m-d H:i:s');

	// While Insert
	$id = "newCheckboxDescription";
	$name = 'newCheckboxDescription';
	$content = esc_textarea(stripslashes(''));
	$settings = array(
		'tinymce' => true,
		'textarea_name' => 'newCheckboxDescription',
		'quicktags' => false,
	   	'wpautop' => false,		// auto add p tags to paragraphs
	   	'media_buttons' => false,	// show or hide the media upload button
	   	'textarea_rows' => 6,	// how many rows you want in the text area
	   	'teeny' => false	// show or hide the compact editor view
	);

	if (isset($_POST['newCheckboxSubmit'])) {
		$label = $_POST['newCheckboxLabel'];
		$description = $_POST['newCheckboxDescription'];
		$wpdb->query("INSERT INTO $table_name (label, description, created) VALUES ('$label', '$description', '$time')");
		echo "<script>alert('New Checkbox Details Added Successfully!');</script>";
		echo "<script>location.replace('admin.php?page=Custom Checklist From/function.php');</script>";
	}
	if (isset($_POST['updateCheckboxSubmit'])) {
		$id = $_GET['updateCheckbox'];
		$label = $_POST['updateCheckboxLabel'];
		$description = $_POST['updateCheckboxDescription'];
		$wpdb->query("UPDATE $table_name SET label = '$label', description = '$description' WHERE id = '$id'");
		echo "<script>alert('Checkbox Details Updated Successfully!');</script>";
		echo "<script>location.replace('admin.php?page=Custom Checklist From/function.php');</script>";
	}
	if (isset($_GET['deleteCheckbox'])) {
		$del_id = $_GET['deleteCheckbox'];
		$wpdb->query("DELETE FROM $table_name WHERE id = '$del_id'");
		echo "<script>alert('Checkbox Details Deleted Successfully!');</script>";
		echo "<script>location.replace('admin.php?page=Custom Checklist From/function.php');</script>";
	}
  	?>
  	<div class="wrap custom_checklist">
  		<h2 style="margin-bottom: 16px; font-weight: 600;">Checkbox List Management</h2>
  		<table class="wp-list-table widefat striped">
  			<thead>
  				<tr>
  					<th width="32%">Label</th>
  					<th width="53%">Description</th>
  					<th width="15%">Actions</th>
  				</tr>
  			</thead>
  			<tbody>
  				<form action="" method="post">
  					<tr>
  						<td><input type="text" id="newCheckboxLabel" name="newCheckboxLabel" style="width: 100%;" required></td>
  						<td><?php wp_editor($content, $id, $settings); ?></td>
  						<td><button class="btn btn-primary btn-sm" id="newCheckboxSubmit" name="newCheckboxSubmit" type="submit">INSERT</button></td>
  					</tr>
  				</form>
  			</tbody>
  		</table>
  		<br>
  		<br>
  		<table class="wp-list-table widefat striped" id="displayAllCheckboxAdmin">
  			<thead>
  				<tr>
  					<th width="6%">ID</th>
  					<th width="29%">Label</th>
  					<th width="49%">Description</th>
  					<th width="16%">Actions</th>
  				</tr>
  			</thead>
  			<tbody>
  				<?php
  				$result = $wpdb->get_results("SELECT * FROM $table_name");
  				foreach ($result as $print) { ?>
  					<tr>
  						<td width='6%'><?php echo $print->id; ?></td>
  						<td width='29%'><?php echo $print->label; ?></td>
  						<td width='49%'><?php echo $print->description; ?></td>
  						<td width='16%'><a href='admin.php?page=Custom Checklist From/function.php&updateCheckbox=<?php echo $print->id; ?>'><button class='btn btn-success btn-sm' type='button'>UPDATE</button></a> <a href='admin.php?page=Custom Checklist From/function.php&deleteCheckbox=<?php echo $print->id; ?>'><button class='btn btn-danger btn-sm' type='button'>DELETE</button></a></td>
  					</tr>
  				<?php } ?>
  			</tbody>
  		</table>
  		<br>
  		<br>
  		<?php
  		if (isset($_GET['updateCheckbox'])) {
  			$upt_id = $_GET['updateCheckbox'];
  			$result = $wpdb->get_results("SELECT * FROM $table_name WHERE id = '$upt_id'");
  			foreach($result as $print) {
  				$label = $print->label;
  				$description = $print->description;
  			}

  			// While Update
			$id = "updateCheckboxDescription";
			$name = 'updateCheckboxDescription';
			$content = esc_textarea(stripslashes($description));
			$settings = array(
				'tinymce' => true,
				'textarea_name' => 'updateCheckboxDescription',
				'quicktags' => false,
			   	'wpautop' => false,		// auto add p tags to paragraphs
			   	'media_buttons' => false,	// show or hide the media upload button
			   	'textarea_rows' => 6,	// how many rows you want in the text area
			   	'teeny' => false	// show or hide the compact editor view
			);

			?>

			<table class="wp-list-table widefat striped">
	  			<thead>
	  				<tr>
	  					<th width="32%">Label</th>
	  					<th width="53%">Description</th>
	  					<th width="15%">Actions</th>
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<form action="" method="post">
	  					<tr>
	  						<td><input type="text" id="updateCheckboxLabel" name="updateCheckboxLabel" value="<?php echo $label; ?>" style="width: 100%;" required></td>
	  						<td><?php wp_editor($content, $id, $settings); ?></td>
	  						<td><button class="btn btn-success btn-sm" id="updateCheckboxSubmit" name="updateCheckboxSubmit" type="submit">UPDATE</button> <a href="admin.php?page=Custom Checklist From/function.php"><button class="btn btn-danger btn-sm" type="button">CANCEL</button></a></td>
	  					</tr>
	  				</form>
	  			</tbody>
	  		</table>
		<?php } ?>
	</div>
	<?php
}

// Register Shortcode
add_shortcode('CCF_Display_Shortcode', 'CCF_shortcode');
// function that runs when Shortcode is called
function CCF_shortcode() {
	ob_start();
    global $wpdb;
    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));
    $domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $current_user_id = '';
	$table_name = $wpdb->prefix . 'custom_checkbox_list';
	$table_name2 = $wpdb->prefix . 'custom_checkbox_user_selected';
	$time = date('Y-m-d H:i:s');

	$new_var = '';
	if (isset($_POST['newUserCheckboxSubmit'])) {
		if (!empty($_POST['newcheckboxList'])) {
			$checked_array = $_POST['newcheckboxList'];
			foreach ($_POST['newcheckboxList'] as $key => $value) {
				if (in_array($_POST['newcheckboxList'][$key], $checked_array)) {
					$checkbox_id = $_POST['newcheckboxList'][$key];
					if (empty($new_var)) {
						$new_var = $checkbox_id;
					}
					else {
						$new_var = $new_var.', '.$checkbox_id;	
					}
				}
			}
			$uid = $_POST['hiddenUserId'];
			$wpdb->query("INSERT INTO $table_name2 (uid, checkbox) VALUES ('$uid', '$new_var')");
		}
		// echo "<script>location.reload();</script>";
		// exit;
	}

	$new_var = '';
	if (isset($_POST['updateUserCheckboxSubmit'])) {
		if (isset($_POST['checkboxList']) && !empty($_POST['checkboxList'])) {
			$checked_array = $_POST['checkboxList'];
			foreach ($_POST['checkboxList'] as $key => $value) {
				if (in_array($_POST['checkboxList'][$key], $checked_array)) {
					$checkbox_id = $_POST['checkboxList'][$key];
					if (empty($new_var)) {
						$new_var = $checkbox_id;
					}
					else {
						$new_var = $new_var.', '.$checkbox_id;	
					}
				}
			}
			$uid = $_POST['hiddenUserId'];
			$wpdb->query("UPDATE $table_name2 SET checkbox = '$new_var' WHERE uid = '$uid'");
		}
		else {
			$uid = $_POST['hiddenUserId'];
			$wpdb->query("DELETE FROM $table_name2 WHERE uid = '$uid'");
		}
		// echo "<script>location.reload();</script>";
		// exit;
	} ?>

	<script type="text/javascript">
		jQuery(document).ready(function () {
		    jQuery("#newUserCheckboxSubmit").click(function() {
		        if (jQuery('input:checkbox').filter(':checked').length < 1) {
		            alert("Check at least one List Item to Proceed!");
		            return false;
		        }
		    });
		});
	</script>

	<style type="text/css">
		.icon-plus:hover {
		    color: #468;
		}
		.btn-group-sm>.btn, .btn-sm {
		    padding: .4rem 1rem !important;
		    font-size: .875rem !important;
		    line-height: 1.5 !important;
		    border-radius: .2rem !important;
		}
		.btn-primary {
		    color: #fff !important;
		    background-color: #007bff !important;
		    border-color: #007bff !important;
		}
		.btn-success {
		    color: #fff !important;
		    background-color: #28a745 !important;
		    border-color: #28a745 !important;
		}
		.btn-success:hover {
		    color: #fff !important;
		    background-color: #218838 !important;
		    border-color: #1e7e34 !important;
		}
		.btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
		    color: #fff !important;
		    background-color: #0062cc !important;
		    border-color: #005cbf !important;
		}
	</style>

	<?php
	// Get User ID
	$current_user_id = get_current_user_id();
	if (isset($current_user_id) && !empty($current_user_id)) {
		$checkboxUserResult = $wpdb->get_results("SELECT * FROM $table_name2 WHERE uid = '$current_user_id'");
		if (!empty($checkboxUserResult)) {
			foreach ($checkboxUserResult as $checkboxUserPrint) {
				if (strlen($checkboxUserPrint->checkbox) > 1) {
					$new_check = explode (", ", $checkboxUserPrint->checkbox);
				}
				else {
					$new_check = $checkboxUserPrint->checkbox;
				}
			}
		}
	}

	echo "<div class='displayAll'>
		<form action='' method='POST' id='addUserCheckboxForm'>";
			$checkboxResult = $wpdb->get_results("SELECT * FROM $table_name");
			foreach ($checkboxResult as $checkboxPrint) { ?>
				<div class="displayAllCheckbox" style="padding: 16px 0px; border-bottom: 1px solid #DBDBDB;">
					<div class="" style="position: relative; z-index: 1; display: block; min-height: 1.5rem; -webkit-print-color-adjust: exact; color-adjust: exact;">
						<div class="form-check" style="padding-left: 0rem !important;">
							<?php
							if (isset($new_check)) {
								if (in_array($checkboxPrint->id, $new_check)) { ?>
									<input type="checkbox" id="<?php echo $checkboxPrint->label; ?>" name="checkboxList[]" checked value="<?php echo $checkboxPrint->id; ?>" style="vertical-align: middle; margin-right: 3px; z-index: -1;">
								<?php }
								elseif ($checkboxPrint->id == $new_check) { ?>
									<input type="checkbox" id="<?php echo $checkboxPrint->label; ?>" name="checkboxList[]" checked value="<?php echo $checkboxPrint->id; ?>" style="vertical-align: middle; margin-right: 3px; z-index: -1;">
								<?php }
								else { ?>
									<input type="checkbox" id="<?php echo $checkboxPrint->label; ?>" name="checkboxList[]" value="<?php echo $checkboxPrint->id; ?>" style="vertical-align: middle; margin-right: 3px; z-index: -1;">
								<?php }
							}
							else { ?>
								<input type="checkbox" id="<?php echo $checkboxPrint->label; ?>" name="newcheckboxList[]" value="<?php echo $checkboxPrint->id; ?>" style="vertical-align: middle; margin-right: 3px; z-index: -1;">
							<?php } ?>
							<label for="<?php echo $checkboxPrint->label; ?>" style="vertical-align: middle; display: inline-block; position: relative; margin-bottom: 0; font-size: 15px;"><?php echo $checkboxPrint->label; ?>
							</label>
						</div>
						<?php if (!empty($checkboxPrint->description)) { ?>
							<p id="click_advance-<?php echo $checkboxPrint->id; ?>" class="icon-plus" style="position: absolute; margin: 0; font-size: 22px; font-weight: 700; padding: 0; margin-top: -31px; right: 16px; cursor: pointer;"><i class="fa fa-plus"></i></p>
							<div id="display_advance-<?php echo $checkboxPrint->id; ?>">
							    <p style="margin: 0; padding: 20px 5px 3px;"><?php echo $checkboxPrint->description; ?></p>
							</div>
							<script type="text/javascript">
								jQuery(document).ready(function () {
									jQuery('#display_advance-<?php echo $checkboxPrint->id; ?>').slideUp('50');

								    jQuery('#click_advance-<?php echo $checkboxPrint->id; ?>').click(function() {
								    	jQuery('#display_advance-<?php echo $checkboxPrint->id; ?>').toggle('1000');
								    	jQuery("i", this).toggleClass("fa fa-minus fa fa-plus");
								    });
								});
							</script>
						<?php } ?>
					</div>
				</div>
			<?php }
			if (isset($current_user_id) && !empty($current_user_id)) {
				$checkboxUserResult = $wpdb->get_results("SELECT * FROM $table_name2 WHERE uid = '$current_user_id'");
				echo "<input type='hidden' id='hiddenUserId' name='hiddenUserId' value='$current_user_id'>";
				if ($wpdb->num_rows > 0) {
					echo "<button class='btn btn-success btn-sm' id='updateUserCheckboxSubmit' name='updateUserCheckboxSubmit' type='submit' style='margin-top: 1.5rem; margin-bottom: 1rem;'>Update List</button></td>";
				}
				else {
					echo "<button class='btn btn-primary btn-sm' id='newUserCheckboxSubmit' name='newUserCheckboxSubmit' type='submit' style='margin-top: 1.5rem; margin-bottom: 1rem;'>Add List</button></td>";
				}
			}
			else {
				echo "<a class='btn btn-primary btn-sm' style='margin-top: 1.5rem; margin-bottom: 1rem;' href='".esc_url(wp_login_url(get_permalink()))."'/wp-login.php?destination=$current_url'>Login to Add List</a></td>";
			}
		echo "</form>
	</div>";

	$result = ob_get_clean(); //capture the buffer into $result
    return $result; // return it, instead of echoing
}