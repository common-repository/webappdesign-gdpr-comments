<?php
/*
Plugin Name: GDPR Comments
Plugin URI: https://github.com/juanmacivico87/webappdesign-gdpr-comments
Description: This plugin allow you to add a checkbox to accept Privacy Policy before add a comment.
Version: 1.2
Author: Juan Manuel Civico Cabrera
Author URI: http://webappdesign.es
License: GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  webappdesign-gdpr-comments
Domain Path:  /languages
*/

if (!defined('ABSPATH'))
	exit;

if (!defined('GDPR_COMMENTS_TEXTDOMAIN'))
	define('GDPR_COMMENTS_TEXTDOMAIN', 'webappdesign-gdpr-comments');

function webappdesign_gdpr_comments_wordpress_install()
{
	if (!current_user_can('activate_plugins'))
		wp_die(__('Don\'t have enough permissions to install this plugin.', GDPR_COMMENTS_TEXTDOMAIN) . '<br /><a href="' . admin_url('plugins.php') . '">&laquo; ' . __('Back to plugins page.', GDPR_COMMENTS_TEXTDOMAIN) . '</a>');
}
register_activation_hook( __FILE__, "webappdesign_gdpr_comments_wordpress_install");

function webappdesign_gdpr_comments_wordpress_uninstall()
{
	if (current_user_can('activate_plugins'))
	{
		if (get_option('_gdpr_comments_responsible')) delete_option('_gdpr_comments_responsible');
		if (get_option('_gdpr_comments_purpose')) delete_option('_gdpr_comments_purpose');
		if (get_option('_gdpr_comments_legitimation')) delete_option('_gdpr_comments_legitimation');
		if (get_option('_gdpr_comments_addressee')) delete_option('_gdpr_comments_addressee');
		if (get_option('_gdpr_comments_rights')) delete_option('_gdpr_comments_rights');
		if (get_option('_gdpr_comments_privacy_link')) delete_option('_gdpr_comments_privacy_link');
	}
	else
		wp_die(__('Don\'t have enough permissions to uninstall this plugin.', GDPR_COMMENTS_TEXTDOMAIN) . '<br /><a href="' . admin_url('plugins.php') . '">&laquo; ' . __('Back to plugins page.', GDPR_COMMENTS_TEXTDOMAIN) . '</a>');
}
register_uninstall_hook( __FILE__, 'webappdesign_gdpr_comments_wordpress_uninstall');

//Eliminar la tabla personalizada y meter los datos en la tabla options de WordPress.
//Remove custom table and save data into WordPress options table.
add_action('plugins_loaded', 'webappdesign_move_config');
function webappdesign_move_config()
{
	global $wpdb;
	if ($wpdb -> get_var('SHOW TABLES LIKE "webappdesign_gdpr_comments_wordpress"') === 'webappdesign_gdpr_comments_wordpress') {
		$data = $wpdb -> get_results('select * from webappdesign_gdpr_comments_wordpress');
		update_option('_gdpr_comments_responsible', $data[0] -> responsible);
		update_option('_gdpr_comments_purpose', $data[0] -> purpose);
		update_option('_gdpr_comments_legitimation', $data[0] -> legitimation);
		update_option('_gdpr_comments_addressee', $data[0] -> addressee);
		update_option('_gdpr_comments_rights', $data[0] -> rights);
		update_option('_gdpr_comments_privacy_link', $data[0] -> privacy_link);
	}
	$sql = "drop table if exists webappdesign_gdpr_comments_wordpress;";
	$wpdb -> query($sql);
}

//Muestro el checkbox para aceptar la política de privacidad debajo del formulario.
//Show the checkbox to accept Privacy Policy under the form.
add_filter('comment_form_field_comment', 'webappdesign_add_checkbox_wordpress_comments');
function webappdesign_add_checkbox_wordpress_comments($comment)
{
	$privacy_link = get_option('_gdpr_comments_privacy_link') ? get_option('_gdpr_comments_privacy_link') : '';

	$link = sprintf(__('I have read and accept the <a href="%s" target="_blank" rel="noopener noreferrer">Privacy Policy</a>', GDPR_COMMENTS_TEXTDOMAIN), esc_url(get_site_url() . $privacy_link));

	return $comment . '<p align="left"><input type="checkbox" id="webappdesign_accept_gdpr_comments" name="webappdesign_accept_gdpr_comments"> ' . $link . '</p>';
}

//Valido el checkbox para aceptar la política de privacidad.
//Check the checkbox to accept the Privacy Policy.
if (!is_admin())
	add_filter('preprocess_comment', 'webappdesign_check_accept_gdpr_comment');
function webappdesign_check_accept_gdpr_comment($userComment)
{
	isset ($_POST['webappdesign_accept_gdpr_comments']) or wp_die (__('Please, accept the Privacy Policy to continue.', GDPR_COMMENTS_TEXTDOMAIN) . '<br /><a href="' . esc_url(get_site_url()) . '" onClick="window.history.go(-1); return false;">&laquo;' . __('Back to the post', GDPR_COMMENTS_TEXTDOMAIN) . '</a>');
	return $userComment;
}

//Muestro la primera capa de información a la que obliga el RGPD.
//Show the first information layer.
add_action('comment_form', 'webappdesign_show_gdpr_layer_information_comments');
function webappdesign_show_gdpr_layer_information_comments()
{
	$gdpr_data = webappdesign_get_gdpr_data();
	?>
		<h3><?php esc_attr_e('Basic information about Data Protection', GDPR_COMMENTS_TEXTDOMAIN); ?></h3>
		<ul>
			<?php echo sprintf(__('<li><b>Responsible:</b> %s</li>', GDPR_COMMENTS_TEXTDOMAIN), $gdpr_data['responsible']);
			echo sprintf(__('<li><b>Purpose:</b> %s</li>', GDPR_COMMENTS_TEXTDOMAIN), $gdpr_data['purpose']);
			echo sprintf(__('<li><b>Legitimation:</b> %s</li>', GDPR_COMMENTS_TEXTDOMAIN), $gdpr_data['legitimation']);
			echo sprintf(__('<li><b>Adressee:</b> %s</li>', GDPR_COMMENTS_TEXTDOMAIN), $gdpr_data['addressee']);
			echo sprintf(__('<li><b>Rights:</b> %s</li>', GDPR_COMMENTS_TEXTDOMAIN), $gdpr_data['rights']);
			echo sprintf(__('<li><b>More information:</b> See more information about Data Protection in the next link: <a href="%s" target="_blank" rel="noopener noreferrer">Privacy Policy</a></li>', GDPR_COMMENTS_TEXTDOMAIN), esc_url(get_site_url() . $gdpr_data['privacy_link'])); ?>
		</ul>
	<?php
}

//Devolver las opciones configuradas del plugin.
//Return plugin config options.
function webappdesign_get_gdpr_data()
{
	$gdpr_data = array();
	$gdpr_data['responsible']  = get_option('_gdpr_comments_responsible') ? get_option('_gdpr_comments_responsible') : '';
	$gdpr_data['purpose'] 	   = get_option('_gdpr_comments_purpose') ? get_option('_gdpr_comments_purpose') : '';
	$gdpr_data['legitimation'] = get_option('_gdpr_comments_legitimation') ? get_option('_gdpr_comments_legitimation') : '';
	$gdpr_data['addressee']    = get_option('_gdpr_comments_addressee') ? get_option('_gdpr_comments_addressee') : '';
	$gdpr_data['rights'] 	   = get_option('_gdpr_comments_rights') ? get_option('_gdpr_comments_rights') : '';
	$gdpr_data['privacy_link'] = get_option('_gdpr_comments_privacy_link') ? get_option('_gdpr_comments_privacy_link') : '';
	return $gdpr_data;
}

//Incluir el menú de las opciones del plugin.
//Include plugin option in menu.
function webappdesign_menu_gdpr_comments_wordpress()
{
	add_menu_page('GDPR Comments', 'GDPR Comments', 'administrator', 'webappdesign_menu_gdpr_comments_wordpress', 'webappdesign_options_gdpr_comments_wordpress');
}
add_action('admin_menu', 'webappdesign_menu_gdpr_comments_wordpress');

//Muestro la pantalla con las opciones del plugin.
//Show plugin option screen.
function webappdesign_options_gdpr_comments_wordpress()
{
	if (isset($_POST['save_gdpr_commets_wordpress_config']))
	{
		if ((!isset($_POST['webappdesign_gdpr_comments_nonce'])) || (!wp_verify_nonce($_POST['webappdesign_gdpr_comments_nonce'], 'submit_data')))
			wp_die(__('This isn\'t the right path.'));
		if (isset($_POST['responsible'])) {
			$responsible = sanitize_text_field($_POST['responsible']);
			update_option('_gdpr_comments_responsible', $responsible);
		}
		if (isset($_POST['purpose'])) {
			$purpose = sanitize_text_field($_POST['purpose']);
			update_option('_gdpr_comments_purpose', $purpose);
		}
		if (isset($_POST['legitimation'])) {
			$legitimation = sanitize_text_field($_POST['legitimation']);
			update_option('_gdpr_comments_legitimation', $legitimation);
		}
		if (isset($_POST['addressee'])) {
			$addressee = sanitize_text_field($_POST['addressee']);
			update_option('_gdpr_comments_addressee', $addressee);
		}
		if (isset($_POST['rights'])) {
			$rights = sanitize_text_field($_POST['rights']);
			update_option('_gdpr_comments_rights', $rights);
		}
		if (isset($_POST['privacy_link'])) {
			$privacy_link = sanitize_text_field($_POST['privacy_link']);
			update_option('_gdpr_comments_privacy_link', $privacy_link);
		}
	}

	$gdpr_data = webappdesign_get_gdpr_data();

	?><div id="webappdesign_fields_gdpr_comments_wordpress">
		<h2><?php esc_attr_e('Basic information about Data Protection', GDPR_COMMENTS_TEXTDOMAIN); ?></h2>
		<form action="" method="post">
			<?php wp_nonce_field('submit_data', 'webappdesign_gdpr_comments_nonce'); ?>
			<label for="responsible"><b><?php esc_attr_e('Responsible (Name of the responsible of the data processing)', GDPR_COMMENTS_TEXTDOMAIN); ?></b></label><br>
			<input type="text" id="responsible" name="responsible" value="<?php echo esc_attr($gdpr_data['responsible']); ?>" size="100%" maxlength="50"><br>
			<label for="purpose"><b><?php esc_attr_e('Purpose (for wich the data is collected)', GDPR_COMMENTS_TEXTDOMAIN); ?></b></label><br>
			<input type="text" id="purpose" name="purpose" value="<?php echo esc_attr($gdpr_data['purpose']); ?>" size="100%"><br>
			<label for="legitimation"><b><?php esc_attr_e('Legitimation (Legal basis to collect user data)', GDPR_COMMENTS_TEXTDOMAIN); ?></b></label><br>
			<input type="text" id="legitimation" name="legitimation" value="<?php echo esc_attr($gdpr_data['legitimation']); ?>" size="100%"><br>
			<label for="addressee"><b><?php esc_attr_e('Addressee (Recipient to whom the data can be transferred)', GDPR_COMMENTS_TEXTDOMAIN); ?></b></label><br>
			<input type="text" id="addressee" name="addressee" value="<?php echo esc_attr($gdpr_data['addressee']); ?>" size="100%"><br>
			<label for="rights"><b><?php esc_attr_e('Rights (that the user can exercise)', GDPR_COMMENTS_TEXTDOMAIN); ?></b></label><br>
			<input type="text" id="rights" name="rights" value="<?php echo esc_attr($gdpr_data['rights']); ?>" size="100%"><br>
			<label for="privacy_link"><b><?php esc_attr_e('Privacy Link (Link to your Privacy Policy page. Example: "https://www.yourwebsite.com/privacy-policy")', GDPR_COMMENTS_TEXTDOMAIN); ?></b></label><br>
			<?php echo esc_url(get_site_url()); ?><input type="text" id="privacy_link" name="privacy_link" value="<?php echo esc_attr($gdpr_data['privacy_link']); ?>" size="75%"><br><br>
			<input type="submit" id="save_gdpr_commets_wordpress_config" name="save_gdpr_commets_wordpress_config" value="<?php esc_attr_e('Save config', GDPR_COMMENTS_TEXTDOMAIN); ?>">
		</form>
	</div><?php
}
