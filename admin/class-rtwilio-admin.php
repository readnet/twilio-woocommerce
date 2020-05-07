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
	    add_settings_section('rtwilio_main', 'Ρυθμίσεις ενσωμάτωσης Twilio SMS', array($this, 'rtwilio_section_text'), 'rtwilio-settings-page');
	    add_settings_field('api_sid', 'API SID', array($this, 'rtwilio_setting_sid'), 'rtwilio-settings-page', 'rtwilio_main');
		add_settings_field('api_auth_token', 'Auth Token', array($this, 'rtwilio_setting_token'), 'rtwilio-settings-page', 'rtwilio_main');
		add_settings_field('sender_phone', 'Αριθμός αποστολέα', array($this, 'rtwilio_setting_phone'), 'rtwilio-settings-page', 'rtwilio_main');
		add_settings_field('download_link', 'Σύνδεσμος λήψης', array($this, 'rtwilio_setting_download_link'), 'rtwilio-settings-page', 'rtwilio_main');

		add_settings_section('rtwilio_messages', 'Μηνύματα αποστολής', array($this, 'rtwilio_section_messages'), 'rtwilio-settings-page');
		add_settings_field('order_completed_message', 'Ολοκληρωμένη:', array($this, 'rtwilio_setting_order_completed_message'), 'rtwilio-settings-page', 'rtwilio_messages');

		add_settings_section('rtwilio_information', 'Πληροφορίες', array($this, 'rtwilio_section_information'), 'rtwilio-settings-page');
		
	}

	/**
	 * Displays the settings sub header
	 *
	 */
	public function rtwilio_section_text() {
		echo '<p class="rt-desk">Γενικές ρυθμίσεις σχετικά με το API.</p>';
	}

	/**
	 * Displays the messages sub header
	 *
	 */
	public function rtwilio_section_messages() {
	    echo '<p class="rt-desk">Ορίστε τα μηνύματα που θα αποστέλλονται κατά την ολοκλήρωση της παραγγελίας από τον πελάτη.</p>';
	}

	/**
	 * Displays the information sub header
	 *
	 */
	public function rtwilio_section_information() {
		echo '<p class="rt-desk">Μπορείτε να ορίσετε shortcodes στα μηνύματα αποστολής για τη σωστή σύνταξη τους:</p>';
		$shorts = <<<SCT
			<ul>
				<li><code>[order_id]</code> = Αριθμός παραγγελίας του πελάτη</li>
				<li><code>[customer_firstname]</code> = Το όνομα του πελάτη</li>
				<li><code>[customer_lastname]</code> = Το επώνυμο του πελάτη</li>
				<li><code>[order_date]</code> = Ημερομηνία καταχώρησης παραγγελίας</li>
				<li><code>[download_link]</code> = Ο σύνδεσμος για τη λήψη των βιβλίων</li>
			</ul>
		SCT;
		echo $shorts;
	}

	/**
	 * Renders the sid input field
	 *
	 */
	public function rtwilio_setting_sid() {
	   $options = get_option($this->plugin_name);
	   echo "<input id='plugin_text_string' autocomplete='off' spellcheck='false' name='$this->plugin_name[api_sid]' size='40' type='text' value='{$options['api_sid']}' />";
	}

	/**
	 * Renders the auth_token input field
	 *
	 */
	public function rtwilio_setting_token() {
	   $options = get_option($this->plugin_name);
	   echo "<input id='plugin_text_string' type='password' autocomplete='off' spellcheck='false' name='$this->plugin_name[api_auth_token]' size='40' type='text' value='{$options['api_auth_token']}' />";
	}

	/**
	 * Renders the sender_phone input field
	 *
	 */
	public function rtwilio_setting_phone() {
		$options = get_option($this->plugin_name);
		echo "<input id='plugin_text_string' name='$this->plugin_name[sender_phone]' size='40' type='text' value='{$options['sender_phone']}' />";
		echo "<p class='description'>Ο αριθμός τηλεφώνου από τον οποίο θα γίνονται οι αποστολές των SMS.</p>";
	 }

	 /**
	 * Renders the order_completed_message input field
	 *
	 */
	public function rtwilio_setting_order_completed_message() {
		$options = get_option($this->plugin_name);
		echo "<textarea class='rt-message-textarea' id='plugin_text_string' name='$this->plugin_name[order_completed_message]' rows='3' cols='20' type='textarea'>{$options['order_completed_message']}</textarea>";
		echo "<p class='description'>Το μήνυμα που θα αποστέλλεται για τις ολοκληρωμένες παραγγελίες.</p>";
	 }

	 /**
	 * Renders the download_link input field
	 *
	 */
	public function rtwilio_setting_download_link() {
		$options = get_option($this->plugin_name);
		echo "<input id='plugin_text_string' name='$this->plugin_name[download_link]' size='60' type='text' value='{$options['download_link']}' />";
		echo "<p class='description'>Ο σύνδεσμος που παραπέμπει στη σελίδα των λήψεων για τις παραγγελίες.</p>";
	 }

	/**
	 * Sanitises all input fields.
	 *
	 */
	public function plugin_options_validate($input) {
	    $newinput['api_sid'] = trim($input['api_sid']);
		$newinput['api_auth_token'] = trim($input['api_auth_token']);
		$newinput['sender_phone'] = trim($input['sender_phone']);
		$newinput['order_completed_message'] = trim($input['order_completed_message']);
		$newinput['download_link'] = trim($input['download_link']);
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
