<?php

require_once( plugin_dir_path( __FILE__ ) .'/../twilio/Twilio/autoload.php');
use Twilio\Rest\Client;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://giannisftaras.dev/
 * @since      1.0.0
 *
 * @package    Rtwilio
 * @subpackage Rtwilio/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rtwilio
 * @subpackage Rtwilio/public
 * @author     Giannis Ftaras <giannis@readnet.gr>
 */
class Rtwilio_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'woocommerce_review_order_before_submit', [ $this, 'send_sms_checkbox' ]);
		add_action( 'woocommerce_thankyou', [ $this, 'check_order_for_sms' ], 10, 1);
		add_action('woocommerce_checkout_update_order_meta', [ $this, 'chksms_checkout_order_meta']);

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rtwilio-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rtwilio-public.js', array( 'jquery' ), $this->version, false );

	}

	function send_sms_checkbox() {
		echo '<div class="cw_custom_class">';
		woocommerce_form_field( 'sms_checkbox', array(
			'type'          => 'checkbox',
			'label'         => __('Αποστολή SMS για την εξέλιξη της παραγγελίας μου'),
			'required'  => false,
		), WC()->checkout->get_value( 'sms_checkbox' ));
		echo '</div>';
		}
	
		function chksms_checkout_order_meta( $order_id ) {
			if ($_POST['sms_checkbox']) update_post_meta( $order_id, 'send_sms', esc_attr($_POST['sms_checkbox']));
		}
	
	
		function check_order_for_sms($order_id) {
	
			if ( ! $order_id ) {
				return;
			}
			$order = wc_get_order( $order_id );
			$phone_checked = false;
	
			//Phone number validation
			$phone = $order->get_billing_phone();
			$phone = str_replace(str_split('-+:*?"<>| '), '', $phone);
			if (strlen($phone) == 12) {
				$phone = substr($phone, 2);
			}
			if( substr( $phone, 0, 2 ) === "69" && preg_match("/^[0-9]{10}$/", $phone)) {
			  $phone_checked = true;
			}
	
			if ($phone_checked && $order->get_meta($key = 'send_sms') == 1) {
				$this->send_twil_sms($order->get_status(), $order->get_id(), $phone);
			} elseif (! $phone_checked && $order->get_meta($key = 'send_sms') == 1) {
				echo '<h2 class="h2thanks">Χωρίς SMS :(</h2><p class="pthanks">Δυστυχώς το τηλέφωνο που καταχωρήσατε δεν είναι συμβατό για την αποστολή SMS της παραγγελίας σας!</p>';
			}
	
		}
	
		function send_twil_sms($status, $order_id, $phone) {
			$api_details = get_option('rTwilio');
	
			if ($status == 'completed') {
				$sid = $api_details['api_sid'];
				$token = $api_details['api_auth_token'];
				$client = new Client($sid, $token);
				$phone = '+30' . $phone;
				$message = "Η παραγγελία σας με αριθμό " . $order_id . " έχει καταχωρθεί επιτυχώς! Μπορείτε να κατεβάσετε τα βιβλία σας εδώ: https://www.alearning.gr/my-account/downloads/";
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
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}
	
		}

}
