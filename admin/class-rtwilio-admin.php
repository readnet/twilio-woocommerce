<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://giannisftaras.dev/
 * @since      1.0.0
 *
 * @package    Rtwilio
 * @subpackage Rtwilio/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rtwilio
 * @subpackage Rtwilio/admin
 * @author     Giannis Ftaras <giannis@readnet.gr>
 */

require_once( plugin_dir_path( __FILE__ ) .'/../twilio/Twilio/autoload.php');
use Twilio\Rest\Client;
		
add_action( 'wp_ajax_send_sms', 'send_sms' );
function send_sms() {
	global $wpdb;

	$api_details = get_option('rTwilio');
	$sid = $api_details['api_sid'];
	$token = $api_details['api_auth_token'];
	$client = new Client($sid, $token);
	$phone = '+30' . $_POST['phone'];
	$message = $_POST['sms'];

	try {
		$client->messages->create(
			// the number you'd like to send the message to
			$phone,
			[
				// A Twilio phone number you purchased at twilio.com/console
				'from' => '+15005550006',
				// the body of the text message you'd like to send
				'body' => $message
			]
		);		
		set_transient( 'send_sms_transient', 'Το μήνυμα (SMS) στάλθηκε επιτυχώς!', 60 );
	} catch (Exception $e) {
		set_transient( 'send_sms_transient', $e->getMessage(), 60 );		
	}

	wp_die();
}

class Rtwilio_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rtwilio_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rtwilio_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rtwilio-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rtwilio_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rtwilio_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rtwilio-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
 *  Register the administration menu for this plugin into the WordPress Dashboard
 * @since    1.0.0
 */

public function add_rtwilio_admin_setting() {

    /*
     * Add a settings page for this plugin to the Settings menu.
     *
     * Administration Menus: http://codex.wordpress.org/Administration_Menus
     *
     */
    add_options_page( 'Twilio SMS Intergration', 'rTwilio API', 'manage_options', $this->plugin_name, array($this, 'display_rtwilio_settings_page'));
	add_action( 'add_meta_boxes', [$this, 'add_sms_meta_boxes'] );
	add_action( 'admin_notices', [$this, 'send_sms_admin_notice'] );


	}

	/**
	 * Render the settings page for this plugin.( The html file )
	 *
	 * @since    1.0.0
	 */

	public function display_rtwilio_settings_page() {
	    include_once( 'partials/rtwilio-admin-display.php' );
	}

	/**
	 * Registers and Defines the necessary fields we need.
	 *
	*/
	public function rtwilio_admin_settings_save(){

	    register_setting( $this->plugin_name, $this->plugin_name, array($this, 'plugin_options_validate') );
	    add_settings_section('rtwilio_main', 'Twilio SMS Integration settings', array($this, 'rtwilio_section_text'), 'rtwilio-settings-page');
	    add_settings_field('api_sid', 'Twilio SID API', array($this, 'rtwilio_setting_sid'), 'rtwilio-settings-page', 'rtwilio_main');
	    add_settings_field('api_auth_token', 'Auth Token', array($this, 'rtwilio_setting_token'), 'rtwilio-settings-page', 'rtwilio_main');
	}

	/**
	 * Displays the settings sub header
	 *
	 */
	public function rtwilio_section_text() {
	    echo '<h3>Edit Twilio API details</h3>';
	}

	/**
	 * Renders the sid input field
	 *
	 */
	public function rtwilio_setting_sid() {
	   $options = get_option($this->plugin_name);
	   echo "<input id='plugin_text_string' name='$this->plugin_name[api_sid]' size='40' type='text' value='{$options['api_sid']}' />";
	}

	/**
	 * Renders the auth_token input field
	 *
	 */
	public function rtwilio_setting_token() {
	   $options = get_option($this->plugin_name);
	   echo "<input id='plugin_text_string' name='$this->plugin_name[api_auth_token]' size='40' type='text' value='{$options['api_auth_token']}' />";
	}

	/**
	 * Sanitises all input fields.
	 *
	 */
	public function plugin_options_validate($input) {
	    $newinput['api_sid'] = trim($input['api_sid']);
	    $newinput['api_auth_token'] = trim($input['api_auth_token']);

	    return $newinput;
	}

	function add_sms_meta_boxes() {
        add_meta_box( 'sms_other_fields', __('Αποστολή SMS','woocommerce'), [$this, 'sms_add_other_fields'], 'shop_order', 'side', 'core' );
  }

	function sms_add_other_fields() {
        global $post;
        $meta_field_data = get_post_meta( $post->ID, 'send_sms', true ) ? get_post_meta( $post->ID, 'send_sms', true ) : '';
				if ($meta_field_data == "") {
					echo '<p><em>Ο πελάτης δεν έχει επιλέξει την αποστολή SMS.</em></p>';
					return;
				}

				echo '<p>
					<label for="send_customer_sms">Αποστολή SMS στον πελάτη</label>
					<textarea type="text" name="order_sms_text" id="send_customer_sms" class="input-text sms-textarea" cols="20" rows="5" maxlength="159"></textarea>
				</p>
				<div class="sms-sub-par">					
					<button type="button" class="button send-sms-button" name="send_order_sms" value="Αποστολή">Αποστολή</button>
					<span id="sms-spinner" class="spinner"></span>
				</div>';
	}

	function send_sms_admin_notice(){
		if( get_transient( 'send_sms_transient' ) ) {
			$transclass = "notice-error";
			if ( get_transient( 'send_sms_transient' ) == "Το μήνυμα (SMS) στάλθηκε επιτυχώς!") {
				$transclass = "notice-success";
			}
			?>
			<div class="notice <?php echo $transclass; ?> is-dismissible">
				<p><?php echo get_transient( 'send_sms_transient' ) ?></p>
			</div>
			<?php			
			delete_transient( 'send_sms_transient' );
		}
	}

}
