<?php

/**
 * # WooCommerce Tmsm WooCommerce DialogInsight Actions
 *
 * @since 1.0
 */

class Tmsm_WooCommerce_DialogInsight_Actions {

	/**
	 * Constructor
	 */
	public function __construct() {

	}

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
    private static $instance;

	/**
     * Get the class instance
	 *
	 * @return Tmsm_WooCommerce_DialogInsight_Actions
	 */
    public static function get_instance() {
        return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
    }

	/**
	 * Localisation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'tmsm-woocommerce-dialoginsight', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Display Checkbox in Billing Form
	 */
	public function apply_checkbox(){

		$integration = new Tmsm_WooCommerce_DialogInsight_Integration();
		$checkbox_label = esc_html($integration->get_option( 'checkbox_label', __( 'Subscribe to our newsletter', 'tmsm-woocommerce-dialoginsight' ) ));

		$checkbox = '<p class="form-row form-row-wide tmsm-woocommerce-dialoginsight-optin">';
		$checkbox .= '<label for="tmsm_woocommerce_dialoginsight_optin" class="woocommerce-form__label woocommerce-form__label-for-checkbox inline">';
		$checkbox .= '<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="tmsm_woocommerce_dialoginsight_optin" type="checkbox" name="tmsm_woocommerce_dialoginsight_optin" value="1"> ';
		$checkbox .= '<span>'. $checkbox_label . '</span>';
		$checkbox .= '</label>';
		$checkbox .= '</p>';

		echo $checkbox;
	}

	/**
	 * Process Checkbox in Billing Form
	 *
	 * @param int $order_id
	 * @param array $posted
	 * @param WC_Order $order
	 */
	public function process_checkbox( $order_id, $posted, $order ) {
		error_log('process_checkbox');

		$status = isset( $_POST['tmsm_woocommerce_dialoginsight_optin'] ) ? (int) $_POST['tmsm_woocommerce_dialoginsight_optin'] : 0;

		if ( ! empty( $order_id ) ) {
			update_post_meta( $order_id, 'tmsm_woocommerce_dialoginsight_optin', $status );
		}
	}

	/**
	* Gets the absolute plugin path without a trailing slash, e.g.
	* /path/to/wp-content/plugins/plugin-directory
	*
	* @return string plugin path
	*/
	public function get_plugin_path() {
		return $this->plugin_path = untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}
}
